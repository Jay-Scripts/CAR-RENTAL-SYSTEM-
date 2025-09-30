<?php
include '../config/db.php';

$booking_id = intval($_POST['booking_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($booking_id <= 0 || !in_array($action, ['approve', 'decline'])) {
    echo "Invalid request";
    exit;
}

// Determine booking and car status
if ($action === 'approve') {
    $bookingStatus = 'FOR PICKUP';
    $carStatus = 'RENTED';
} else { // decline
    $bookingStatus = 'CANCELED';
    $carStatus = 'AVAILABLE';
}

try {
    $conn->beginTransaction();

    // Update booking status
    $stmt = $conn->prepare("
        UPDATE CUSTOMER_BOOKING_DETAILS 
        SET STATUS = :booking_status 
        WHERE BOOKING_ID = :id
    ");
    $stmt->execute([
        ':booking_status' => $bookingStatus,
        ':id' => $booking_id
    ]);

    // Update car status
    $stmt = $conn->prepare("
        UPDATE CAR_DETAILS c
        INNER JOIN CUSTOMER_BOOKING_DETAILS b ON c.CAR_ID = b.CAR_ID
        SET c.STATUS = :car_status
        WHERE b.BOOKING_ID = :id
    ");
    $stmt->execute([
        ':car_status' => $carStatus,
        ':id' => $booking_id
    ]);

    $conn->commit();
    echo "success";
} catch (PDOException $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
