<?php
include "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['car_id'])) {
    echo json_encode(['success' => false, 'message' => 'Car ID missing']);
    exit;
}

$carId = $data['car_id'];

$stmt = $conn->prepare("DELETE FROM CAR_DETAILS WHERE CAR_ID = ?");
if ($stmt->execute([$carId])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete car']);
}
