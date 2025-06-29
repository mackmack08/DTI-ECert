<?php
session_start();
header('Content-Type: application/json');

// Get progress data from session
$progress = $_SESSION['certificate_progress'] ?? [
    'total' => 0,
    'current' => 0,
    'current_client' => '',
    'status' => 'idle',
    'message' => ''
];

echo json_encode($progress);
?>
