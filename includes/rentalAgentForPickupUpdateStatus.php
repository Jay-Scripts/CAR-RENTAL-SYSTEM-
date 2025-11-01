<?php
include "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';
    $car_status_note = $_POST['car_status_note'] ?? null; // grab note from SweetAlert

    if ($booking_id > 0 && $new_status === 'ONGOING') {
        try {
            $conn->beginTransaction();

            // Update booking status and optional car note
            $stmt = $conn->prepare("
                UPDATE CUSTOMER_BOOKING_DETAILS 
                SET STATUS = ?, CAR_STATUS_NOTE = ? 
                WHERE BOOKING_ID = ?
            ");
            $stmt->execute([$new_status, $car_status_note, $booking_id]);

            // Get CAR_ID from booking
            $stmtCar = $conn->prepare("SELECT CAR_ID FROM CUSTOMER_BOOKING_DETAILS WHERE BOOKING_ID = ?");
            $stmtCar->execute([$booking_id]);
            $car = $stmtCar->fetch(PDO::FETCH_ASSOC);

            if ($car && isset($car['CAR_ID'])) {
                $car_id = $car['CAR_ID'];

                // Update CAR_DETAILS to Rented
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
