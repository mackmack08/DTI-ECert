<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate a unique client reference ID
 */
function generateClientReferenceId($conn, $type) {
    // Generate unique client ID based on type
    $prefix = '';
    switch ($type) {
        case 'citizen':
            $prefix = 'DTI-C-';
            break;
        case 'business':
            $prefix = 'DTI-B-';
            break;
        case 'government':
            $prefix = 'DTI-G-';
            break;
    }
    
    // Get the last ID from the database to create a sequential number
    $stmt = $conn->prepare("SELECT MAX(id) as max_id FROM clients");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $lastId = $row['max_id'] ?? 0;
    $newId = $lastId + 1;
    $uniqueId = $prefix . sprintf('%03d', $newId); // Format as 001, 002, etc.
    $stmt->close();
    
    return $uniqueId;
}
