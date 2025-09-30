<?php
include "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';

    if ($booking_id > 0 && $new_status === 'ONGOING') {
        try {
            $stmt = $conn->prepare("UPDATE CUSTOMER_BOOKING_DETAILS SET STATUS = ? WHERE BOOKING_ID = ?");
            $stmt->execute([$new_status, $booking_id]);
            echo "success";
        } catch (PDOException $e) {
            echo "DB Error: " . $e->getMessage();
        }
    } else {
        echo "Invalid request";
    }
}
