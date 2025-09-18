<?php
session_start();

// clear all session variables
session_unset();

// destroy the session
session_destroy();

// redirect to login
header("Location: ../views/unRegistedUserModule/loginPage.php");
exit;
