<?php
// Include database connection
include("dbcon.php");

// Set content type to JSON
header('Content-Type: application/json');

// Check if we received POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the raw POST data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Check if we have the required data
if (!isset($data['name']) || !isset($data['image_data'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Get form data
$name = $data['name'];
$file_description = isset($data['file_description']) ? $data['file_description'] : '';
$imageData = $data['image_data']; // Base64 encoded image data

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/certificates/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$filename = 'certificate_' . time() . '_' . uniqid() . '.png';
$file_path = $upload_dir . $filename;

// Remove the data URL prefix and decode
$imageData = str_replace('data:image/png;base64,', '', $imageData);
$imageData = str_replace(' ', '+', $imageData);
$imageBinary = base64_decode($imageData);

// Save the image file
if (file_put_contents($file_path, $imageBinary)) {
    // Insert certificate information into the database
    $query = "INSERT INTO certificates (name, description, file_path) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $name, $file_description, $file_path);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Certificate saved successfully',
            'file_path' => $file_path
        ]);
    } else {
        // If database insertion fails, delete the uploaded file
        unlink($file_path);
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to save certificate information: ' . $conn->error
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save certificate image']);
}
?>
