<?php
require_once '../config/db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $notes      = trim($_POST['notes'] ?? '');
    $penalty    = floatval($_POST['penalty'] ?? 0);
    $penalty    = max($penalty, 0);

    // ✅ Get logged-in rental agent USER_ID from session
    $user_id = $_SESSION['USER_ID'] ?? 0;

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

        // ✅ Insert inspection report with USER_ID
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

        // Update car status to MAINTENANCE
        $stmt = $conn->prepare("
            UPDATE CAR_DETAILS c
            INNER JOIN CUSTOMER_BOOKING_DETAILS b ON c.CAR_ID = b.CAR_ID
            SET c.STATUS = 'MAINTENANCE'
            WHERE b.BOOKING_ID = :booking_id
        ");
        $stmt->execute([':booking_id' => $booking_id]);

        // Booking + payment
        if ($penalty > 0) {
            $stmt = $conn->prepare("
                INSERT INTO BOOKING_PAYMENT_DETAILS 
                (BOOKING_ID, RECEIPT_PATH, PAYMENT_TYPE, AMOUNT, STATUS, CREATED_AT)
                VALUES (:booking_id, '', 'PENALTY', :amount, 'UNPAID', NOW())
            ");
            $stmt->execute([
                ':booking_id' => $booking_id,
                ':amount'     => $penalty
            ]);

            $stmt = $conn->prepare("
                UPDATE CUSTOMER_BOOKING_DETAILS
                SET STATUS = 'CHECKING'
                WHERE BOOKING_ID = :booking_id
            ");
            $stmt->execute([':booking_id' => $booking_id]);
        } else {
            $stmt = $conn->prepare("
                UPDATE CUSTOMER_BOOKING_DETAILS
                SET STATUS = 'COMPLETED'
                WHERE BOOKING_ID = :booking_id
            ");
            $stmt->execute([':booking_id' => $booking_id]);

            $stmt = $conn->prepare("
                UPDATE BOOKING_PAYMENT_DETAILS
                SET STATUS = 'PAID'
                WHERE BOOKING_ID = :booking_id
            ");
            $stmt->execute([':booking_id' => $booking_id]);
        }

        $conn->commit();
        echo "success";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
