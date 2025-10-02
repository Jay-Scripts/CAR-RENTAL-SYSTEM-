<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $notes      = trim($_POST['notes'] ?? '');
    $penalty    = floatval($_POST['penalty'] ?? 0);
    $penalty    = max($penalty, 0);

    if ($booking_id <= 0) {
        echo "Invalid booking ID";
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
            // Store **single string path** like receipt upload
            $lastUploadedPath = "../src/images/inspection_proof/" . $newFileName;
        }
    }

    if (!$lastUploadedPath) {
        echo "File upload failed";
        exit;
    }

    try {
        $conn->beginTransaction();

        // 1️ Insert inspection report
        $stmt = $conn->prepare("
            INSERT INTO BOOKING_VEHICLE_INSPECTION 
            (BOOKING_ID, IMAGE_PATH, NOTES, PENALTY, CREATED_AT)
            VALUES (:booking_id, :image_path, :notes, :penalty, NOW())
        ");
        $stmt->execute([
            ':booking_id' => $booking_id,
            ':image_path' => $lastUploadedPath, // single string
            ':notes'      => $notes,
            ':penalty'    => $penalty
        ]);

        // 2️ Update car status to MAINTENANCE
        $stmt = $conn->prepare("
            UPDATE CAR_DETAILS c
            INNER JOIN CUSTOMER_BOOKING_DETAILS b ON c.CAR_ID = b.CAR_ID
            SET c.STATUS = 'MAINTENANCE'
            WHERE b.BOOKING_ID = :booking_id
        ");
        $stmt->execute([':booking_id' => $booking_id]);

        // 3️ Handle booking status
        if ($penalty > 0) {
            // Create a penalty payment
            $stmt = $conn->prepare("
                INSERT INTO BOOKING_PAYMENT_DETAILS 
                (BOOKING_ID, RECEIPT_PATH, PAYMENT_TYPE, AMOUNT, STATUS, CREATED_AT)
                VALUES (:booking_id, '', 'PENALTY', :amount, 'UNPAID', NOW())
            ");
            $stmt->execute([
                ':booking_id' => $booking_id,
                ':amount'     => $penalty
            ]);

            // Booking remains CHECKING
            $stmt = $conn->prepare("
                UPDATE CUSTOMER_BOOKING_DETAILS
                SET STATUS = 'CHECKING'
                WHERE BOOKING_ID = :booking_id
            ");
            $stmt->execute([':booking_id' => $booking_id]);
        } else {
            // No penalty → mark booking COMPLETED
            $stmt = $conn->prepare("
                UPDATE CUSTOMER_BOOKING_DETAILS
                SET STATUS = 'COMPLETED'
                WHERE BOOKING_ID = :booking_id
            ");
            $stmt->execute([':booking_id' => $booking_id]);
        }

        $conn->commit();
        echo "success";
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
