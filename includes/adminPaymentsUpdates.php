<?php
include '../config/db.php';
header('Content-Type: application/json');

$booking_id = intval($_POST['booking_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($booking_id <= 0 || !in_array($action, ['approve', 'decline'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$bookingStatus = $action === 'approve' ? 'FOR PICKUP' : 'CANCELED';
$carStatus = $action === 'approve' ? 'RENTED' : 'AVAILABLE';

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("
        UPDATE CUSTOMER_BOOKING_DETAILS 
        SET STATUS = :booking_status 
        WHERE BOOKING_ID = :id
    ");
    $stmt->execute([':booking_status' => $bookingStatus, ':id' => $booking_id]);

    $stmt = $conn->prepare("
        UPDATE CAR_DETAILS c
        INNER JOIN CUSTOMER_BOOKING_DETAILS b ON c.CAR_ID = b.CAR_ID
        SET c.STATUS = :car_status
        WHERE b.BOOKING_ID = :id
    ");
    $stmt->execute([':car_status' => $carStatus, ':id' => $booking_id]);

    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Booking has been successfully updated!'
    ]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
