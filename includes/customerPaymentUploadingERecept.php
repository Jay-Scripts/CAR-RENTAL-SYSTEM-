<?php
include "../config/db.php";
require '../vendor/autoload.php'; // include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);

    if ($booking_id <= 0 || !isset($_FILES['receipt'])) {
        echo "Invalid request";
        exit;
    }

    // File details
    $fileTmp  = $_FILES['receipt']['tmp_name'];
    $fileName = basename($_FILES['receipt']['name']);
    $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileExt, $allowedExt)) {
        echo "Invalid file type. Only JPG, JPEG, PNG, GIF allowed.";
        exit;
    }

    // Generate new filename
    $newFileName = "receipt_" . $booking_id . "_" . time() . "." . $fileExt;
    $uploadDir   = __DIR__ . "/../src/images/e-receiptsFolder/";
    $uploadPath  = $uploadDir . $newFileName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        $receiptPath = "../src/images/e-receiptsFolder/" . $newFileName;

        try {
            $conn->beginTransaction();

            // 1️ Insert payment record
            $sql = "INSERT INTO BOOKING_PAYMENT_DETAILS (RECEIPT_PATH, BOOKING_ID) 
                    VALUES (:receipt, :booking_id)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':receipt'    => $receiptPath,
                ':booking_id' => $booking_id
            ]);

            // 2️ Update booking status
            $sql2 = "UPDATE CUSTOMER_BOOKING_DETAILS 
                     SET STATUS = 'FOR VERIFICATION'
                     WHERE BOOKING_ID = :booking_id";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute([':booking_id' => $booking_id]);

            // 3️ Fetch customer email
            $queryUser = $conn->prepare("
                SELECT u.EMAIL, u.FIRST_NAME 
                FROM CUSTOMER_BOOKING_DETAILS cbd
                JOIN USER_DETAILS u ON cbd.USER_ID = u.USER_ID
                WHERE cbd.BOOKING_ID = :booking_id
            ");
            $queryUser->execute([':booking_id' => $booking_id]);
            $user = $queryUser->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // 4️ Send email
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'carrentaljemjem@gmail.com';
                    $mail->Password   = 'yaiu cwqf vehd uwtg'; // Gmail app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('carrentaljemjem@gmail.com', 'Car Rental System');
                    $mail->addAddress($user['EMAIL'], $user['FIRST_NAME']);
                    $mail->isHTML(true);
                    $mail->Subject = "Booking ID : #$booking_id Payment Submitted for Verification";

                    $mail->Body = "
                        <p>Dear {$user['FIRST_NAME']},</p>
                        <p>Your payment receipt for <b>Booking ID: $booking_id</b> has been successfully submitted.</p>
                        <p>Your booking status is now set to <b>FOR VERIFICATION</b>. Our team will review your payment shortly.</p>
                        <p>We’ll notify you once your booking has been approved or if additional details are required.</p>
                        <br>
                        <p>Thank you for choosing <b>Car Rental System</b>! 🚗</p>
                        <p>Best regards,<br><b>Car Rental System Team</b></p>
                    ";

                    $mail->send();
                } catch (Exception $e) {
                    error_log("Mail error: " . $mail->ErrorInfo);
                }
            }

            $conn->commit();
            echo "success";
        } catch (PDOException $e) {
            $conn->rollBack();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "File upload failed";
    }
}
