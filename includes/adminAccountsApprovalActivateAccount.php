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

    // 1️⃣ Get user email
    $stmt = $conn->prepare("SELECT u.EMAIL, u.FIRST_NAME FROM USER_ACCOUNT ua 
                            JOIN USER_DETAILS u ON ua.USER_ID = u.USER_ID
                            WHERE ua.ACCOUNT_ID = :id");
    $stmt->execute([':id' => $account_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo "User not found";
        exit;
    }

    // 2️⃣ Update account status
    $stmt = $conn->prepare("UPDATE USER_ACCOUNT SET STATUS = :status WHERE ACCOUNT_ID = :id");
    $success = $stmt->execute([':status' => $status, ':id' => $account_id]);

    if ($success && $stmt->rowCount() > 0) {

        // 3️⃣ Send email notification
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'carrentaljemjem@gmail.com';
            $mail->Password   = 'yaiu cwqf vehd uwtg'; // App password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('carrentaljemjem@gmail.com', 'Car Rental System');
            $mail->addAddress($user['EMAIL'], $user['FIRST_NAME']);

            $mail->isHTML(true);
            $mail->Subject = 'Account Status Update';
            $actionText = $status === 'ACTIVE' ? 'activated' : 'declined';
            $mail->Body    = "Hi {$user['FIRST_NAME']},<br><br>Your account has been <b>{$actionText}</b>.<br><br>Regards,<br>Car Rental System";

            $mail->send();
        } catch (Exception $e) {
            // Optional: log error but don't block response
        }

        echo "success";
    } else {
        http_response_code(500);
        echo "Failed to update status";
    }
}
