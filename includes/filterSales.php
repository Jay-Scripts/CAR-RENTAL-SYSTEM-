<?php
include "../config/db.php";
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['date'])) {
    echo json_encode([]);
    exit;
}

$date = $data['date'];

$stmt = $conn->prepare("
    SELECT cbd.BOOKING_ID, CONCAT(u.FIRST_NAME,' ',u.LAST_NAME) AS CUSTOMER,
           c.CAR_NAME, cbd.PICKUP_DATE, cbd.DROP_OFF_DATE, cbd.TOTAL_COST
    FROM CUSTOMER_BOOKING_DETAILS cbd
    JOIN USER_DETAILS u ON cbd.USER_ID = u.USER_ID
    JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
    WHERE cbd.STATUS = 'COMPLETED'
      AND DATE(cbd.CREATED_AT) = ?
    ORDER BY cbd.CREATED_AT DESC
");

$stmt->execute([$date]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
