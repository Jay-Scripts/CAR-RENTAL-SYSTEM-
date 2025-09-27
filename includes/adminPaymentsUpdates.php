<?php
include '../config/db.php';

$booking_id = intval($_POST['booking_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($booking_id <= 0 || !in_array($action, ['approve', 'decline'])) {
    echo "Invalid request";
    exit;
}

$status = $action === 'approve' ? 'FOR PICKUP' : 'CANCELED';

$stmt = $conn->prepare("UPDATE CUSTOMER_BOOKING_DETAILS SET STATUS = :status WHERE BOOKING_ID = :id");
$stmt->execute([':status' => $status, ':id' => $booking_id]);

echo "success";
