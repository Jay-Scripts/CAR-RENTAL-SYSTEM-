<?php
include "../config/db.php";
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['month'])) {
    echo json_encode([]);
    exit;
}

$month = $data['month']; // format: YYYY-MM

// Completed Bookings
$completedStmt = $conn->prepare("SELECT COUNT(*) AS total_completed, SUM(TOTAL_COST) AS total_income 
    FROM CUSTOMER_BOOKING_DETAILS 
    WHERE STATUS='COMPLETED' AND DATE_FORMAT(CREATED_AT, '%Y-%m') = ?");
$completedStmt->execute([$month]);
$completed = $completedStmt->fetch(PDO::FETCH_ASSOC);

// Penalties
$penaltyStmt = $conn->prepare("SELECT COUNT(*) AS penalty_count, SUM(PENALTY) AS total_penalty 
    FROM BOOKING_VEHICLE_INSPECTION 
    WHERE PENALTY > 0 AND DATE_FORMAT(CREATED_AT, '%Y-%m') = ?");
$penaltyStmt->execute([$month]);
$penalties = $penaltyStmt->fetch(PDO::FETCH_ASSOC);

$reportData = [
    "Total Completed Bookings" => $completed['total_completed'] ?? 0,
    "Total Sale Income" => '₱' . number_format($completed['total_income'] ?? 0, 2),
    "Total Penalty Count" => $penalties['penalty_count'] ?? 0,
    "Total Penalty Amount" => '₱' . number_format($penalties['total_penalty'] ?? 0, 2)
];

echo json_encode($reportData);
