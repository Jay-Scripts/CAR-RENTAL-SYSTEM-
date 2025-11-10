<?php
include '../config/db.php';
require '../vendor/autoload.php'; // PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
session_start();

$changed_by = $_SESSION['user_id'] ?? null; // current logged-in user
$booking_id = intval($_POST['booking_id'] ?? 0);
$action     = $_POST['action'] ?? '';
$remarks    = $_POST['remarks'] ?? null;

if ($booking_id <= 0 || !in_array($action, ['approve', 'decline'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

try {
    $conn->beginTransaction();

    // 1️ Get previous status, customer info, and car info
    $stmt = $conn->prepare("
        SELECT cbd.STATUS AS prev_status, u.EMAIL, u.FIRST_NAME, c.CAR_NAME, cbd.PICKUP_DATE, cbd.DROP_OFF_DATE, cbd.TOTAL_COST
        FROM CUSTOMER_BOOKING_DETAILS cbd
        JOIN USER_DETAILS u ON cbd.USER_ID = u.USER_ID
        JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
        WHERE cbd.BOOKING_ID = :id
    ");
    $stmt->execute([':id' => $booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) throw new Exception("Booking not found.");

    $previousStatus = $booking['prev_status'];
    $customerEmail  = $booking['EMAIL'];
    $customerName   = $booking['FIRST_NAME'];
    $carName        = $booking['CAR_NAME'];
    $pickupDate     = date('F j, Y', strtotime($booking['PICKUP_DATE']));
    $dropOffDate    = date('F j, Y', strtotime($booking['DROP_OFF_DATE']));
    $totalAmount    = number_format($booking['TOTAL_COST'], 2);

    // 2️ Determine new booking/car status and email content
    if ($action === 'approve') {
        $bookingStatus = 'FOR PICKUP';
        $carStatus     = 'RENTED';
        $emailSubject  = 'Payment Confirmed : Thank You!';
        $emailBody     = "
            <p>Dear {$customerName},</p>
            <p>Your payment for the <b>{$carName}</b> rental has been successfully verified.</p>
            <p><b>Booking ID:</b> #{$booking_id}</p>
            <p><b>Pickup Date:</b> {$pickupDate}<br>
               <b>Drop-off Date:</b> {$dropOffDate}<br>
               <b>Total Amount:</b> ₱{$totalAmount}</p>
            <p>Your booking is now confirmed and ready your rented car is ready for pickup.</p>
            <p>Please be informed that you need to bring a valid ID upon pickup and a hardcopy of your sales Invoices for reference.</p>
            <p>Thank you for your prompt payment and for trusting <b>Car Rental System</b>.</p>
        ";
    } else { // decline
        $bookingStatus = 'CANCELED';
        $carStatus     = 'AVAILABLE';
        $emailSubject  = 'Payment Unsuccessful : Action Required';
        $emailBody     = "
        <p>Dear {$customerName},</p>
        <p>Unfortunately, your payment for the <b>{$carName}</b> rental has declined.</p>
        <p>As a result, your reservation has been automatically cancelled.</p>
        <p>Payment will be refund with in the day.</p>
        <p>Please make a new booking if you still wish to rent a vehicle. Thank you for your understanding.</p>
        <p><b>Booking ID:</b> #{$booking_id}</p>
        <p><b>Pickup Date:</b> {$pickupDate}<br>
           <b>Drop-off Date:</b> {$dropOffDate}<br>
           <b>Total Amount:</b> ₱{$totalAmount}</p>
    ";
    }


    // 3️ Update booking status
    $stmt = $conn->prepare("
        UPDATE CUSTOMER_BOOKING_DETAILS 
        SET STATUS = :booking_status 
        WHERE BOOKING_ID = :id
    ");
    $stmt->execute([':booking_status' => $bookingStatus, ':id' => $booking_id]);

    // 4️ Update car status
    $stmt = $conn->prepare("
        UPDATE CAR_DETAILS c
        INNER JOIN CUSTOMER_BOOKING_DETAILS b ON c.CAR_ID = b.CAR_ID
        SET c.STATUS = :car_status
        WHERE b.BOOKING_ID = :id
    ");
    $stmt->execute([':car_status' => $carStatus, ':id' => $booking_id]);

    // 5️ Insert into booking status logs
    $stmt = $conn->prepare("
        INSERT INTO BOOKING_STATUS_LOGS 
        (BOOKING_ID, PREVIOUS_STATUS, NEW_STATUS, CHANGED_BY, CHANGE_SOURCE, REMARKS) 
        VALUES (:booking_id, :previous_status, :new_status, :changed_by, :change_source, :remarks)
    ");
    $stmt->execute([
        ':booking_id'      => $booking_id,
        ':previous_status' => $previousStatus,
        ':new_status'      => $bookingStatus,
        ':changed_by'      => $changed_by,
        ':change_source'   => 'USER',
        ':remarks'         => $remarks
    ]);

    $conn->commit();

    // 6️ Send payment status email
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
        $mail->Subject = $emailSubject;
        $mail->Body    = $emailBody;

        $mail->send();
    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
    }

    echo json_encode([
        'status'         => 'success',
        'message'        => 'Booking has been successfully updated and email sent!',
        'booking_status' => $bookingStatus,
        'car_status'     => $carStatus
    ]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
