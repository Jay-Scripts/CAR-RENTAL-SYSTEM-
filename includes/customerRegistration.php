<?php
$customer_registration_message = [
    "email" => "",
    "password" => "",
    "repeat_password" => "",
    "gender" => "",
    "first_name" => "",
    "last_name" => "",
    "phone" => "",
    "address" => "",
];

if (isset($_POST['customerRegister'])) {

    // 1. Trim
    $email        = trim($_POST['email'] ?? '');
    $password     = trim($_POST['password'] ?? '');
    $repeat_pass  = trim($_POST['repeat_password'] ?? '');
    $gender       = trim($_POST['gender'] ?? '');
    $first_name   = trim($_POST['first_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $address      = trim($_POST['address'] ?? '');

    // 2. Sanitize
    $sanitized_email       = filter_var($email, FILTER_SANITIZE_EMAIL);
    $sanitized_password    = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');
    $sanitized_repeat_pass = htmlspecialchars($repeat_pass, ENT_QUOTES, 'UTF-8');
    $sanitized_gender      = htmlspecialchars($gender, ENT_QUOTES, 'UTF-8');
    $sanitized_first_name  = htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8');
    $sanitized_last_name   = htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8');
    $sanitized_phone       = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
    $sanitized_address     = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');

    // 3. Validation
    // Email
    if (empty($sanitized_email)) {
        $customer_registration_message['email'] = "<p class='text-red-500 text-sm'>Email is required.</p>";
    } elseif (!filter_var($sanitized_email, FILTER_VALIDATE_EMAIL)) {
        $customer_registration_message['email'] = "<p class='text-red-500 text-sm'>Please enter a valid email format.</p>";
    }

    // Password
    if (empty($sanitized_password)) {
        $customer_registration_message['password'] = "<p class='text-red-500 text-sm'>Password is required.</p>";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $sanitized_password)) {
        $customer_registration_message['password'] = "<p class='text-red-500 text-sm'>Password must be at least 8 characters, with upper, lower, number, and special char.</p>";
    }


    // Repeat password
    if (empty($sanitized_repeat_pass)) {
        $customer_registration_message['repeat_password'] = "<p class='text-red-500 text-sm'>Password is required.</p>";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $sanitized_password)) {
        $customer_registration_message['password'] = "<p class='text-red-500 text-sm'>Password must be at least 8 characters, with upper, lower, number, and special char.</p>";
    } elseif ($sanitized_repeat_pass !== $sanitized_password) {
        $customer_registration_message['repeat_password'] = "<p class='text-red-500 text-sm'>Passwords do not match.</p>";
    }

    // Gender
    $allowed_genders = ["Male", "Female", "Other"];
    if (!in_array($sanitized_gender, $allowed_genders)) {
        $customer_registration_message['gender'] = "<p class='text-red-500 text-sm'>Invalid gender value.</p>";
    }


    // First name
    if (empty($sanitized_first_name)) {
        $customer_registration_message['first_name'] = "<p class='text-red-500 text-sm'>First name is required.</p>";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $sanitized_first_name)) {
        $customer_registration_message['first_name'] = "<p class='text-red-500 text-sm'>First name must only contains letter.</p>";
    }

    // Last name
    if (empty($sanitized_last_name)) {
        $customer_registration_message['last_name'] = "<p class='text-red-500 text-sm'>Last name is required.</p>";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $sanitized_last_name)) {
        $customer_registration_message['last_name'] = "<p class='text-red-500 text-sm'>Last name must only contains letter.</p>";
    }

    // Phone (basic check, adjust regex to your rules)
    if (empty($sanitized_phone)) {
        $customer_registration_message['phone'] = "<p class='text-red-500 text-sm'>Phone number is required.</p>";
    } elseif (!preg_match("/^[0-9]{10,15}$/", $sanitized_phone)) {
        $customer_registration_message['phone'] = "<p class='text-red-500 text-sm'>Enter a valid phone number (10–15 digits).</p>";
    }

    // Address
    if (empty($sanitized_address)) {
        $customer_registration_message['address'] = "<p class='text-red-500 text-sm'>Address is required.</p>";
    }
}
