<?php
require_once '../config/db.php';
session_start();
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Make sure PDO throws exceptions
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$data = json_decode(file_get_contents('php://input'), true);

$payment_id = intval($data['payment_id'] ?? 0);
$booking_id = intval($data['booking_id'] ?? 0);

if ($payment_id <= 0 || $booking_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

// Ensure logged-in user ID is valid or fallback to system
$changed_by = $_SESSION['USER_ID'] ?? null;
$source = $changed_by ? 'USER' : 'SYSTEM';

try {
    $conn->beginTransaction();

    // 1️⃣ Mark payment as PAID
    $stmt = $conn->prepare("UPDATE BOOKING_PAYMENT_DETAILS SET STATUS = 'PAID' WHERE PAYMENT_DETAILS_ID = :id");
    $stmt->execute([':id' => $payment_id]);
    if ($stmt->rowCount() === 0) {
        throw new Exception("Payment ID not found or already PAID.");
    }

    // 2️⃣ Fetch booking details for logging and email
    $stmt = $conn->prepare("
        SELECT cbd.STATUS, cbd.CAR_ID, u.FIRST_NAME, u.LAST_NAME, u.EMAIL, c.CAR_NAME
        FROM CUSTOMER_BOOKING_DETAILS cbd
        JOIN USER_DETAILS u ON cbd.USER_ID = u.USER_ID
        JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
        WHERE cbd.BOOKING_ID = :id
        FOR UPDATE
    ");
    $stmt->execute([':id' => $booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception("Booking not found.");
    }

    $previousStatus = $booking['STATUS'];
    $customerName   = $booking['FIRST_NAME'] . ' ' . $booking['LAST_NAME'];
    $customerEmail  = $booking['EMAIL'];
    $carModel       = $booking['CAR_NAME'];

    // 3️⃣ Update booking status to COMPLETED
    $stmt = $conn->prepare("UPDATE CUSTOMER_BOOKING_DETAILS SET STATUS = 'COMPLETED' WHERE BOOKING_ID = :id");
    $stmt->execute([':id' => $booking_id]);
    if ($stmt->rowCount() === 0) {
        throw new Exception("Failed to update booking status. It may already be completed.");
    }

    // 4️⃣ Insert booking log
    $stmt = $conn->prepare("
        INSERT INTO BOOKING_STATUS_LOGS
        (BOOKING_ID, PREVIOUS_STATUS, NEW_STATUS, CHANGED_BY, CHANGE_SOURCE, REMARKS)
        VALUES (:booking_id, :prev_status, :new_status, :changed_by, :source, :remarks)
    ");
    $stmt->execute([
        ':booking_id' => $booking_id,
        ':prev_status' => $previousStatus,
        ':new_status'  => 'COMPLETED',
        ':changed_by'  => $changed_by,
        ':source'      => $source,
        ':remarks'     => 'Payment completed, booking closed.'
    ]);
    if ($stmt->rowCount() === 0) {
        throw new Exception("Failed to insert booking log.");
    }

    $conn->commit();

    // 5️⃣ Send Thank You email (outside transaction)
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'carrentaljemjem@gmail.com';
        $mail->Password   = 'yaiu cwqf vehd uwtg';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('carrentaljemjem@gmail.com', 'Car Rental System');
        $mail->addAddress($customerEmail, $customerName);

        $mail->isHTML(true);
        $mail->Subject = 'Thank You for Choosing Our Car Rental!';
        $mail->Body = "
            <p>Dear {$customerName},</p>
            <p>We have successfully received your returned <strong>{$carModel}</strong>.</p>
            <p>Thank you for choosing our car rental service. We hope you had a smooth and enjoyable experience.</p>
            <p>We’d love to hear your feedback! Reply to this message or leave a quick review to help us improve.</p>
            <p>We look forward to serving you again!</p>
        ";
        $mail->send();
    } catch (Exception $e) {
        // Log email errors but don't fail the API
        error_log("Email sending failed for booking {$booking_id}: {$mail->ErrorInfo}");
    }

    echo json_encode(["status" => "success", "message" => "Booking completed and email sent."]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
