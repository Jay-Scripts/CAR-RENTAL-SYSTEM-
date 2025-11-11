<?php
include "../config/db.php";
require "../vendor/autoload.php"; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';
    $car_status_note = $_POST['car_status_note'] ?? null;
    $user_id = $_SESSION['USER_ID'] ?? null;

    if ($booking_id > 0 && $new_status === 'ONGOING') {
        try {
            $conn->beginTransaction();

            // Get previous status and booking details
            $stmtPrev = $conn->prepare("
                SELECT cbd.STATUS, u.EMAIL, u.FIRST_NAME, c.CAR_NAME
                FROM CUSTOMER_BOOKING_DETAILS cbd
                JOIN USER_DETAILS u ON cbd.USER_ID = u.USER_ID
                JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
                WHERE cbd.BOOKING_ID = ?
            ");
            $stmtPrev->execute([$booking_id]);
            $bookingData = $stmtPrev->fetch(PDO::FETCH_ASSOC);

            if (!$bookingData) throw new Exception("Booking not found.");

            $prevStatus = $bookingData['STATUS'];
            $customerEmail = $bookingData['EMAIL'];
            $customerName = $bookingData['FIRST_NAME'];
            $carModel = $bookingData['CAR_NAME'];
            $handoverDate = date('F j, Y g:i A');

            // Handle optional image upload
            $car_status_image = null;
            if (!empty($_FILES['car_status_image']['name'])) {
                $uploadDir = "../uploads/car_status/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileName = uniqid('status_') . "_" . basename($_FILES['car_status_image']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['car_status_image']['tmp_name'], $targetPath)) {
                    $car_status_image = "uploads/car_status/" . $fileName;
                }
            }

            // Update booking
            $stmtUpdate = $conn->prepare("
                UPDATE CUSTOMER_BOOKING_DETAILS 
                SET STATUS = ?, CAR_STATUS_NOTE = ?, CAR_STATUS_IMAGE = ?
                WHERE BOOKING_ID = ?
            ");
            $stmtUpdate->execute([$new_status, $car_status_note, $car_status_image, $booking_id]);

            // Update car status
            $stmtCar = $conn->prepare("SELECT CAR_ID FROM CUSTOMER_BOOKING_DETAILS WHERE BOOKING_ID = ?");
            $stmtCar->execute([$booking_id]);
            $car = $stmtCar->fetch(PDO::FETCH_ASSOC);

            if ($car && isset($car['CAR_ID'])) {
                $car_id = $car['CAR_ID'];
                $stmtUpdateCar = $conn->prepare("UPDATE CAR_DETAILS SET STATUS = 'RENTED' WHERE CAR_ID = ?");
                $stmtUpdateCar->execute([$car_id]);
            }

            // Log status change
            $stmtLog = $conn->prepare("
                INSERT INTO BOOKING_STATUS_LOGS 
                (BOOKING_ID, PREVIOUS_STATUS, NEW_STATUS, CHANGED_BY, CHANGE_SOURCE, REMARKS)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmtLog->execute([
                $booking_id,
                $prevStatus,
                $new_status,
                $user_id,
                $user_id ? 'USER' : 'SYSTEM',
                $car_status_note
            ]);

            // Commit transaction
            $conn->commit();

            // Send email confirmation
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'carrentaljemjem@gmail.com';
                $mail->Password = 'yaiu cwqf vehd uwtg'; // Gmail App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('carrentaljemjem@gmail.com', 'Car Rental System');
                $mail->addAddress($customerEmail, $customerName);
                $mail->isHTML(true);
                $mail->Subject = "Vehicle Handover Confirmation";
                $mail->Body = "
                    <p>Dear {$customerName},</p>
                    <p>This is to confirm that you have successfully received the vehicle <b>{$carModel}</b> on <b>{$handoverDate}</b>.</p>
                    <p>Please ensure that the vehicle is returned on or before the scheduled drop-off time to avoid additional charges.</p>
                    <p>Kindly note that any extension beyond the agreed rental period will incur additional payment, and any damages identified upon return will be subject to assessment and corresponding penalties.</p>
                    <p>Thank you for choosing <b>Car Rental System</b>. Drive safely and enjoy your trip!</p>
                    <p>Best regards,<br><b>Car Rental System</b></p>
                ";
                $mail->send();
            } catch (Exception $e) {
                error_log("Email sending failed for Booking ID {$booking_id}: " . $mail->ErrorInfo);
            }

            echo "success";
        } catch (Exception $e) {
            $conn->rollBack();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Invalid request";
    }
}
