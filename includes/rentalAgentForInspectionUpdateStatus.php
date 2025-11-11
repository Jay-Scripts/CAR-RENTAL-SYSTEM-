<?php
require_once '../config/db.php';
session_start();
require '../vendor/autoload.php'; // PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $notes      = trim($_POST['notes'] ?? '');
    $penalty    = floatval($_POST['penalty'] ?? 0);
    $penalty    = max($penalty, 0);
    $user_id    = $_SESSION['user_id'] ?? 0; // logged-in rental agent

    if ($booking_id <= 0 || $user_id <= 0) {
        echo "Invalid booking or user";
        exit;
    }

    if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        echo "No images uploaded";
        exit;
    }

    $uploadDir = __DIR__ . "/../src/images/inspection_proof/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $lastUploadedPath = '';
    foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
        if ($_FILES['images']['error'][$index] !== UPLOAD_ERR_OK) continue;
        $fileName = basename($_FILES['images']['name'][$index]);
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) continue;

        $newFileName = "inspection_{$booking_id}_" . time() . "_" . ($index + 1) . "." . $fileExt;
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($tmpName, $uploadPath)) {
            $lastUploadedPath = "../src/images/inspection_proof/" . $newFileName;
        }
    }

    if (!$lastUploadedPath) {
        echo "File upload failed";
        exit;
    }

    try {
        $conn->beginTransaction();

        // 1️⃣ Insert inspection report
        $stmt = $conn->prepare("
            INSERT INTO BOOKING_VEHICLE_INSPECTION 
            (BOOKING_ID, USER_ID, IMAGE_PATH, NOTES, PENALTY, CREATED_AT)
            VALUES (:booking_id, :user_id, :image_path, :notes, :penalty, NOW())
        ");
        $stmt->execute([
            ':booking_id' => $booking_id,
            ':user_id'    => $user_id,
            ':image_path' => $lastUploadedPath,
            ':notes'      => $notes,
            ':penalty'    => $penalty
        ]);

        // 2️⃣ Update car status to MAINTENANCE
        $stmt = $conn->prepare("
            UPDATE CAR_DETAILS c
            INNER JOIN CUSTOMER_BOOKING_DETAILS b ON c.CAR_ID = b.CAR_ID
            SET c.STATUS = 'MAINTENANCE'
            WHERE b.BOOKING_ID = :booking_id
        ");
        $stmt->execute([':booking_id' => $booking_id]);

        // 3️⃣ Get booking details for logging/email
        $stmt = $conn->prepare("
            SELECT cbd.STATUS, cbd.CAR_ID, u.FIRST_NAME, u.LAST_NAME, u.EMAIL, c.CAR_NAME
            FROM CUSTOMER_BOOKING_DETAILS cbd
            JOIN USER_DETAILS u ON cbd.USER_ID = u.USER_ID
            JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
            WHERE cbd.BOOKING_ID = :id FOR UPDATE
        ");
        $stmt->execute([':id' => $booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        $previousStatus = $booking['STATUS'];
        $customerName = $booking['FIRST_NAME'] . ' ' . $booking['LAST_NAME'];
        $customerEmail = $booking['EMAIL'];
        $carModel = $booking['CAR_NAME'];

        // 4️⃣ Update booking & payment based on penalty
        if ($penalty > 0) {
            // Create penalty payment
            $stmt = $conn->prepare("
                INSERT INTO BOOKING_PAYMENT_DETAILS 
                (BOOKING_ID, RECEIPT_PATH, PAYMENT_TYPE, AMOUNT, STATUS, CREATED_AT)
                VALUES (:booking_id, '', 'PENALTY', :amount, 'UNPAID', NOW())
            ");
            $stmt->execute([':booking_id' => $booking_id, ':amount' => $penalty]);

            // Update booking status to CHECKING
            $newStatus = 'CHECKING';
        } else {
            // No penalty → booking completed
            $stmt = $conn->prepare("
                UPDATE CUSTOMER_BOOKING_DETAILS
                SET STATUS = 'COMPLETED'
                WHERE BOOKING_ID = :booking_id
            ");
            $stmt->execute([':booking_id' => $booking_id]);

            // Mark payment as PAID
            $stmt = $conn->prepare("
                UPDATE BOOKING_PAYMENT_DETAILS
                SET STATUS = 'PAID'
                WHERE BOOKING_ID = :booking_id
            ");
            $stmt->execute([':booking_id' => $booking_id]);

            $newStatus = 'COMPLETED';
        }

        // 5️⃣ Insert log
        $stmt = $conn->prepare("
            INSERT INTO BOOKING_STATUS_LOGS
            (BOOKING_ID, PREVIOUS_STATUS, NEW_STATUS, CHANGED_BY, CHANGE_SOURCE, REMARKS)
            VALUES (:booking_id, :prev_status, :new_status, :changed_by, :source, :remarks)
        ");
        $stmt->execute([
            ':booking_id' => $booking_id,
            ':prev_status' => $previousStatus,
            ':new_status' => $newStatus,
            ':changed_by' => $user_id,
            ':source'     => 'USER',
            ':remarks'    => $penalty > 0 ? "Penalty applied, booking needs checking." : "Booking completed successfully."
        ]);

        $conn->commit();

        // 6️⃣ Send email if completed
        if ($newStatus === 'COMPLETED') {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'carrentaljemjem@gmail.com';
                $mail->Password   = 'yaiu cwqf vehd uwtg';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('carrentaljemjem@gmail.com', 'Car Rental System');
                $mail->addAddress($customerEmail, $customerName);

                $mail->isHTML(true);
                $mail->Subject = 'Thank You for Choosing Our Car Rental!';
                $mail->Body = "
                    <p>Dear {$customerName},</p>
                    <p>We have successfully received your returned <strong>{$carModel}</strong>.</p>
                    <p>Thank you for choosing our car rental service. We hope you had a smooth and enjoyable experience.</p>
                    <p>We’d love to hear your feedback! Reply to this message or leave a quick review to help us improve.</p>
                    <p>We look forward to serving you again!</p>
                ";
                $mail->send();
            } catch (Exception $e) {
                error_log("Email error for booking {$booking_id}: {$mail->ErrorInfo}");
            }
        }

        echo "success";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
