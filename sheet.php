<?php
session_start(); // Start session at the beginning
include('dbcon.php'); // Include database connection
require 'vendor/autoload.php'; // Include PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;

$pageTitle = "Sheet Management";
$currentPage = "Sheet Management";

// Initialize message variables
$success_message = '';
$error_message = '';

// Fetch certificates for dropdown
$certificates = [];
$cert_query = "SELECT * FROM certificates ORDER BY name ASC";
$cert_result = $conn->query($cert_query);
while ($cert_row = $cert_result->fetch_assoc()) {
    $certificates[] = [
        'id' => $cert_row['id'],
        'name' => $cert_row['name'],
        'description' => $cert_row['description'],
        'file_path' => $cert_row['file_path']    
    ];
}

// Retrieve all unarchived uploaded files from the database
$uploaded_files = []; 
$query = "SELECT f.*, c.name as cert_name 
          FROM files f 
          LEFT JOIN certificates c ON f.cert_type = c.id 
          WHERE f.status = 'Unarchived'";

// Add search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = '%' . $conn->real_escape_string($_GET['search']) . '%';
    $query .= " AND (f.file_name LIKE ? OR c.name LIKE ?)";
}

$query .= " ORDER BY f.upload_time DESC";

// Prepare and execute the query
$stmt = $conn->prepare($query);

