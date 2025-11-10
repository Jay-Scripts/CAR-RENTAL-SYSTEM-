<?php
include "../config/db.php";
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_id = $_POST['account_id'] ?? null;
    $status = $_POST['status_action'] ?? null; // ACTIVE / DECLINED

    if (!$account_id || !$status) {
        http_response_code(400);
        echo "Invalid parameters";
        exit;
    }

    // 1️ Get user email
    $stmt = $conn->prepare("
        SELECT u.EMAIL, u.FIRST_NAME 
        FROM USER_ACCOUNT ua 
        JOIN USER_DETAILS u ON ua.USER_ID = u.USER_ID
        WHERE ua.ACCOUNT_ID = :id
    ");
    $stmt->execute([':id' => $account_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo "User not found";
        exit;
    }

    // 2️ Update account status
    $stmt = $conn->prepare("UPDATE USER_ACCOUNT SET STATUS = :status WHERE ACCOUNT_ID = :id");
    $success = $stmt->execute([':status' => $status, ':id' => $account_id]);

    if ($success && $stmt->rowCount() > 0) {
        try {
            // 3️ Send email notification
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'carrentaljemjem@gmail.com';
            $mail->Password   = 'yaiu cwqf vehd uwtg'; // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('carrentaljemjem@gmail.com', 'Car Rental System');
            $mail->addAddress($user['EMAIL'], $user['FIRST_NAME']);
            $mail->isHTML(true);

            // 4️ Define email subject and message
            if ($status === 'ACTIVE') {
                $mail->Subject = 'Account Registration Approved!';
                $mail->Body = "
                    <p>Dear {$user['FIRST_NAME']},</p>
                    <p>Your account registration has been successfully <b>approved</b>!</p>
                    <p>You can now log in to your account and start exploring our car rental services.</p>
                    <p>Thank you for choosing <b>Car Rental System</b>. We’re excited to serve your travel needs!</p>
                    <br>
                    <p>Best regards,<br><b>Car Rental System Team</b></p>
                ";
            } else {
                $mail->Subject = 'Account Registration Declined';
                $mail->Body = "
                    <p>Dear {$user['FIRST_NAME']},</p>
                    <p>We regret to inform you that your account registration has been <b>declined</b>.</p>
                    <p>If you believe this was a mistake or wish to reapply, please contact our support team.</p>
                    <br>
                    <p>Best regards,<br><b>Car Rental System Team</b></p>
                ";
            }

            $mail->send();
        } catch (Exception $e) {
            error_log("Mail error: " . $mail->ErrorInfo);
        }

        echo "success";
    } else {
        http_response_code(500);
        echo "Failed to update status";
    }
}
