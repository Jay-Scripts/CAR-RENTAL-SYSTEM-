<?php
include "../config/db.php";

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

    // Allowed image types
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileExt, $allowedExt)) {
        echo "Invalid file type. Only JPG, JPEG, PNG, GIF allowed.";
        exit;
    }

    // Generate new filename
    $newFileName = "receipt_" . $booking_id . "_" . time() . "." . $fileExt;
    $uploadDir   = __DIR__ . "/../../src/images/e-receiptsFolder/"; // safer absolute path
    $uploadPath  = $uploadDir . $newFileName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        // Store relative path (for frontend use)
        $receiptPath = "src/images/e-receiptsFolder/" . $newFileName;

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