// If search is active, bind the parameters
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $stmt->bind_param("ss", $search, $search);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $uploaded_files[] = [
        'id' => $row['id'],
        'file_name' => $row['file_name'],
        'file_path' => $row['file_path'],
        'upload_time' => $row['upload_time'],
        'cert_type' => $row['cert_type'],
        'cert_name' => $row['cert_name']
    ];
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_upload'])) {
    $file_uploads = $_FILES['file_upload'];
    $upload_dir = 'uploads/';
    $completion_date = isset($_POST['completion_date']) ? $_POST['completion_date'] : null;
    $cert_type = isset($_POST['cert_type']) ? $_POST['cert_type'] : null;
    $status = "Unarchived";
    $carp = $_POST['carp'];
    $staff = $_POST['staff'];
    $starting_number = $_POST['starting_number'];
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $upload_success = false; // Flag to track if any file was successfully uploaded
    
    // Iterate through all uploaded files
    for ($i = 0; $i < count($file_uploads['name']); $i++) {
        $file_name = $_POST['file_name'] ?? $file_uploads['name'][$i]; // Use the name from the input field if it's available
        $file_tmp_name = $file_uploads['tmp_name'][$i];
        $file_size = $file_uploads['size'][$i];
        $file_error = $file_uploads['error'][$i];
        $upload_path = $upload_dir . basename($file_name);
        
        if ($file_error === UPLOAD_ERR_OK) {
            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file_tmp_name, $upload_path)) {
                // Insert file information into the database
                $stmt = $conn->prepare("INSERT INTO files (file_name, file_path, cert_type, status, starting_number) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssisi", $file_name, $upload_path, $cert_type, $status, $starting_number);
                if ($stmt->execute()) {
                    // Get the last inserted ID and current timestamp
                    $last_id = $conn->insert_id;
                    $current_time = date("Y-m-d H:i:s");
                    
                    $file_id = $last_id;
                    $upload_success = true; // Set flag to true
                }
                $stmt->close();
                
                // Load the spreadsheet and process it
                $spreadsheet = IOFactory::load($upload_path);
                $sheet = $spreadsheet->getActiveSheet();
                
                // Iterate over the rows in the spreadsheet
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // Loop through each row and insert the data into the clients table
                for ($row = 2; $row <= $highestRow; $row++) { // Assuming row 1 contains headers
                    $timestampValue = $sheet->getCell('A' . $row)->getValue();
                    
                    // Skip rows with empty timestamp
                    if (empty($timestampValue)) {
                        continue;
                    }
                    
                    // Check if it's a numeric value (Excel date/time)
                    if (is_numeric($timestampValue)) {
                        // Convert Excel date/time to PHP DateTime
                        $dateObject = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($timestampValue);
                        // Format the date as needed
                        $timestamp = $dateObject->format('Y-m-d H:i:s');
                    } else {
                        // If it's already a string, use it as is
                        $timestamp = $timestampValue;
                    }
                    
                    // Get all cell values and ensure they're not NULL
                    $client_name = strtoupper($sheet->getCell('B' . $row)->getValue()) ?? '';
                    $client_type = $sheet->getCell('C' . $row)->getValue() ?? '';
                    $sex = $sheet->getCell('D' . $row)->getValue() ?? '';
                    $age = $sheet->getCell('E' . $row)->getValue() ?? '';
                    $region = $sheet->getCell('F' . $row)->getValue() ?? '';
                    $contact = $sheet->getCell('G' . $row)->getValue() ?? '';
                    $email = $sheet->getCell('H' . $row)->getValue() ?? '';
                    $service_ro_objectives_achieved = $sheet->getCell('I' . $row)->getValue() ?? '';
                    $service_ro_info_received = $sheet->getCell('J' . $row)->getValue() ?? '';
                    $service_ro_relevance_value = $sheet->getCell('K' . $row)->getValue() ?? '';
                    $service_ro_duration_sufficient = $sheet->getCell('L' . $row)->getValue() ?? '';
                    $service_af_sign_up_access = $sheet->getCell('M' . $row)->getValue() ?? '';
                    $service_af_audio_video_sync = $sheet->getCell('N' . $row)->getValue() ?? '';
                    $resource_speaker_rq_knowledge = $sheet->getCell('O' . $row)->getValue() ?? '';
                    $resource_speaker_rq_clarity = $sheet->getCell('P' . $row)->getValue() ?? '';
                    $resource_speaker_rq_engagement = $sheet->getCell('Q' . $row)->getValue() ?? '';
                    $resource_speaker_rq_visual_relevance = $sheet->getCell('R' . $row)->getValue() ?? '';
                    $resource_speaker_ri_answer_questions = $sheet->getCell('S' . $row)->getValue() ?? '';
                    $resource_speaker_ri_chat_responsiveness = $sheet->getCell('T' . $row)->getValue() ?? '';
                    $moderator_rr_manage_discussion = $sheet->getCell('U' . $row)->getValue() ?? '';
                    $moderator_rr_monitor_raises_questions = $sheet->getCell('V' . $row)->getValue() ?? '';
                    $moderator_rr_manage_program = $sheet->getCell('W' . $row)->getValue() ?? '';
                    $host_secretariat_rr_technical_assistance = $sheet->getCell('X' . $row)->getValue() ?? '';
                    $host_secretariat_rr_admittance_management = $sheet->getCell('Y' . $row)->getValue() ?? '';
                    $overall_satisfaction_rating = $sheet->getCell('Z' . $row)->getValue() ?? '';
                    $feedback_dissatisfied_reasons = $sheet->getCell('AA' . $row)->getValue() ?? '';
                    $feedback_improvement_suggestions = $sheet->getCell('AB' . $row)->getValue() ?? '';
                    $status = 'Unarchived';
                    $manual = 'no';

                    // Insert into clients table with completion date
                    $stmt = $conn->prepare("INSERT INTO clients (timestamp, client_name, client_type, sex, age, region, contact,
                    email, service_ro_objectives_achieved, service_ro_info_received, service_ro_relevance_value,
                    service_ro_duration_sufficient, service_af_sign_up_access, service_af_audio_video_sync, resource_speaker_rq_knowledge,
                    resource_speaker_rq_clarity, resource_speaker_rq_engagement, resource_speaker_rq_visual_relevance, resource_speaker_ri_answer_questions,
                    resource_speaker_ri_chat_responsiveness, moderator_rr_manage_discussion, moderator_rr_monitor_raises_questions, moderator_rr_manage_program,
                    host_secretariat_rr_technical_assistance, host_secretariat_rr_admittance_management, overall_satisfaction_rating,
                    feedback_dissatisfied_reasons, feedback_improvement_suggestions, completion_date, cert_type, file_id, status, carp, manual, staff)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    $stmt->bind_param("sssssssssssssssssssssssssssssiissss",
                    $timestamp, $client_name, $client_type, $sex, $age, $region, $contact, $email,
                    $service_ro_objectives_achieved, $service_ro_info_received, $service_ro_relevance_value, $service_ro_duration_sufficient,
                    $service_af_sign_up_access, $service_af_audio_video_sync, $resource_speaker_rq_knowledge, $resource_speaker_rq_clarity,
                    $resource_speaker_rq_engagement, $resource_speaker_rq_visual_relevance, $resource_speaker_ri_answer_questions,
                    $resource_speaker_ri_chat_responsiveness, $moderator_rr_manage_discussion, $moderator_rr_monitor_raises_questions,
                    $moderator_rr_manage_program, $host_secretariat_rr_technical_assistance, $host_secretariat_rr_admittance_management,
                    $overall_satisfaction_rating, $feedback_dissatisfied_reasons, $feedback_improvement_suggestions, $completion_date, $cert_type, $file_id, $status, $carp, $manual, $staff
                    );
                    
                    // Execute the statement and handle any errors
                    try {
                        $stmt->execute();
                    } catch (Exception $e) {
                        echo "Error inserting row $row: " . $e->getMessage() . "<br>";
                    }
                }
            }
        }
    }
    
    // Set success message in session and redirect
    if ($upload_success) {
        $_SESSION['success_message'] = "Sheet uploaded successfully!";
    } else {
        $_SESSION['error_message'] = "There was a problem uploading the sheet.";
    }
    
    // Redirect to prevent form resubmission
    header("Location: sheet.php");
    exit;
}

