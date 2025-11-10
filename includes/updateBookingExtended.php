<?php
include '../config/db.php';
require '../vendor/autoload.php'; // PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Manila');
$today = date('Y-m-d'); // only date, no time

// Fetch bookings that are CHECKING and drop off date has passed
$stmt = $conn->prepare("
    SELECT cbd.BOOKING_ID, cbd.STATUS, u.EMAIL, u.FIRST_NAME, c.CAR_NAME, cbd.DROP_OFF_DATE
    FROM CUSTOMER_BOOKING_DETAILS cbd
    JOIN USER_DETAILS u ON cbd.USER_ID = u.USER_ID
    JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
    WHERE cbd.STATUS = 'CHECKING' 
      AND DATE(cbd.DROP_OFF_DATE) < :today
");
$stmt->execute([':today' => $today]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$updatedCount = 0;

foreach ($bookings as $b) {
    $bookingId     = $b['BOOKING_ID'];
    $prevStatus    = $b['STATUS'];
    $customerEmail = $b['EMAIL'];
    $customerName  = $b['FIRST_NAME'];
    $carName       = $b['CAR_NAME'];
    $dropOffDate   = date('F j, Y', strtotime($b['DROP_OFF_DATE']));

    // 1️⃣ Update status to EXTENDED
    $update = $conn->prepare("
        UPDATE CUSTOMER_BOOKING_DETAILS 
        SET STATUS = 'EXTENDED' 
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
        ':new'  => 'EXTENDED'
    ]);

    $updatedCount++;

    // 3️⃣ Send Extended Reservation email
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
        $mail->Subject = "Rental Time Exceeded Extended Reservation Notice";
        $mail->Body = "
            <p>Dear {$customerName},</p>
            <p>Our system shows that your rental for <b>{$carName}</b> has exceeded the scheduled return time.</p>
            <p>Your reservation will be marked as <b>EXTENDED</b>, and additional rental hours may apply.</p>
            <p>Please contact us if you need further assistance.</p>
            <p>Thank you for renting with <b>Car Rental System</b>.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Mail error for Booking ID {$bookingId}: " . $mail->ErrorInfo);
    }
}

// Return JSON response
echo json_encode(['status' => 'ok', 'updated' => $updatedCount]);
