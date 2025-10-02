<?php
include '../config/db.php';
header('Content-Type: application/json');

$booking_id = intval($_POST['booking_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($booking_id <= 0 || !in_array($action, ['approve', 'decline'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

try {
    $conn->beginTransaction();

    if ($action === 'approve') {
        $bookingStatus = 'FOR PICKUP';
        $carStatus = 'RENTED';
    } else { // decline
        $bookingStatus = 'CANCELED';
        $carStatus = 'AVAILABLE'; // if you want the car released back to pool
        // $carStatus = 'CANCELED'; // <-- uncomment if you want car itself to reflect canceled
    }

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

    // Update car status based on booking
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

    echo json_encode([
        'status' => 'success',
        'message' => 'Booking has been successfully updated!',
        'booking_status' => $bookingStatus,
        'car_status' => $carStatus
    ]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
