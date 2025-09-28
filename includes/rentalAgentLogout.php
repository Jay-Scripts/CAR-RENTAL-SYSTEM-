<?php
session_start();

// clear all session variables
session_unset();

// destroy the session
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Logging Out</title>
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex items-center justify-center h-screen bg-gray-100 font-sans">
    <div class="flex flex-col items-center">
        <!-- Spinner -->
        <div class="w-12 h-12 border-4 border-gray-300 border-t-blue-500 rounded-full animate-spin"></div>

        <!-- Message -->
        <p class="mt-4 text-lg text-gray-700 font-medium">Logging out...</p>
    </div>

    <script>
        // Wait 2 seconds then redirect
        setTimeout(() => {
            window.location.href = "../views/rentalAgentDashboard/rentalAgentLoginPage.php";
        }, 2000);
    </script>
</body>

</html>