// Handle Edit Sheet form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_file_id'])) {
    $file_id = $_POST['edit_file_id'];
    $new_file_name = $_POST['edit_file_name'];
    $upload_dir = 'uploads/';
    
    // Update file name in database
    $stmt = $conn->prepare("UPDATE files SET file_name = ? WHERE id = ?");
    $stmt->bind_param("si", $new_file_name, $file_id);
    $stmt->execute();
    
    // Check if a new file was uploaded
    if (isset($_FILES['edit_file_upload']) && $_FILES['edit_file_upload']['error'] === UPLOAD_ERR_OK) {
        // Get the current file path
        $query = "SELECT file_path FROM files WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $old_file_path = $row['file_path'];
        
        // Upload the new file
        $file_tmp_name = $_FILES['edit_file_upload']['tmp_name'];
        $new_file_path = $upload_dir . basename($new_file_name);
        
        if (move_uploaded_file($file_tmp_name, $new_file_path)) {
            // Update the file path in the database
            $stmt = $conn->prepare("UPDATE files SET file_path = ? WHERE id = ?");
            $stmt->bind_param("si", $new_file_path, $file_id);
            $stmt->execute();
            
            // Delete the old file if it exists and is different from the new path
            if (file_exists($old_file_path) && $old_file_path != $new_file_path) {
                unlink($old_file_path);
            }
            
            // Process the new Excel file
            $spreadsheet = IOFactory::load($new_file_path);
            $sheet = $spreadsheet->getActiveSheet();
            
            // You may want to update the client data here as well
            // For simplicity, we're not implementing that part in this example
        }
    }
    
    // Set success message in session and redirect
    $_SESSION['success_message'] = "Sheet updated successfully!";
    
    // Redirect to prevent form resubmission
    header("Location: sheet.php");
    exit;
}

// Handle Archive Sheet form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_file_id'])) {
    $file_id = $_POST['delete_file_id'];
    
    try {
        // Start transaction to ensure both operations succeed or fail together
        $conn->begin_transaction();
         
        // Also update the status to 'Archived' for all related client records
        $query = "UPDATE files SET status = 'Archived' WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        
        // Commit the transaction
        $conn->commit();
        
        // Set success message in session
        $_SESSION['success_message'] = "Sheet archived successfully!";
    } catch (Exception $e) {
        // If there's an error, roll back the transaction
        $conn->rollback();
        $_SESSION['error_message'] = "Error archiving sheet: " . $e->getMessage();
    }
    
    // Redirect to prevent form resubmission
    header("Location: sheet.php");
    exit;
}

