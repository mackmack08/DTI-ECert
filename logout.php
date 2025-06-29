<?php
session_start(); // Start session at the beginning
include("logincode.php");
include("dbcon.php"); 

// Check if the user is logged in before logging out
if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];

    // Unset all session variables and destroy the session
    session_unset();
    session_destroy();

    // Start a new session and set the logout message
    session_start();
    $_SESSION['status'] = "Logged Out!";

    // Redirect to index.php
    header("Location: index.php");
    exit();
}
?>
