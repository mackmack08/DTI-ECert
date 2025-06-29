<?php
include("dbcon.php");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = $_POST['editClientId'];
    $name = trim($_POST['editClientName']);
    $type = trim($_POST['editClientType']);
    $region = trim($_POST['editClientRegion']);
    $contact = trim($_POST['editClientContact']);
    $email = trim($_POST['editClientEmail']);
    
    // Additional fields for citizen type
    $sex = ($type == 'citizen' && isset($_POST['editClientSex'])) ? trim($_POST['editClientSex']) : '';
    $age = ($type == 'citizen' && isset($_POST['editClientAge'])) ? intval($_POST['editClientAge']) : 0;
    
    // Basic validation
    if (empty($name) || empty($type) || empty($region) || empty($contact) || empty($email)) {
        $_SESSION['message'] = "Please fill all required fields.";
        $_SESSION['message_type'] = "danger";
        header("Location: client_management.php");
        exit;
    }
    
    // Check if email already exists for other clients
    $stmt = $conn->prepare("SELECT id FROM clients WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['message'] = "Another client with this email already exists.";
        $_SESSION['message_type'] = "danger";
        header("Location: client_management.php");
        exit;
    }
    
    // Update client in database
    $stmt = $conn->prepare("UPDATE clients SET client_name = ?, client_type = ?, sex = ?, age = ?, region = ?, contact = ?, email = ? WHERE id = ?");
    $stmt->bind_param("sssisssi", $name, $type, $sex, $age, $region, $contact, $email, $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Client updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt->close();
    
    header("Location: client_management.php");
    exit;
}
