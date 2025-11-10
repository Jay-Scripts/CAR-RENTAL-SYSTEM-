<?php
include '../config/db.php';
require '../vendor/autoload.php'; // PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Manila');
$today = date('Y-m-d H:i:s');

// Fetch bookings that are ONGOING and drop off date is today
$stmt = $conn->prepare("
    SELECT cbd.BOOKING_ID, cbd.STATUS, u.EMAIL, u.FIRST_NAME, c.CAR_NAME, cbd.DROP_OFF_DATE
    FROM CUSTOMER_BOOKING_DETAILS cbd
    JOIN USER_DETAILS u ON cbd.USER_ID = u.USER_ID
    JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
    WHERE cbd.STATUS = 'ONGOING' 
      AND DATE(cbd.DROP_OFF_DATE) = CURDATE()
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$updatedCount = 0;

foreach ($bookings as $b) {
    $bookingId  = $b['BOOKING_ID'];
    $prevStatus = $b['STATUS'];
    $customerEmail = $b['EMAIL'];
    $customerName  = $b['FIRST_NAME'];
    $carName       = $b['CAR_NAME'];
    $dropOffDate   = date('F j, Y H:i', strtotime($b['DROP_OFF_DATE']));

    // 1️⃣ Update status to CHECKING
    $update = $conn->prepare("
        UPDATE CUSTOMER_BOOKING_DETAILS 
        SET STATUS = 'CHECKING' 
        WHERE BOOKING_ID = :id
    ");
    $update->execute([':id' => $bookingId]);

    // 2️⃣ Insert log
    $log = $conn->prepare("
        INSERT INTO BOOKING_STATUS_LOGS 
        (BOOKING_ID, PREVIOUS_STATUS, NEW_STATUS, CHANGE_SOURCE) 
        VALUES (:bid, :prev, :new, 'SYSTEM')
    ");
    $log->execute([
        ':bid'  => $bookingId,
        ':prev' => $prevStatus,
        ':new'  => 'CHECKING'
    ]);

    $updatedCount++;

    // 3️⃣ Send reminder email
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'carrentaljemjem@gmail.com';
        $mail->Password   = 'yaiu cwqf vehd uwtg'; // Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('carrentaljemjem@gmail.com', 'Car Rental System');
        $mail->addAddress($customerEmail, $customerName);
        $mail->isHTML(true);
        $mail->Subject = "Reminder Car Return Due Soon";
        $mail->Body = "
            <p>Dear {$customerName},</p>
            <p>This is a friendly reminder that your rental period for <b>{$carName}</b> will end on <b>{$dropOffDate}</b>.</p>
            <p>Please return the vehicle on time to avoid additional charges.</p>
            <p>Thank you for your cooperation and for renting with <b>Car Rental System</b>.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Mail error for Booking ID {$bookingId}: " . $mail->ErrorInfo);
    }
}

// Return JSON response
echo json_encode(['status' => 'ok', 'updated' => $updatedCount]);
