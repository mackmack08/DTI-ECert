<?php
include('dbcon.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $requiredFields = ['client_name', 'reference_id', 'client_type', 'email', 'contact', 'sex', 'age', 'region', 'completion_date', 'file_id', 'cert_type', 'carp'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        $client_name = strtoupper($_POST['client_name']);
        $status = 'Unarchived';
        $manual = 'yes';

        // Prepare with positional placeholders
        $stmt = $conn->prepare("INSERT INTO clients (
            client_name, reference_id, client_type, email, contact,
            sex, age, region, completion_date, file_id,
            cert_type, carp, status, manual, staff
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Execute with values in order
        $stmt->execute([
            $client_name,
            $_POST['reference_id'],
            $_POST['client_type'],
            $_POST['email'],
            $_POST['contact'],
            $_POST['sex'],
            $_POST['age'],
            $_POST['region'],
            $_POST['completion_date'],
            $_POST['file_id'],
            $_POST['cert_type'],
            $_POST['carp'],
            $_POST['staff'],
            $status,
            $manual
        ]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}