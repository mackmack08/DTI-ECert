<?php
include("dbcon.php");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['deleteClientId'];

    // Update client status to 'Archived' instead of deleting
    $stmt = $conn->prepare("UPDATE clients SET status = 'Archived' WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Client archived successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt->close();

    header("Location: client_management.php");
    exit;
}
