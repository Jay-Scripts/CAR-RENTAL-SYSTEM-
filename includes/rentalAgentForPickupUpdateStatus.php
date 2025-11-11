<?php
include "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';
    $car_status_note = $_POST['car_status_note'] ?? null;

    if ($booking_id > 0 && $new_status === 'ONGOING') {
        try {
            $conn->beginTransaction();

            // 🖼️ Handle optional image upload
            $car_status_image = null;
            if (!empty($_FILES['car_status_image']['name'])) {
                $uploadDir = "../uploads/car_status/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = uniqid('status_') . "_" . basename($_FILES['car_status_image']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['car_status_image']['tmp_name'], $targetPath)) {
                    $car_status_image = "uploads/car_status/" . $fileName; // save relative path
                }
            }

            // 📝 Update booking details (status, note, image)
            $stmt = $conn->prepare("
                UPDATE CUSTOMER_BOOKING_DETAILS 
                SET STATUS = ?, CAR_STATUS_NOTE = ?, CAR_STATUS_IMAGE = ?
                WHERE BOOKING_ID = ?
            ");
            $stmt->execute([$new_status, $car_status_note, $car_status_image, $booking_id]);

            // 🚗 Get CAR_ID and update car status
            $stmtCar = $conn->prepare("SELECT CAR_ID FROM CUSTOMER_BOOKING_DETAILS WHERE BOOKING_ID = ?");
            $stmtCar->execute([$booking_id]);
            $car = $stmtCar->fetch(PDO::FETCH_ASSOC);

            if ($car && isset($car['CAR_ID'])) {
                $car_id = $car['CAR_ID'];
                $stmtUpdateCar = $conn->prepare("UPDATE CAR_DETAILS SET STATUS = 'RENTED' WHERE CAR_ID = ?");
                $stmtUpdateCar->execute([$car_id]);
            }

            $conn->commit();
            echo "success";
        } catch (PDOException $e) {
            $conn->rollBack();
            echo "DB Error: " . $e->getMessage();
        }
    } else {
        echo "Invalid request";
    }
}
