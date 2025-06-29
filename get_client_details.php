<?php
// Include database connection
require_once 'dbcon.php';

// Check if client_id is provided
if (!isset($_GET['client_id']) || empty($_GET['client_id'])) {
    echo json_encode(['error' => 'Client ID is required']);
    exit;
}

$clientId = $_GET['client_id'];

// Prepare and execute query to get client details
$stmt = $conn->prepare("SELECT * FROM clients WHERE reference_id = ?");
$stmt->bind_param("s", $clientId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Client not found']);
    exit;
}

$clientDetails = $result->fetch_assoc();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($clientDetails);

$stmt->close();
$conn->close();
?>