// Include the header
include('header.php');
// Include the sidebar
include('sidebar.php');

$conn->close(); // Close the database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="shortcut icon" href="img/OIP.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
    /* Additional styles moved from inline CSS */
    .upload-btn {
        display: inline-block;
        width: auto;
        margin-bottom: 20px;
    }

    .card {
        margin-bottom: 15px;
    }

    .card-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .upload-time {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .view-btn {
        width: auto;
        padding: 0.25rem 0.5rem;
    }

    .modal-footer {
        justify-content: flex-end;
    }

    .modal-footer .btn-group {
        display: flex;
        width: 100%;
        justify-content: space-between;
    }

    .modal-footer .btn {
        width: 30%;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        margin: 0 5px;
    }

    .modal-footer .btn:first-child {
        margin-left: 0;
    }

    .modal-footer .btn:last-child {
        margin-right: 0;
    }

    /* Custom styles for the close button */
    .modal-header .close {
        margin: -1rem -1rem -1rem auto;
        padding: 0.5rem 0.5rem;
        background-color: transparent;
        border: none;
        font-size: 1.5rem;
        opacity: 0.5;
    }

    .modal-header .close:hover {
        opacity: 1;
    }

    /* Custom upload button color */
    .btn-custom-upload {
        background-color: #01043A !important;
        color: white !important;
        border: none !important;
    }

    .btn-custom-upload:hover {
        background-color: #01043A !important;
        color: white !important;
    }

    /* Button container for upload modal */
    .button-container {
        display: flex;
        justify-content: flex-end;
        width: 100%;
    }

    .button-container .btn {
        margin-left: 10px;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    /* Delete button styling */
    .btn-danger {
        background-color: #dc3545;
        color: white;
        border: none;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    /* Action buttons container */
    .action-buttons {
        display: flex;
        gap: 5px;
    }

    /* Card styling */
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        background-color: #01043A !important;
        color: white !important;
        font-weight: 600;
        padding: 12px 15px;
        border-bottom: none;
    }

    .card-body {
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .upload-time {
        color: #7f8c8d;
        font-size: 14px;
        margin-bottom: 10px;
        flex: 1 0 100%;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 10px;
        flex-wrap: wrap;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.2rem;
    }

    /* Button styling */
    .btn-view-details {
        background-color: #01043A !important;
        color: white !important;
        border: none !important;
    }

    .btn-view-details:hover {
        background-color: #01043A !important;
        opacity: 0.9;
        color: white !important;
    }

    .btn-edit-sheet {
        background-color: #FFD700 !important;
        color: #212529 !important;
        border: none !important;
    }

    .btn-edit-sheet:hover {
        background-color: #FFC107 !important;
        color: #212529 !important;
    }

    .btn-archive-sheet {
        background-color: #FF8C00 !important;
        color: white !important;
        border: none !important;
    }

    .btn-archive-sheet:hover {
        background-color: #FF7000 !important;
        color: white !important;
    }

    .btn-upload-new {
        background-color: #006400 !important;
        color: white !important;
        border: none !important;
    }

    .btn-upload-new:hover {
        background-color: #005000 !important;
        color: white !important;
    }

    .btn-archived-sheets {
        background-color: #FF8C00 !important;
        color: white !important;
        border: none !important;
    }

    .btn-archived-sheets:hover {
        background-color: #FF7000 !important;
        color: white !important;
    }

    /* Alert styling */
    .alert {
        border-radius: 8px;
        margin-bottom: 20px;
        position: relative;
        padding-right: 40px; /* Make room for the close button */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .alert .close {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        padding: 0.75rem 1.25rem;
        background: transparent;
        border: 0;
        opacity: 0.5;
    }

    .alert .close:hover {
        opacity: 1;
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }

    /* Certificate preview */
    .certificate-preview {
        max-width: 100%;
        max-height: 300px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
    }

    .modal-dialog.modal-lg {
        max-width: 800px;
    }

    .certificate-preview-container {
        border-left: 1px solid #dee2e6;
        padding-left: 15px;
    }

    /* Make buttons slightly rounded rectangles instead of ovals */
    .btn {
        border-radius: 4px !important;
    }

    /* Enhanced styling for View Details modal */
    .modal-dialog {
        max-width: 600px; /* Increase modal width */
    }

    /* View Details Modal styling */
    #viewModal .modal-body {
        padding: 25px;
        background-color: #f8f9fa;
    }

    #viewModal .modal-body p {
        margin-bottom: 15px; /* Increase spacing between paragraphs */
        padding: 10px;
        background-color: white;
        border-radius: 6px;
        border-left: 4px solid #01043A;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    #viewModal .modal-body p strong {
        display: inline-block;
        width: 120px; /* Fixed width for labels */
        color: #01043A;
    }

    #viewModal .modal-header {
        background-color: #01043A;
        color: white;
        padding: 15px 25px;
    }

    #viewModal .modal-footer {
        padding: 15px 25px;
        background-color: #f8f9fa;
    }

    #viewModal .modal-footer .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        width: 100%;
        justify-content: flex-end;
    }

    #viewModal .modal-footer .btn {
        flex: 0 0 auto;
        margin: 0;
    }

    /* Make buttons in the modal footer more spaced out */
    #viewModal .btn-group .btn {
        margin-right: 8px;
        padding: 8px 16px;
    }

    /* Apply the same styling to all view modals with dynamic IDs */
    [id^="viewModal"] .modal-body {
        padding: 25px;
        background-color: #f8f9fa;
    }

    [id^="viewModal"] .modal-body p {
        margin-bottom: 15px;
        padding: 10px;
        background-color: white;
        border-radius: 6px;
        border-left: 4px solid #01043A;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    [id^="viewModal"] .modal-body p strong {
        display: inline-block;
        width: 120px;
        color: #01043A;
    }

    [id^="viewModal"] .modal-header {
        background-color: #01043A;
        color: white;
        padding: 15px 25px;
    }

    [id^="viewModal"] .modal-footer {
        padding: 15px 25px;
        background-color: #f8f9fa;
    }

    [id^="viewModal"] .modal-footer .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        width: 100%;
        justify-content: flex-end;
    }

    [id^="viewModal"] .modal-footer .btn {
        flex: 0 0 auto;
        margin: 0;
    }

    [id^="viewModal"] .btn-group .btn {
        margin-right: 8px;
        padding: 8px 16px;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        [id^="viewModal"] .modal-body p strong {
            display: block;
            width: 100%;
            margin-bottom: 5px;
        }
        
        [id^="viewModal"] .modal-footer .btn-group {
            justify-content: center;
        }
        
        [id^="viewModal"] .btn-group .btn {
            flex: 1 1 auto;
            text-align: center;
            margin-bottom: 8px;
        }
    }

    /* Archived sheets page styling */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 15px;
    }

    .page-header h2 {
        color: #01043A;
        font-weight: 600;
        font-size: 24px;
        margin-bottom: 5px;
        text-align: left;
    }

    .page-header p {
        margin: 0;
        color: #6c757d;
        font-size: 0.9rem;
    }
   

    /* Add new styles for certificate type display */
    .certificate-type {
        margin-top: 8px;
        margin-bottom: 15px;
        color: #555;
        font-size: 14px;
    }

    .certificate-type i {
        color: #FF8C00;
        margin-right: 5px;
    }
    
    .text-muted {
        color: #6c757d !important;
        font-style: italic;
    }
    
    /* Search bar styling - simplified version with reduced height */
    .search-container {
        max-width: 400px;
        width: 100%;
    }
    
    .btn-search {
        background-color: #01043A;
        color: white;
        border-color: #01043A;
        height: 36px; /* Match the height of the form-control-sm */
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }
    
    .btn-search:hover {
        background-color: #0038A8;
        color: white;
    }
    
    .input-group {
        border-radius: 4px;
        overflow: hidden;
    }
    
    .input-group .form-control {
        border-right: none;
        height: 36px; /* Reduced height */
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }
    
    /* Remove focus shadow and change border color to blue */
    .input-group .form-control:focus {
        box-shadow: none;
        border-color: #0038A8;
    }
    
    .input-group-append .btn {
        border-left: none;
    }

        /* Form control small - reduced height */
    .form-control-sm {
        height: 31px; /* Reduced height */
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    
    /* Remove focus shadow and change border color to blue for all form controls */
    .form-control:focus {
        box-shadow: none;
        border-color: #0038A8;
    }
    
    /* Action buttons container */
    .action-buttons-container {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .search-container {
            max-width: 100%;
        }
        
        .action-buttons-container {
            flex-direction: column;
        }
        
        .action-buttons-container .btn {
            width: 100%;
        }
    }

    /* Custom info alert styling - scoped to avoid affecting other elements */
    .custom-info-alert {
        background-color: #e6f3ff !important; /* Light blue background */
        color: #01043A !important; /* Dark blue text - matching the primary color */
        border-color: #b8daff !important;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
    }

    .custom-info-alert i {
        font-size: 1.2rem;
        margin-right: 10px;
        color: #01043A;
    }

    /* Add this CSS to your sheet.php file in the <style> section */

    /* Style for "No archived sheets found" alert */
    .archived-sheets-container .alert-info {
        background-color: #e6f3ff !important; 
        color: #01043A !important;
        border-color: #b8daff !important;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 500;
    }

    .archived-sheets-container .alert-info i {
        font-size: 1.2rem;
        margin-right: 10px;
        color: #01043A;
    }
    </style>
    <script>
        // JavaScript to update the input field with the selected file name
        function updateFileName(input) {
            var fileName = input.files[0] ? input.files[0].name : '';
            document.getElementById('file_name_input').value = fileName;
        }
        
        // JavaScript to update the edit form with the selected file name
        function updateEditFileName(input) {
            var fileName = input.files[0] ? input.files[0].name : '';
            document.getElementById('edit_file_name_display').textContent = fileName;
        }
        
        // Function to populate the edit modal with file data
        function populateEditModal(fileId, fileName, filePath) {
            document.getElementById('edit_file_id').value = fileId;
            document.getElementById('edit_file_name').value = fileName;
            document.getElementById('current_file_path').textContent = filePath;
            document.getElementById('edit_file_name_display').textContent = "No new file selected";
        }
        
        // Function to populate the archive modal with file data
        function populateDeleteModal(fileId, fileName) {
            document.getElementById('delete_file_id').value = fileId;
            document.getElementById('delete_file_name').textContent = fileName;
        }
        
        // Function to show certificate preview
        function showCertificatePreview(previewElementId, certificateId) {
            const previewElement = document.getElementById(previewElementId);
            
            if (!certificateId) {
                previewElement.innerHTML = '<p class="text-muted">No certificate selected</p>';
                return;
            }
            
            // Find the certificate data from our PHP-generated certificates array
            const certificates = <?php echo json_encode($certificates); ?>;
            const certificate = certificates.find(cert => cert.id === certificateId);
            
            if (certificate) {
                previewElement.innerHTML = `
                    <img src="${certificate.file_path}" alt="${certificate.name}" class="certificate-preview">
                    <p class="mt-2"><strong>${certificate.name}</strong></p>
                    <p class="text-muted">${certificate.description || ''}</p>
                `;
            } else {
                previewElement.innerHTML = '<p class="text-danger">Certificate not found</p>';
            }
        }
    </script>
</head>

<body>
    <div class="main-content" style="margin-top: 120px;">
        <!-- Display success/error messages -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show notification-alert" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <strong><?php echo $success_message; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show notification-alert" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong><?php echo $error_message; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Search bar and action buttons in separate rows -->
        <div class="row mb-3">
            <!-- Search bar in the upper left -->
            <div class="col-md-6">
                
                    <form action="sheet.php" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search sheets..." name="search" 
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <div class="input-group-append">
                                <button class="btn btn-search" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
            </div>
            
            <!-- Action buttons in the upper right -->
            <div class="col-md-6">
                <div class="action-buttons-container">
                    <a href="archived_sheets.php" class="btn btn-archived-sheets">
                        <i class="bi bi-archive"></i> Archived Sheets
                    </a>
                    <button class="btn btn-upload-new" data-toggle="modal" data-target="#uploadModal">
                        <i class="bi bi-plus"></i> Upload New Sheet
                    </button>
                </div>
            </div>
        </div>
            
        <!-- Modal for File Upload -->
        <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="sheet.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadModalLabel">Upload New Sheet</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <!-- File Name Input Field (Above File Upload Input) -->
                                    <label for="file_name_input">Sheet Name:</label>
                                    <input type="text" id="file_name_input" name="file_name" value="" placeholder="Enter file name" class="form-control"><br>
                                    
                                    <!-- Date Uploaded Input -->
                                    <label for="completion_date">Date Uploaded:</label>
                                    <input type="date" id="completion_date" name="completion_date" class="form-control" required><br>
                                    
                                    <!-- Certificate Type Dropdown -->
                                    <label for="cert_type">Certificate Type:</label>
                                    <select id="cert_type" name="cert_type" class="form-control" onchange="showCertificatePreview('cert_preview', this.value)">
                                        <option value="">-- Select Certificate --</option>
                                        <?php foreach ($certificates as $cert): ?>
                                        <option value="<?php echo $cert['id']; ?>"><?php echo htmlspecialchars($cert['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select><br>
                                    <label for="starting_number">Starting Certificate Number:</label>
                                    <input type="number" id="starting_number" name="starting_number" value="" placeholder="Enter starting number" class="form-control" min="1"><br>            
                                    <div class="form-group">
                                        <label>Carp:</label>
                                        <div class="d-flex">
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" name="carp" id="dti_yes" value="Yes">
                                                <label class="form-check-label" for="dti_yes">
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="carp" id="dti_no" value="No">
                                                <label class="form-check-label" for="dti_no">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <label for="staff_name_input">Conducted by:</label>
                                    <input type="text" id="staff_name_input" name="staff" value="" placeholder="Enter staff name" class="form-control"><br>
                                    <!-- File Upload Input -->
                                    <label for="file_upload">File Upload (Excel File):</label>
                                    <input type="file" id="file_upload" name="file_upload[]" accept=".xlsx, .xls" multiple required onchange="updateFileName(this)" class="form-control-file">
                                </div>
                                <div class="col-md-5 certificate-preview-container">
                                    <h6>Certificate Preview:</h6>
                                    <div id="cert_preview">
                                        <p class="text-muted">No certificate selected</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="button-container">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-upload-new btn-sm">Upload Sheet</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
        <!-- Modal for Edit Sheet -->
        <div class="modal" id="editSheetModal" tabindex="-1" role="dialog" aria-labelledby="editSheetModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="sheet.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editSheetModalLabel">Edit Sheet</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_file_id" name="edit_file_id">
                            
                            <!-- File Name Input Field -->
                            <label for="edit_file_name">Sheet Name:</label>
                            <input type="text" id="edit_file_name" name="edit_file_name" placeholder="Enter file name" class="form-control"><br>
                            
                            <!-- Current File Information -->
                            <div class="form-group">
                                <label>Current File:</label>
                                <p id="current_file_path" class="form-control-static"></p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="button-container">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-edit-sheet btn-sm">Update Sheet</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Modal for Archive Sheet -->
        <div class="modal" id="deleteSheetModal" tabindex="-1" role="dialog" aria-labelledby="deleteSheetModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="sheet.php" method="POST">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title" id="deleteSheetModalLabel">Archive Sheet</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="delete_file_id" name="delete_file_id">
                            <p>Are you sure you want to archive the sheet: <strong><span id="delete_file_name"></span></strong>?</p>
                            <p><i class="bi bi-info-circle text-info"></i> This will archive both the sheet and all associated client data.</p>
                            <p><i class="bi bi-arrow-return-left text-success"></i> You can restore archived sheets from the Archived Sheets page.</p>
                        </div>
                        <div class="modal-footer">
                            <div class="button-container">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-archive-sheet btn-sm">
                                    <i class="bi bi-archive"></i> Archive Sheet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
        <!-- Display Cards for Each Uploaded File -->
        <?php if (!empty($uploaded_files)) {
            foreach ($uploaded_files as $file) { ?>
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-file-earmark-excel"></i> Uploaded File: <?php echo htmlspecialchars($file['file_name']); ?>
                    </div>
                    <div class="card-body">
                <div class="upload-time">
                    <i class="bi bi-clock"></i> Upload Time: <?php echo htmlspecialchars($file['upload_time']); ?>
                </div>
                <div class="certificate-type">
                    <i class="bi bi-award"></i> Certificate Type: 
                    <?php 
                    if (!empty($file['cert_name'])) {
                        echo htmlspecialchars($file['cert_name']);
                    } else {
                        echo '<span class="text-muted">Not assigned</span>';
                    }
                    ?>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-view-details btn-sm" data-toggle="modal" data-target="#viewModal<?php echo $file['id']; ?>">
                        <i class="bi bi-eye"></i> View
                    </button>
                                        <button type="button" class="btn btn-edit-sheet btn-sm" onclick="populateEditModal('<?php echo $file['id']; ?>', '<?php echo htmlspecialchars($file['file_name']); ?>', '<?php echo htmlspecialchars($file['file_path']); ?>')" data-toggle="modal" data-target="#editSheetModal">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button type="button" class="btn btn-archive-sheet btn-sm" onclick="populateDeleteModal('<?php echo $file['id']; ?>', '<?php echo htmlspecialchars($file['file_name']); ?>')" data-toggle="modal" data-target="#deleteSheetModal">
                        <i class="bi bi-archive"></i> Archive
                    </button>
                </div>
            </div>
        </div>
        
    <!-- View Details Modal - Replace the existing modal with this improved version -->
    <div class="modal fade" id="viewModal<?php echo $file['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel<?php echo $file['id']; ?>" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel<?php echo $file['id']; ?>">
                        <i class="bi bi-info-circle me-2"></i> Sheet Details
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Sheet Name:</strong> <?php echo htmlspecialchars($file['file_name']); ?></p>
                    <p><strong>Upload Date:</strong> <?php echo htmlspecialchars(date('F d, Y h:i A', strtotime($file['upload_time']))); ?></p>
                    <p><strong>File Location:</strong> <?php echo htmlspecialchars($file['file_path']); ?></p>
                    <p><strong>Certificate Type:</strong> 
                        <?php 
                        if (!empty($file['cert_name'])) {
                            echo htmlspecialchars($file['cert_name']);
                        } else {
                            echo '<span class="text-muted">Not assigned</span>';
                        }
                        ?>
                    </p>
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <a href="<?php echo htmlspecialchars($file['file_path']); ?>" class="btn btn-success" download>
                            <i class="bi bi-download"></i> Download Excel
                        </a>
                        <a href="client_certificates.php?file_id=<?php echo $file['id']; ?>" class="btn btn-primary">
                            <i class="bi bi-award"></i> Issue Certificate
                        </a>
                        <a href="view_certificate.php?file_id=<?php echo $file['id']; ?>" class="btn btn-info">
                            <i class="bi bi-card-list"></i> View Certificates
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php }
} else { ?>
    <div class="alert custom-info-alert" style="text-align: center;">
    <i class="bi bi-info-circle"></i> No sheets have been uploaded yet.
    </div>
<?php } ?>
</div>

<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var alerts = document.querySelectorAll('.notification-alert');
        alerts.forEach(function(alert) {
            // Check if Bootstrap 5 is available
            if (typeof bootstrap !== 'undefined') {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                // Fallback for Bootstrap 4
                $(alert).alert('close');
            }
        });
    }, 5000);
});
</script>

<?php
// Include the footer
include('footer.php');
?>
</body>
</html>

