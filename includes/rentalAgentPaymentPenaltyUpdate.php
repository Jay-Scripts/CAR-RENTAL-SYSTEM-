<?php
require_once '../config/db.php';
$data = json_decode(file_get_contents('php://input'), true);

$payment_id = intval($data['payment_id'] ?? 0);
$booking_id = intval($data['booking_id'] ?? 0);

if ($payment_id <= 0 || $booking_id <= 0) {
    echo "Invalid request";
    exit;
}

try {
    $conn->beginTransaction();

    // 1. Mark penalty as PAID
    $stmt = $conn->prepare("UPDATE BOOKING_PAYMENT_DETAILS SET STATUS = 'PAID' WHERE PAYMENT_DETAILS_ID = :id");
    $stmt->execute([':id' => $payment_id]);

    // 2. Update booking status to COMPLETED
    $stmt = $conn->prepare("UPDATE CUSTOMER_BOOKING_DETAILS SET STATUS = 'COMPLETED' WHERE BOOKING_ID = :id");
    $stmt->execute([':id' => $booking_id]);

    $conn->commit();
    echo "success";
} catch (PDOException $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
