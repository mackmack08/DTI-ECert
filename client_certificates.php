<?php
session_start();
include('dbcon.php');
require 'vendor/autoload.php';

// Initialize variables first
$selected_file_id = 0;
$files = [];
$clients = [];
$certificates = [];
$message = '';
$messageType = '';

// Check if certificates are currently being generated
$generation_in_progress = isset($_SESSION['certificates_generating']) && $_SESSION['certificates_generating'] == $selected_file_id;

// Clear generation flag if it's been more than 10 minutes (cleanup)
if (isset($_SESSION['generation_start_time']) && (time() - $_SESSION['generation_start_time']) > 600) {
    unset($_SESSION['certificates_generating']);
    unset($_SESSION['generation_start_time']);
    $generation_in_progress = false;
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $file_id = intval($_POST['file_id'] ?? 0);
    
    switch ($action) {
        case 'assign_certificate':
            $cert_id = intval($_POST['cert_id'] ?? 0);
            if ($file_id > 0 && $cert_id > 0) {
                $update_query = "UPDATE files SET cert_type = ? WHERE id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("ii", $cert_id, $file_id);
                
                if ($stmt->execute()) {
                    // Clear any existing certificate paths since template changed
                    $clear_query = "UPDATE clients SET file_path = NULL WHERE file_id = ?";
                    $clear_stmt = $conn->prepare($clear_query);
                    $clear_stmt->bind_param("i", $file_id);
                    $clear_stmt->execute();
                    
                    // Clear ZIP file path
                    $clear_zip_query = "UPDATE files SET zip_file = NULL WHERE id = ?";
                    $clear_zip_stmt = $conn->prepare($clear_zip_query);
                    $clear_zip_stmt->bind_param("i", $file_id);
                    $clear_zip_stmt->execute();
                    
                    // Clear session data
                    unset($_SESSION['certificates_generated']);
                    
                    $message = "Certificate template assigned successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error assigning certificate template.";
                    $messageType = "danger";
                }
            }
            break;
            
        case 'delete_client':
            $client_id = intval($_POST['client_id'] ?? 0);
            if ($file_id > 0 && $client_id > 0) {
                // Get client file path before deletion
                $get_client_query = "SELECT file_path FROM clients WHERE id = ? AND file_id = ?";
                $stmt = $conn->prepare($get_client_query);
                $stmt->bind_param("ii", $client_id, $file_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $client_data = $result->fetch_assoc();
                
                // Delete client from database
                $delete_query = "DELETE FROM clients WHERE id = ? AND file_id = ?";
                $stmt = $conn->prepare($delete_query);
                $stmt->bind_param("ii", $client_id, $file_id);
                
                if ($stmt->execute()) {
                    // Delete certificate file if exists
                    if (!empty($client_data['file_path'])) {
                        $file_path = str_replace($_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '', $client_data['file_path']);
                        $file_path = ltrim($file_path, '/');
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                    
                    $message = "Client deleted successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error deleting client.";
                    $messageType = "danger";
                }
            }
            break;
            
        case 'regenerate_certificates':
            if ($file_id > 0) {
                // Clear all certificate paths
                $clear_query = "UPDATE clients SET file_path = NULL WHERE file_id = ?";
                $stmt = $conn->prepare($clear_query);
                $stmt->bind_param("i", $file_id);
                $stmt->execute();
                
                // Clear ZIP file path
                $clear_zip_query = "UPDATE files SET zip_file = NULL WHERE id = ?";
                $stmt = $conn->prepare($clear_zip_query);
                $stmt->bind_param("i", $file_id);
                $stmt->execute();
                
                // Clear session data
                unset($_SESSION['certificates_generated']);
                
                // Delete existing certificate files
                $get_files_query = "SELECT file_path FROM clients WHERE file_id = ? AND file_path IS NOT NULL";
                $stmt = $conn->prepare($get_files_query);
                $stmt->bind_param("i", $file_id);
                $stmt->execute();
                $files_result = $stmt->get_result();
                
                while ($file_row = $files_result->fetch_assoc()) {
                    if (!empty($file_row['file_path'])) {
                        $file_path = str_replace($_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '', $file_row['file_path']);
                        $file_path = ltrim($file_path, '/');
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
                
                $message = "All certificates cleared. You can now generate new certificates.";
                $messageType = "success";
            }
            break;
    }
    
    // Redirect to prevent form resubmission
    $_SESSION['certificate_message'] = $message;
    $_SESSION['certificate_message_type'] = $messageType;
    header("Location: client_certificates.php?file_id=" . $file_id);
    exit();
}

// Handle certificate issuance for a single client
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['issue_certificate'])) {
    $client_id = $_POST['client_id'];
    $file_id = $_POST['file_id'];
    
    // Redirect to certificate generation page
    header("Location: generate_certificate.php?client_id={$client_id}&file_id={$file_id}");
    exit();
}

// Handle batch certificate generation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_all_certificates'])) {
    $file_id = $_POST['file_id'];
    $client_name_left = $_POST['client_name_left'];
    $client_name_top = $_POST['client_name_top'];
    $client_name_font_size = $_POST['client_name_font_size'];
    $client_name_font_weight = $_POST['client_name_font_weight'];
    
    $reference_id_left = $_POST['reference_id_left'];
    $reference_id_top = $_POST['reference_id_top'];
    $reference_id_font_size = $_POST['reference_id_font_size'];
    $reference_id_font_weight = $_POST['reference_id_font_weight'];
    
    // Save template settings to session
    $_SESSION['certificate_template'] = [
        'file_id' => $file_id,
        'client_name' => [
            'left' => $client_name_left,
            'top' => $client_name_top,
            'font_size' => $client_name_font_size,
            'font_weight' => $client_name_font_weight
        ],
        'reference_id' => [
            'left' => $reference_id_left,
            'top' => $reference_id_top,
            'font_size' => $reference_id_font_size,
            'font_weight' => $reference_id_font_weight
        ]
    ];
    
    // Redirect to batch certificate generation page
    header("Location: generate_all_certificates.php?file_id={$file_id}");
    exit();
}

// Get message from session
if (isset($_SESSION['certificate_message'])) {
    $message = $_SESSION['certificate_message'];
    $messageType = $_SESSION['certificate_message_type'] ?? 'info';
    unset($_SESSION['certificate_message']);
    unset($_SESSION['certificate_message_type']);
}

// Get all files with their associated certificate information
$file_query = "SELECT f.id, f.file_name, f.upload_time, f.cert_type, f.zip_file,
                      c.id as cert_id, c.name as cert_name, c.file_path as cert_file_path,
                      c.description as cert_description
               FROM files f
               LEFT JOIN certificates c ON f.cert_type = c.id
               ORDER BY f.upload_time DESC";
$file_result = $conn->query($file_query);
while ($file_row = $file_result->fetch_assoc()) {
    $files[$file_row['id']] = [
        'id' => $file_row['id'],
        'file_name' => $file_row['file_name'],
        'upload_date' => $file_row['upload_time'],
        'cert_type' => $file_row['cert_type'],
        'cert_name' => $file_row['cert_name'],
        'cert_file_path' => $file_row['cert_file_path'],
        'cert_description' => $file_row['cert_description'],
        'zip_file' => $file_row['zip_file']
    ];
}

// Get selected file_id from GET parameter or default to first file
$selected_file_id = isset($_GET['file_id']) ? intval($_GET['file_id']) : (count($files) > 0 ? array_key_first($files) : 0);

// Get clients for the selected file
$clients = [];
if ($selected_file_id > 0) {
    $client_query = "SELECT id, client_name, reference_id, email, file_path
                     FROM clients
                     WHERE file_id = ?
                     ORDER BY client_name ASC";
    $stmt = $conn->prepare($client_query);
    $stmt->bind_param("i", $selected_file_id);
    $stmt->execute();
    $client_result = $stmt->get_result();
    
    while ($client_row = $client_result->fetch_assoc()) {
        $clients[] = [
            'id' => $client_row['id'],
            'client_name' => $client_row['client_name'],
            'reference_id' => $client_row['reference_id'],
            'email' => $client_row['email'],
            'file_path' => $client_row['file_path']
        ];
    }
}

// Get selected client_id from GET parameter or default to first client
$selected_client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : (count($clients) > 0 ? $clients[0]['id'] : 0);

// Fetch all available certificates for template assignment
$certificates_query = "SELECT id, name, description FROM certificates ORDER BY name ASC";
$certificates_result = $conn->query($certificates_query);
while ($row = $certificates_result->fetch_assoc()) {
    $certificates[] = $row;
}

// Calculate certificate generation statistics
$generated_count = 0;
$total_count = count($clients);
foreach ($clients as $client) {
    if (!empty($client['file_path'])) {
        $generated_count++;
    }
}
$generation_percentage = $total_count > 0 ? round(($generated_count / $total_count) * 100) : 0;

$conn->close();

// Set page-specific variables
$pageTitle = "Client Certificate Management";
$currentPage = "Client Certificates";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #01043A;
            --secondary-color: #3498db;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-radius: 6px;
            --box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        body {
            background-color: #f5f7fa;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            color: #333;
            line-height: 1.5;
        }
        .main-content {
            padding: 15px 0;
        }
        /* Card Styling */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
            margin-bottom: 15px;
            height: 100%;
        }
        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #01043A !important;
            color: white;
            font-weight: 600;
            padding: 8px 12px;
            border-bottom: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-header h4 {
            margin-bottom: 0;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .card-header i {
            margin-right: 6px;
        }
        .card-body {
            padding: 10px;
        }
        /* Compact card for top section */
        .compact-card {
            min-height: 180px; /* Increased from 170px to prevent cropping */
            max-height: none; /* Remove max-height to prevent cropping */
        }
        .compact-card .card-body {
            padding: 8px;
        }
        .compact-card .info-item {
            margin-bottom: 3px;
            padding: 2px 0;
        }
        /* Buttons */
        .btn {
            border-radius: 4px;
            font-weight: 500;
            padding: 4px 8px;
            transition: var(--transition);
            margin: 1px;
            display: inline-flex;
                        align-items: center;
            gap: 4px;
        }
        .btn i {
            font-size: 0.9em;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: #162a78;
            border-color: #162a78;
            transform: translateY(-1px);
        }
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
            transform: translateY(-1px);
        }
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #e0a800;
            color: #212529;
            transform: translateY(-1px);
        }
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
            transform: translateY(-1px);
        }
        .btn-sm {
            padding: 2px 6px;
            font-size: 0.75rem;
        }
        /* Form Elements */
        .form-select, .form-control {
            border-radius: 4px;
            border: 1px solid #ddd;
            padding: 4px 8px;
            transition: var(--transition);
            font-size: 0.85rem;
        }
        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.15rem rgba(1, 4, 58, 0.25);
        }
        /* Table Styling */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 0;
        }
        .table th {
            background-color: #01043A !important;
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.3px;
            padding: 6px 4px;
            border: none;
            text-align: center;
        }
        .table td {
            padding: 5px 4px;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 0.8rem;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(236, 240, 241, 0.5);
        }
        .table-responsive {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }
        /* Alerts */
        .alert {
            border-radius: var(--border-radius);
            padding: 8px 12px;
            margin-bottom: 10px;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            position: relative;
            padding-right: 30px;
            font-size: 0.85rem;
        }
        
        .btn-close {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.8;
            padding: 0.2rem;
            font-size: 0.8rem;
        }
        
        .btn-close:hover {
            opacity: 1;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 3px solid var(--warning-color);
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 3px solid var(--success-color);
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 3px solid #17a2b8;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 3px solid var(--danger-color);
        }
        /* Modal Styling */
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 10px 12px;
        }
        .modal-title {
            font-weight: 600;
            font-size: 1rem;
        }
        .modal-body {
            padding: 12px 15px;
            font-size: 0.85rem;
        }
        .modal-footer {
            border-top: 1px solid #eee;
            padding: 10px 12px;
        }
        /* Certificate preview */
        .certificate-preview-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            padding: 3px;
            min-height: 120px; /* Ensure minimum height */
        }
        .certificate-preview {
            max-width: 100%;
            max-height: 140px; /* Increased from 120px */
            border: 1px solid #ddd;
            border-radius: 3px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            object-fit: contain;
        }
        
        /* File info section */
        .file-info {
            background-color: #f8f9fa;
            padding: 6px 8px;
            border-radius: var(--border-radius);
            margin-bottom: 6px;
            font-size: 0.8rem;
        }
        .file-info h5 {
            color: var(--primary-color);
            margin-bottom: 5px;
            font-size: 0.85rem;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            padding: 2px 0;
            border-bottom: 1px solid #eee;
        }
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .info-label {
            font-weight: 600;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        /* Certificate assignment section */
        .certificate-assignment {
            background-color: #fff;
            padding: 8px;
            border-radius: var(--border-radius);
            border: 1px dashed #ddd;
            margin-bottom: 8px;
            text-align: center;
        }
        .certificate-assignment.assigned {
            border-color: var(--success-color);
            background-color: #f8fff9;
        }
        .certificate-assignment.assigned .assignment-icon {
            color: var(--success-color);
        }
        .assignment-icon {
            font-size: 1.2rem;
            color: #ddd;
            margin-bottom: 3px;
        }
        /* Action buttons section */
        .action-section {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: var(--border-radius);
            margin-bottom: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
            justify-content: center;
        }
        /* Badge styling */
        .badge {
            padding: 3px 6px;
            font-weight: 500;
            border-radius: 20px;
            font-size: 0.7rem;
        }
        .bg-success {
            background-color: var(--success-color) !important;
        }
        .bg-danger {
            background-color: var(--danger-color) !important;
        }
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        .bg-warning {
            background-color: var(--warning-color) !important;
            color: #212529 !important;
        }
        .bg-secondary {
            background-color: #6c757d !important;
        }
        /* Client info styling */
        .client-name {
            font-weight: 600;
            color: var(--primary-color);
            text-align: left;
            font-size: 0.8rem;
        }
        .client-email {
            color: #6c757d;
            font-size: 0.75rem;
            text-align: left;
        }
        /* Action buttons */
        .action-buttons-cell {
            display: flex;
            gap: 2px;
            justify-content: center;
            flex-wrap: wrap;
        }
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 10px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 1.5rem;
            margin-bottom: 6px;
            color: #ddd;
        }
        .empty-state h5 {
            margin-bottom: 5px;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .empty-state p {
            font-size: 0.8rem;
            margin-bottom: 6px;
        }
        /* Status indicators */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }
        .status-dot.success {
            background-color: var(--success-color);
        }
        .status-dot.warning {
            background-color: var(--warning-color);
        }
        .status-dot.danger {
            background-color: var(--danger-color);
        }
        /* Stats cards */
        .stat-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 6px;
            text-align: center;
            height: 100%;
        }
        .stat-icon {
            font-size: 1.2rem;
            margin-bottom: 3px;
        }
        .stat-number {
            font-size: 1rem;
            font-weight: 700;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.7rem;
        }
        /* Progress bar */
        .progress {
            height: 5px;
            border-radius: 2px;
            background-color: #e9ecef;
            margin-bottom: 3px;
        }
        /* Top section container */
        .top-section {
            margin-bottom: 10px;
        }
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .top-section {
                margin-bottom: 8px;
            }
            .compact-card {
                min-height: auto; /* Allow cards to size to content on smaller screens */
            }
        }
        @media (max-width: 768px) {
            .btn {
                padding: 3px 6px;
                font-size: 0.75rem;
            }
            .action-buttons-cell {
                flex-direction: column;
                gap: 2px;
            }
            .table th, .table td {
                padding: 4px 3px;
                font-size: 0.75rem;
            }
            .info-item {
                flex-direction: column;
                gap: 2px;
            }
        }
    </style>
</head>
<body>
    <?php
    include('header.php');
    include('sidebar.php');
    ?>
    
    <div class="main-content" style="margin-top: 70px;">
        <div class="container">
            <!-- Page Title -->
            <div class="row mb-2">
                <div class="col-12">
                    <h2 class="mb-2" style="font-size: 1.3rem;">Certificate Management</h2>
                    
                    <!-- Display Messages -->
                    <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Top Section with Compact Cards -->
            <div class="row top-section g-2">
                <!-- File Selection Card -->
                <div class="col-md-4">
                    <div class="card compact-card">
                        <div class="card-header">
                            <h4><i class="fas fa-file-alt"></i> Selected Sheet</h4>
                        </div>
                        <div class="card-body p-2">
                            <?php if ($selected_file_id > 0): ?>
                                <!-- File Information -->
                                <div class="file-info">
                                    <div class="info-item">
                                        <span class="info-label">Sheet:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($files[$selected_file_id]['file_name']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Upload:</span>
                                        <span class="info-value"><?php echo date('M d, Y', strtotime($files[$selected_file_id]['upload_date'])); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Clients:</span>
                                        <span class="info-value"><?php echo count($clients); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Template:</span>
                                        <span class="info-value">
                                            <?php if ($files[$selected_file_id]['cert_name']): ?>
                                                <span class="badge bg-success"><?php echo htmlspecialchars($files[$selected_file_id]['cert_name']); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Not Assigned</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-end mt-1">                              
                                    <?php if ($files[$selected_file_id]['cert_type']): ?>
                                    <button type="button" class="btn btn-sm btn-primary ms-1" data-bs-toggle="modal" data-bs-target="#changeCertificateModal">
                                        <i class="fas fa-edit"></i> Change Template
                                    </button>
                                    <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-primary ms-1" data-bs-toggle="modal" data-bs-target="#assignCertificateModal">
                                        <i class="fas fa-plus"></i> Assign Template
                                    </button>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <!-- No File Selected -->
                                <div class="empty-state">
                                    <i class="fas fa-file-image"></i>
                                    <h5>No Sheet Selected</h5>
                                    
                                    <div class="mt-2">
                                        <select id="file_selector" class="form-select form-select-sm" onchange="window.location.href='client_certificates.php?file_id='+this.value">
                                            <option value="">-- Select Sheet --</option>
                                            <?php foreach ($files as $file): ?>
                                            <option value="<?php echo $file['id']; ?>">
                                                <?php echo htmlspecialchars($file['file_name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Certificate Status Card -->
                <div class="col-md-4">
                    <?php if ($selected_file_id > 0): ?>
                    <div class="card compact-card">
                        <div class="card-header">
                            <h4><i class="fas fa-chart-pie"></i> Certificate Status</h4>
                        </div>
                        <div class="card-body p-2">
                            <div class="row g-1 mb-2">
                                <div class="col-4">
                                    <div class="stat-card">
                                        <div class="stat-number text-success"><?php echo $generated_count; ?></div>
                                        <div class="stat-label">Generated</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-card">
                                        <div class="stat-number text-warning"><?php echo $total_count - $generated_count; ?></div>
                                        <div class="stat-label">Pending</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-card">
                                        <div class="stat-number text-primary"><?php echo $total_count; ?></div>
                                        <div class="stat-label">Total</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small" style="font-size: 0.7rem;">Progress</span>
                                    <span class="small" style="font-size: 0.7rem;"><?php echo $generation_percentage; ?>%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar <?php echo $generated_count == $total_count ? 'bg-success' : ($generated_count > 0 ? 'bg-warning' : 'bg-secondary'); ?>" 
                                        role="progressbar" 
                                        style="width: <?php echo $generation_percentage; ?>%" 
                                        aria-valuenow="<?php echo $generation_percentage; ?>" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($files[$selected_file_id]['cert_type']): ?>
                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between mt-1">
                                <?php if ($generated_count < $total_count): ?>
                                <a href="generate_all_certificates.php?file_id=<?php echo $selected_file_id; ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-pdf"></i> 
                                    <?php echo $generated_count > 0 ? 'Continue' : 'Generate All'; ?>
                                </a>
                                <?php else: ?>
                                <a href="generate_all_certificates.php?file_id=<?php echo $selected_file_id; ?>" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-check-circle"></i> View All
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($generated_count > 0): ?>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#regenerateCertificatesModal">
                                    <i class="fas fa-redo"></i> Regenerate
                                </button>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="card compact-card">
                        <div class="card-header">
                            <h4><i class="fas fa-chart-pie"></i> Certificate Status</h4>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center p-2">
                            <div class="empty-state">
                                <i class="fas fa-chart-bar"></i>
                                <h5>No Data Available</h5>
                                <p>Select a sheet to view status</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Certificate Preview Card -->
                <div class="col-md-4">
                    <div class="card compact-card">
                        <div class="card-header">
                            <h4><i class="fas fa-certificate"></i> Certificate Preview</h4>
                        </div>
                        <div class="card-body p-0">
                            <?php if ($selected_file_id > 0 && $files[$selected_file_id]['cert_type']): ?>
                                <div class="certificate-preview-container">
                                    <?php if (!empty($files[$selected_file_id]['cert_file_path'])): ?>
                                    <a href="certificate_preview.php?file_id=<?php echo $selected_file_id; ?>">
                                        <img src="<?php echo htmlspecialchars($files[$selected_file_id]['cert_file_path']); ?>"
                                             alt="<?php echo htmlspecialchars($files[$selected_file_id]['cert_name']); ?>"
                                             class="certificate-preview">
                                    </a>
                                    <?php else: ?>
                                    <div class="alert alert-info w-100 text-center m-1" style="font-size: 0.75rem;">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Preview not available
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($selected_file_id > 0): ?>
                                <div class="d-flex flex-column justify-content-center align-items-center h-100">
                                    <div class="empty-state">
                                        <i class="fas fa-certificate"></i>
                                        <h5>No Template Assigned</h5>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignCertificateModal">
                                            <i class="fas fa-plus"></i> Assign Template
                                        </button>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="d-flex flex-column justify-content-center align-items-center h-100">
                                    <div class="empty-state">
                                        <i class="fas fa-file-alt"></i>
                                        <h5>No Sheet Selected</h5>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Clients List Section (Full Width) -->
            <div class="row g-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-users"></i> Clients</h4>
                            <?php if ($total_count > 0): ?>
                            <span class="badge bg-primary"><?php echo $total_count; ?> Total</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <?php if ($selected_file_id > 0): ?>
                                <?php if (count($clients) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">#</th>
                                                <th style="width: 30%">Client Name</th>
                                                <th style="width: 20%">Reference ID</th>
                                                <th style="width: 15%">Email</th>
                                                <th style="width: 15%">Status</th>
                                                <th style="width: 15%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($clients as $index => $client): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td class="text-start">
                                                    <div class="client-name"><?php echo htmlspecialchars($client['client_name']); ?></div>
                                                </td>
                                                <td><?php echo htmlspecialchars($client['reference_id']); ?></td>
                                                <td><?php echo htmlspecialchars($client['email'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php if (!empty($client['file_path'])): ?>
                                                        <span class="badge bg-success">Generated</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="action-buttons-cell">
                                                        <?php if (!empty($client['file_path'])): ?>
                                                        <a href="<?php echo htmlspecialchars($client['file_path']); ?>" class="btn btn-primary btn-sm" target="_blank" title="View Certificate">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php endif; ?>
                                                        <button type="button" class="btn btn-danger btn-sm delete-client-btn"
                                                                data-id="<?php echo $client['id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($client['client_name']); ?>"
                                                                title="Delete Client">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="empty-state py-3">
                                    <i class="fas fa-users"></i>
                                    <h5>No Clients Found</h5>
                                    <p>No clients have been uploaded for this file yet.</p>
                                </div>
                                <?php endif; ?>
                            <?php else: ?>
                            <div class="empty-state py-3">
                                <i class="fas fa-file-alt"></i>
                                <h5>No Sheet Selected</h5>
                                <p>Please select a sheet to view clients</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Modal for Certificate Generation -->
    <div class="modal fade" id="generationProgressModal" tabindex="-1" aria-labelledby="generationProgressModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 py-2">
                    <h5 class="modal-title" id="generationProgressModalLabel" style="font-size: 0.9rem;">
                                                        <i class="fas fa-cog fa-spin me-2"></i> Generating
                    </h5>
                </div>
                <div class="modal-body text-center py-2">
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                            role="progressbar" style="width: 0%" id="generationProgress">
                            0%
                        </div>
                    </div>
                    <p class="mb-0 small" style="font-size: 0.75rem;">Please wait while we generate your certificates...</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Assign Certificate Modal -->
    <div class="modal fade" id="assignCertificateModal" tabindex="-1" aria-labelledby="assignCertificateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="assignCertificateModalLabel" style="font-size: 0.9rem;">Assign Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <div class="modal-body py-2">
                        <input type="hidden" name="action" value="assign_certificate">
                        <input type="hidden" name="file_id" value="<?php echo $selected_file_id; ?>">
                        
                        <div class="mb-2">
                            <label for="cert_id" class="form-label" style="font-size: 0.8rem;">Select Certificate Template</label>
                            <select class="form-select form-select-sm" id="cert_id" name="cert_id" required>
                                <option value="">Choose template...</option>
                                <?php foreach ($certificates as $cert): ?>
                                <option value="<?php echo $cert['id']; ?>"><?php echo htmlspecialchars($cert['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="alert alert-info py-1 px-2 mb-0" style="font-size: 0.75rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            This template will be used for all clients.
                        </div>
                    </div>
                    <div class="modal-footer py-1">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Change Certificate Modal -->
    <div class="modal fade" id="changeCertificateModal" tabindex="-1" aria-labelledby="changeCertificateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="changeCertificateModalLabel" style="font-size: 0.9rem;">Change Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <div class="modal-body py-2">
                        <input type="hidden" name="action" value="assign_certificate">
                        <input type="hidden" name="file_id" value="<?php echo $selected_file_id; ?>">
                        
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.8rem;">Current Template</label>
                            <div class="alert alert-info py-1 px-2 mb-2" style="font-size: 0.75rem;">
                                <strong><?php echo htmlspecialchars($files[$selected_file_id]['cert_name'] ?? 'None'); ?></strong>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label for="change_cert_id" class="form-label" style="font-size: 0.8rem;">Select New Template</label>
                            <select class="form-select form-select-sm" id="change_cert_id" name="cert_id" required>
                                <option value="">Choose template...</option>
                                <?php foreach ($certificates as $cert): ?>
                                <option value="<?php echo $cert['id']; ?>" <?php echo ($cert['id'] == $files[$selected_file_id]['cert_type']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cert['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="alert alert-warning py-1 px-2 mb-0" style="font-size: 0.75rem;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Changing template requires regenerating all certificates.
                        </div>
                    </div>
                    <div class="modal-footer py-1">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-warning">Change</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Client Modal -->
    <div class="modal fade" id="deleteClientModal" tabindex="-1" aria-labelledby="deleteClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="deleteClientModalLabel" style="font-size: 0.9rem;">Delete Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="deleteClientForm">
                    <div class="modal-body py-2">
                        <input type="hidden" name="action" value="delete_client">
                        <input type="hidden" name="file_id" value="<?php echo $selected_file_id; ?>">
                        <input type="hidden" name="client_id" id="deleteClientId">
                        
                        <div class="alert alert-danger py-1 px-2 mb-2" style="font-size: 0.75rem;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Warning:</strong> This action cannot be undone!
                        </div>
                        
                        <p style="font-size: 0.8rem;">Delete client <strong id="deleteClientName"></strong>?</p>
                        <p class="text-muted mb-0" style="font-size: 0.75rem;">This will also delete any generated certificate file.</p>
                    </div>
                    <div class="modal-footer py-1">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Regenerate Certificates Modal -->
    <div class="modal fade" id="regenerateCertificatesModal" tabindex="-1" aria-labelledby="regenerateCertificatesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title" id="regenerateCertificatesModalLabel" style="font-size: 0.9rem;">Regenerate Certificates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="regenerateCertificatesForm">
                    <div class="modal-body py-2">
                        <input type="hidden" name="action" value="regenerate_certificates">
                        <input type="hidden" name="file_id" value="<?php echo $selected_file_id; ?>">
                        
                        <div class="alert alert-warning py-1 px-2 mb-2" style="font-size: 0.75rem;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Warning:</strong> This will delete all existing certificates.
                        </div>
                        
                        <p style="font-size: 0.8rem;">Regenerate all certificates?</p>
                        <ul class="text-muted mb-0" style="font-size: 0.75rem; padding-left: 1rem;">
                            <li>Delete existing certificate files</li>
                            <li>Clear certificate paths</li>
                            <li>Generate new certificates</li>
                        </ul>
                    </div>
                    <div class="modal-footer py-1">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-warning">Regenerate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap and jQuery Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Handle delete client button click
            $('.delete-client-btn').click(function() {
                const clientId = $(this).data('id');
                const clientName = $(this).data('name');
                
                $('#deleteClientId').val(clientId);
                $('#deleteClientName').text(clientName);
                
                // Show modal
                new bootstrap.Modal(document.getElementById('deleteClientModal')).show();
            });
            
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert:not(.modal .alert)').fadeOut('slow');
            }, 5000);
            
            // Add confirmation for certificate template changes
            $('#changeCertificateModal form').submit(function(e) {
                const currentCert = '<?php echo htmlspecialchars($files[$selected_file_id]['cert_type'] ?? ''); ?>';
                const newCert = $('#change_cert_id').val();
                
                if (currentCert && currentCert !== newCert) {
                    if (!confirm('Are you sure you want to change the certificate template? This will require regenerating all certificates.')) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
            
            // Add tooltips to status badges
            $('[title]').tooltip();
            
            // Handle certificate preview link
            $('a[href*="certificate_preview.php"]').click(function(e) {
                const hasTemplate = '<?php echo $files[$selected_file_id]['cert_type'] ?? '' ? 'true' : 'false'; ?>';
                if (hasTemplate === 'false') {
                    e.preventDefault();
                    alert('Please assign a certificate template first.');
                }
            });
            
            // Handle generate certificates link
            $('a[href*="generate_all_certificates.php"]').click(function(e) {
                const hasTemplate = '<?php echo $files[$selected_file_id]['cert_type'] ?? '' ? 'true' : 'false'; ?>';
                const clientCount = <?php echo count($clients); ?>;
                
                if (hasTemplate === 'false') {
                    e.preventDefault();
                    alert('Please assign a certificate template first.');
                    return;
                }
                
                if (clientCount === 0) {
                    e.preventDefault();
                    alert('No clients found to generate certificates for.');
                    return;
                }
                
                // Show progress modal
                const progressModal = new bootstrap.Modal(document.getElementById('generationProgressModal'));
                progressModal.show();
                
                // Simulate progress (you can replace this with actual progress tracking)
                let progress = 0;
                const progressInterval = setInterval(function() {
                    progress += Math.random() * 15;
                    if (progress > 90) progress = 90; // Don't complete until actual completion
                    
                    $('#generationProgress').css('width', progress + '%').text(Math.round(progress) + '%');
                }, 500);
                
                // Store interval ID to clear it later
                window.generationProgressInterval = progressInterval;
                
                // Allow the link to proceed
                return true;
            });
            
            // Validate form submissions
            $('form').submit(function(e) {
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                
                // Disable submit button to prevent double submission
                submitBtn.prop('disabled', true);
                
                // Re-enable after 3 seconds in case of errors
                setTimeout(function() {
                    submitBtn.prop('disabled', false);
                }, 3000);
            });
            
            // Enhanced file selector with loading state
            $('#file_selector').change(function() {
                const selectedValue = $(this).val();
                if (selectedValue) {
                    // Show loading state
                    $(this).prop('disabled', true);
                    $('body').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status" style="width: 1.5rem; height: 1.5rem;"><span class="visually-hidden">Loading...</span></div></div>');
                    
                    // Add loading overlay styles
                    $('<style>')
                        .prop('type', 'text/css')
                        .html(`
                            .loading-overlay {
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                background-color: rgba(255, 255, 255, 0.8);
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                z-index: 9999;
                            }
                        `)
                        .appendTo('head');
                    
                    // Navigate to new page
                    window.location.href = 'client_certificates.php?file_id=' + selectedValue;
                }
            });
            
            // Initialize tooltips for all elements with title attribute
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], [title]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    delay: { show: 500, hide: 100 },
                    container: 'body'
                });
            });
            
            // Adjust table responsiveness
            function adjustTableResponsiveness() {
                const tableContainer = $('.table-responsive');
                if (tableContainer.width() < 500) {
                    $('.table th, .table td').css('font-size', '0.7rem');
                    $('.table th, .table td').css('padding', '3px 2px');
                } else {
                    $('.table th, .table td').css('font-size', '');
                    $('.table th, .table td').css('padding', '');
                }
            }
            
            // Call on load and resize
            adjustTableResponsiveness();
            $(window).resize(adjustTableResponsiveness);
            
            // Fix for modals on mobile
            $('.modal').on('shown.bs.modal', function() {
                if ($(window).width() < 768) {
                    $(this).find('.modal-dialog').css({
                        'margin-top': '10px',
                        'margin-bottom': '10px'
                    });
                }
            });
            
            // Ensure card heights are properly adjusted
            function adjustCardHeights() {
                // Reset heights first
                $('.compact-card').css('height', 'auto');
                
                // Only equalize heights on larger screens
                if ($(window).width() >= 768) {
                    let maxHeight = 0;
                    $('.compact-card').each(function() {
                        const height = $(this).outerHeight();
                        maxHeight = Math.max(maxHeight, height);
                    });
                    
                    // Add a little extra space to prevent content from being cut off
                    $('.compact-card').css('height', (maxHeight + 5) + 'px');
                }
            }
            
            // Call on document ready and window resize
            setTimeout(adjustCardHeights, 100); // Slight delay to ensure content is rendered
            $(window).resize(adjustCardHeights);
        });
    </script>
    
    <?php include('footer.php'); ?>
</body>
</html>

