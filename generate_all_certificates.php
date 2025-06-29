<?php
session_start();
include('dbcon.php');
require 'vendor/autoload.php';
use TCPDF as TCPDF;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

// Function to update progress
function updateProgress($current, $total, $clientName = '', $status = 'generating', $message = '') {
    $_SESSION['certificate_progress'] = [
        'total' => $total,
        'current' => $current,
        'current_client' => $clientName,
        'status' => $status,
        'message' => $message,
        'percentage' => $total > 0 ? round(($current / $total) * 100, 1) : 0
    ];
    
    // Force session write
    session_write_close();
    session_start();
}

// Increase execution time and memory for large batches
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '512M');

// Default positioning settings
$defaultSettings = [
    'ref_id_x' => 242,
    'ref_id_y' => 12,
    'ref_id_size' => 13,
    'ref_id_color' => '0,0,0',
    'client_name_x' => 'center',
    'client_name_y' => 'middle',
    'client_name_size' => 25,
    'client_name_color' => '38,61,128'
];

// Initialize variables
$file_data = null;
$clients = [];
$zip_filename = '';

// Validate file_id
if (!isset($_GET['file_id']) || empty($_GET['file_id'])) {
    die("Error: No file selected.");
}
$file_id = intval($_GET['file_id']);

// Check if certificates were already generated
if (isset($_SESSION['certificates_generated']) && $_SESSION['certificates_generated'] == $file_id) {
    $display_only = true;
    
    $zip_query = "SELECT zip_file FROM files WHERE id = ?";
    $stmt = $conn->prepare($zip_query);
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $zip_result = $stmt->get_result();
    $zip_data = $zip_result->fetch_assoc();
    $zip_filename = $zip_data['zip_file'] ?? '';
} else {
    $display_only = false;
    $template = $_SESSION['certificate_template'] ?? null;
}

// Fetch file info and certificate data
$file_query = "SELECT f.id, f.file_name, f.cert_type, f.zip_file, 
               c.id as cert_id, c.name as cert_name, c.file_path as cert_file_path, c.positioning_data
               FROM files f
               LEFT JOIN certificates c ON f.cert_type = c.id
               WHERE f.id = ?";
$stmt = $conn->prepare($file_query);
$stmt->bind_param("i", $file_id);
$stmt->execute();
$file_result = $stmt->get_result();
$file_data = $file_result->fetch_assoc();

if (!$file_data) {
    die("Error: File not found.");
}

if (!$display_only && empty($file_data['cert_file_path'])) {
    $_SESSION['certificate_message'] = "Please assign a certificate template first before generating certificates.";
    $_SESSION['certificate_message_type'] = "warning";
    header("Location: client_certificates.php?file_id=" . $file_id);
    exit();
}

// Parse positioning data
$positioning_data = $defaultSettings;
if (!empty($file_data['positioning_data'])) {
    $parsed_data = json_decode($file_data['positioning_data'], true);
    if ($parsed_data) {
        $positioning_data = array_merge($defaultSettings, $parsed_data);
    }
}

// Get clients
$client_query = "SELECT id, client_name, reference_id, email, file_path FROM clients WHERE file_id = ? ORDER BY client_name ASC";
$stmt = $conn->prepare($client_query);
$stmt->bind_param("i", $file_id);
$stmt->execute();
$client_result = $stmt->get_result();
$clients = [];
while ($row = $client_result->fetch_assoc()) {
    $clients[] = $row;
}

if (count($clients) === 0) {
    die("Error: No clients found for this file.");
}

// OPTIMIZED CERTIFICATE GENERATION WITH PROGRESS TRACKING
if (!$display_only) {
    $total_clients = count($clients);
    
    // Initialize progress
    updateProgress(0, $total_clients, '', 'starting', 'Initializing certificate generation...');
    
    $output_dir = 'uploads';
    if (!file_exists(__DIR__ . '/' . $output_dir)) {
        mkdir(__DIR__ . '/' . $output_dir, 0777, true);
    }
    
    updateProgress(0, $total_clients, '', 'preparing', 'Creating ZIP archive...');
    
    $zip_filename = $output_dir . '/' . $file_id . '_' . time() . '.zip';
    $zip = new ZipArchive();
    if ($zip->open($zip_filename, ZipArchive::CREATE) !== TRUE) {
        updateProgress(0, $total_clients, '', 'error', 'Error: Cannot create ZIP archive.');
        die("Error: Cannot create ZIP archive.");
    }
    
    // Check template file exists once
    $template_path = $file_data['cert_file_path'];
    if (!file_exists($template_path)) {
        updateProgress(0, $total_clients, '', 'error', 'Certificate template file not found.');
        die("Error: Certificate template file not found: " . $template_path);
    }
    
    updateProgress(0, $total_clients, '', 'generating', 'Starting certificate generation...');
    
    // Prepare batch update query
    $batch_updates = [];
    $update_stmt = $conn->prepare("UPDATE clients SET file_path = ? WHERE id = ?");
    
    // Process clients with progress tracking
    foreach ($clients as $index => $client) {
        $current_number = $index + 1;
        $client_name = strtoupper($client['client_name']);
        $reference_id = $client['reference_id'];
        
        // Update progress for current client
        updateProgress(
            $current_number, 
            $total_clients, 
            $client['client_name'], 
            'generating', 
            "Generating certificate {$current_number} of {$total_clients}"
        );
        
        // Create PDF with optimized settings
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Certificate Generator');
        $pdf->SetAuthor('DTI');
        $pdf->SetTitle('Certificate for ' . $client_name);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();
        
        // Use lower DPI for faster processing
        $pdf->Image($template_path, 0, 0, 297, 210, '', '', '', false, 150, '', false, false, 0);
        
        // Reference ID
        $ref_id_color = explode(',', $positioning_data['ref_id_color']);
        $pdf->SetFont('helvetica', '', $positioning_data['ref_id_size']);
        $pdf->SetTextColor($ref_id_color[0], $ref_id_color[1], $ref_id_color[2]);
        $pdf->SetXY($positioning_data['ref_id_x'], $positioning_data['ref_id_y']);
        $pdf->Cell(40, 10, $reference_id, 0, 1, 'L');
        
        // Client name with proper font family handling
        $client_name_color = explode(',', $positioning_data['client_name_color']);
        
        // Get font family from positioning data or use default
        $font_family = isset($positioning_data['client_name_font_family']) ? $positioning_data['client_name_font_family'] : 'Times New Roman';
        
        // Map font families to TCPDF compatible fonts
        $tcpdf_font = 'times'; // default
        switch($font_family) {
            case 'Times New Roman':
                $tcpdf_font = 'times';
                break;
            case 'Arial':
                $tcpdf_font = 'helvetica';
                break;
            case 'Georgia':
                $tcpdf_font = 'times'; // Georgia fallback to Times
                break;
            case 'Verdana':
                $tcpdf_font = 'helvetica'; // Verdana fallback to Helvetica
                break;
            case 'Helvetica':
                $tcpdf_font = 'helvetica';
                break;
            case 'Courier New':
                $tcpdf_font = 'courier';
                break;
            case 'Poppins':
                $tcpdf_font = 'helvetica'; // Poppins fallback to Helvetica
                break;
            case 'Playfair Display':
                $tcpdf_font = 'times'; // Playfair Display fallback to Times
                break;
            case 'Montserrat':
                $tcpdf_font = 'helvetica'; // Montserrat fallback to Helvetica
                break;
            case 'Roboto':
                $tcpdf_font = 'helvetica'; // Roboto fallback to Helvetica
                break;
            case 'Droid Serif':
                $tcpdf_font = 'times'; // Droid Serif fallback to Times (since it's a serif font)
            default:
                $tcpdf_font = 'times';
        }
        
        $pdf->SetFont($tcpdf_font, 'B', $positioning_data['client_name_size']);
        $pdf->SetTextColor($client_name_color[0], $client_name_color[1], $client_name_color[2]);
        $pdf->SetDrawColor($client_name_color[0], $client_name_color[1], $client_name_color[2]);
        
        // Handle positioning
        if ($positioning_data['client_name_x'] === 'center') {
            $client_name_width = $pdf->GetStringWidth($client_name);
            $client_name_x = (($pdf->getPageWidth() - $client_name_width) / 2);
        } else {
            $client_name_x = floatval($positioning_data['client_name_x']);
        }

        if ($positioning_data['client_name_y'] === 'middle') {
            $client_name_y = ($pdf->getPageHeight() / 2 - 5);
        } else {
            $client_name_y = floatval($positioning_data['client_name_y']);
        }
        
        $pdf->SetXY($client_name_x, $client_name_y);
        $pdf->Cell($pdf->GetStringWidth($client_name), 10, $client_name, 'B', 1, 'C');
        
        // Save PDF
        $safe_client_name = preg_replace('/[^a-zA-Z0-9]/', '_', $client_name);
        $output_filename = $output_dir . '/certificate_' . $client['reference_id'] . '_' . $safe_client_name . '.pdf';
        $pdf->Output(__DIR__ . '/' . $output_filename, 'F');
        
        // Generate URL
        $filePath = $output_filename;
        if (!preg_match('/^https?:\/\//', $filePath)) {
            $baseDir = dirname($_SERVER['SCRIPT_NAME']);
            $baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            if ($baseDir == '/') $baseDir = '';
            $filePath = $baseURL . $baseDir . '/' . ltrim($filePath, '/');
        }
        
        // Add to ZIP
        $zip->addFile($output_filename, basename($output_filename));
        
        // Store for batch update
        $batch_updates[] = [$filePath, $client['id']];
        $clients[$index]['file_path'] = $filePath;
        
        // Update database for this client
        $update_stmt->bind_param("si", $filePath, $client['id']);
        $update_stmt->execute();
        
        // Clean up PDF object to free memory
        unset($pdf);
        
        // Small delay to allow progress to be read
        usleep(100000); // 0.1 second
        
        // Force garbage collection every 10 certificates
        if ($current_number % 10 === 0 && function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    
    updateProgress($total_clients, $total_clients, '', 'finalizing', 'Finalizing ZIP archive...');
    
    $zip->close();
    
    // Update ZIP path in DB
    $update_zip_query = "UPDATE files SET zip_file = ? WHERE id = ?";
    $stmt = $conn->prepare($update_zip_query);
    $stmt->bind_param("si", $zip_filename, $file_id);
    $stmt->execute();
    
    // Mark as completed
    updateProgress($total_clients, $total_clients, '', 'completed', 'All certificates generated successfully!');
    
    $_SESSION['certificates_generated'] = $file_id;
} else {
    // Display mode - fetch clients with file paths
    $client_query = "SELECT id, client_name, reference_id, email, file_path FROM clients WHERE file_id = ? ORDER BY client_name ASC";
    $stmt = $conn->prepare($client_query);
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $client_result = $stmt->get_result();
    $clients = [];
    while ($row = $client_result->fetch_assoc()) {
        $clients[] = $row;
    }
}

// Process messages
$message = '';
$messageType = '';
if (isset($_SESSION['certificate_message'])) {
    $message = $_SESSION['certificate_message'];
    $messageType = $_SESSION['certificate_message_type'] ?? 'info';
    unset($_SESSION['certificate_message']);
    unset($_SESSION['certificate_message_type']);
}

$conn->close();

$pageTitle = "Certificate Generation";
$currentPage = "Certificate Generation";
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
            --border-radius: 8px;
            --box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        body {
            background-color: #f5f7fa;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            color: #333;
            line-height: 1.6;
            overflow-y: auto !important; /* Force vertical scrolling */
            min-height: 100vh;
        }
        .main-content {
            padding: 30px 0;
        }
        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }
        
        .back-button {
            background-color: #01043A !important;
            color: white !important;
        }
        
        .back-button:hover {
            background-color: #0038A8 !important;
            color: white;
        }
        /* Card Styling */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
            margin-bottom: 30px;
        }
        .card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }
        .card-header {
            background-color: #01043A !important;
            color: white;
            font-weight: 600;
            padding: 15px 20px;
            border-bottom: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-header h4 {
            margin-bottom: 0;
            color: white;
            font-weight: 600;
        }
        .card-header i {
            margin-right: 10px;
        }
        .card-body {
            padding: 25px;
        }
        /* Buttons */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 8px 16px;
            transition: var(--transition);
            margin: 5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn i {
            font-size: 1.1em;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {                    
            background-color: #162a78;
            border-color: #162a78;
            transform: translateY(-2px);
        }
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
            transform: translateY(-2px);
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
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
            transform: translateY(-2px);
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.85rem;
        }
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        .btn-outline-success {
            color: var(--success-color);
            border-color: var(--success-color);
        }
        .btn-outline-success:hover {
            background-color: var(--success-color);
            color: white;
        }
        .btn-outline-warning {
            color: #212529;
            border-color: var(--warning-color);
        }
        .btn-outline-warning:hover {
            background-color: var(--warning-color);
            color: #212529;
        }
        /* Table Styling */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .table th {
            background-color: #01043A !important;
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 14px 10px;
            border: none;
            text-align: center;
        }
        .table td {
            padding: 12px 10px;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(236, 240, 241, 0.5);
        }
        .table-responsive {
            border-radius: var(--border-radius);
            overflow-x: auto; /* Only horizontal scroll for table */
            overflow-y: visible; /* Allow vertical content to flow */
            box-shadow: var(--box-shadow);
            max-height: none; /* Remove any height restrictions */
        }
        /* Form Elements */
        .form-check-input {
            width: 18px;
            height: 18px;
            margin-top: 0;
            cursor: pointer;
        }
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .form-check-label {
            cursor: pointer;
            user-select: none;
        }
        
        .form-check {
        margin-bottom: 0;
        margin-right: 15px;
    }
        /* Action Buttons Section */
        .action-buttons {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding-left: 15px;
            padding-right: 15px;
            white-space: nowrap;
        }
            /* Add this to your existing CSS */
        .select-all-container {
            display: flex;
            align-items: center;
            background-color: white;
            padding: 8px 15px;
            border-radius: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-right: auto;
        }
        .selected-count {
            font-size: 0.9rem;
            color: var(--primary-color);
            font-weight: 600;
            margin-top: 0 !important;
        }
        /* For smaller screens, make buttons full width */
        @media (max-width: 768px) {
            .action-buttons .btn {
                width: 100%;
                min-width: 100%;
                margin: 5px 0;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }
            
            .select-all-container {
                margin-bottom: 15px;
                width: 100%;
            }
        }
        /* Alerts */
        .alert {
            border-radius: var(--border-radius);
            padding: 15px 20px;
            margin-bottom: 25px;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
            padding-right: 40px;
        }
        
        .btn-close {
            position: absolute;
            right: 10px;
            top: 50%;
            opacity: 0.8;
        }
        
        .btn-close:hover {
            opacity: 1;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid var(--warning-color);
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid var(--success-color);
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }
        /* Modal Styling */
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 15px 20px;
        }
        .modal-title {
            font-weight: 600;
        }
        .modal-body {
            padding: 25px;
        }
        .modal-footer {
            border-top: 1px solid #eee;
            padding: 15px 20px;
        }
        /* Client info styling */
        .client-name {
            font-weight: 600;
            color: var(--primary-color);
            text-align: left;
        }
        .client-email {
            color: #6c757d;
            font-size: 0.85rem;
            text-align: left;
        }
        /* Badge styling */
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            border-radius: 30px;
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
        /* Action buttons */
        .action-buttons-cell {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .table th, .table td {
                padding: 10px 5px;
                font-size: 0.9rem;
            }
            
            .d-flex.flex-nowrap {
                flex-wrap: wrap !important;
            }
            
            .btn-sm {
                padding: 6px 8px;
                margin: 2px;
            }
            .action-buttons-cell {
                flex-direction: column;
            }
            
            .page-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <?php
    include('header.php');
    include('sidebar.php');
    ?>
    

    
    <div class="main-content" style="margin-top: 120px;">
        <div class="container">
            <!-- Page Header with Back Button -->
            <div class="page-header">
                <h2 class="page-title">Certificates for: <?php echo htmlspecialchars($file_data['file_name']); ?></h2>
                <a href="client_certificates.php?file_id=<?php echo $file_id; ?>" class="btn back-button">
                    <i class="fas fa-arrow-left me-2"></i> Back to Certificate Management
                </a>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-file-pdf me-2"></i> Certificate Management</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!$display_only): ?>
                    <div class="alert alert-success alert-dismissible fade show success-animation" role="alert">
                        <p class="mb-0"><i class="fas fa-check-circle me-2"></i> <strong>Success!</strong> Generated certificates for file: <b><?php echo htmlspecialchars($file_data['file_name'] ?? 'Unknown'); ?></b></p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <div class="action-buttons mb-4">
                        <div class="select-all-container">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">Select All</label>
                            </div>
                            <div class="selected-count" id="selectedCount">0 selected</div>
                        </div>
                        
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" id="sendSelectedBtn" disabled>
                                <i class="fas fa-envelope me-1"></i> Send Selected
                            </button>
                            
                            <button type="button" class="btn btn-success btn-sm" id="downloadSelectedBtn" disabled>
                                <i class="fas fa-download me-1"></i> Download Selected
                            </button>
                            
                            <?php if (!empty($zip_filename) && file_exists($zip_filename)): ?>
                            <a href="<?php echo htmlspecialchars($zip_filename); ?>" class="btn btn-warning btn-sm" download>
                                <i class="fas fa-file-archive me-1"></i> Download All (ZIP)
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 5%">No.</th>
                                    <th style="width: 5%"></th>
                                    <th style="width: 15%">Reference ID</th>
                                    <th style="width: 30%">Client Name</th>
                                    <th style="width: 15%">Status</th>
                                    <th style="width: 30%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $counter = 1;
                                foreach ($clients as $client):
                                ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input client-checkbox" type="checkbox"
                                                   value="<?php echo $client['id']; ?>"
                                                   data-path="<?php echo htmlspecialchars($client['file_path']); ?>">
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($client['reference_id']); ?></td>
                                    <td>
                                        <div class="client-name"><?php echo htmlspecialchars($client['client_name']); ?></div>
                                        <div class="client-email"><?php echo htmlspecialchars($client['email']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Generated</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons-cell">
                                            <a href="<?php echo htmlspecialchars($client['file_path']); ?>" class="btn btn-primary btn-sm" target="_blank">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                            <a href="<?php echo htmlspecialchars($client['file_path']); ?>" class="btn btn-success btn-sm" download>
                                                <i class="fas fa-download me-1"></i> Download
                                            </a>
                                            <button type="button" class="btn btn-warning btn-sm send-email-btn"
                                                    data-id="<?php echo $client['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($client['client_name']); ?>"
                                                    data-email="<?php echo htmlspecialchars($client['email']); ?>">
                                                <i class="fas fa-envelope me-1"></i> Send
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Your existing modals here... -->
    
    <!-- Bootstrap and jQuery Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
             // Ensure body can always scroll
            $('body').css({
                'overflow-y': 'auto',
                'overflow-x': 'hidden'
            });
            // Check if we need to show progress modal
            const urlParams = new URLSearchParams(window.location.search);
            const showProgress = <?php echo !$display_only ? 'true' : 'false'; ?>;
            
            if (showProgress) {
                showProgressModal();
                startProgressTracking();
            }
            
            // Progress tracking variables
            let progressInterval;
            let isCompleted = false;
            
            function showProgressModal() {
                $('#progressModal').fadeIn(300);
                $('body').css('overflow', 'hidden');
            }
            
            function hideProgressModal() {
                $('#progressModal').fadeOut(300);
                $('body').css('overflow', 'auto');
            }
            
            function startProgressTracking() {
                progressInterval = setInterval(function() {
                    if (!isCompleted) {
                        fetchProgress();
                    }
                }, 500); // Check every 500ms
            }
            
            function stopProgressTracking() {
                if (progressInterval) {
                    clearInterval(progressInterval);
                }
            }
            
            function fetchProgress() {
                $.ajax({
                    url: 'progress_tracker.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        updateProgressDisplay(data);
                    },
                    error: function() {
                        console.log('Error fetching progress');
                    }
                });
            }
            
            function updateProgressDisplay(progress) {
                const percentage = progress.percentage || 0;
                const current = progress.current || 0;
                const total = progress.total || 0;
                const clientName = progress.current_client || 'Initializing...';
                const status = progress.status || 'preparing';
                const message = progress.message || 'Preparing...';
                
                // Update counters
                $('#currentCount').text(current);
                $('#totalCount').text(total);
                $('#percentageCount').text(percentage + '%');
                
                // Update progress bar
                $('#progressBarFill').css('width', percentage + '%');
                $('#progressText').text(percentage + '%');
                
                // Update current client
                $('#currentClientName').text(clientName);
                $('#statusMessage').text(message);
                
                // Update title based on status
                switch(status) {
                    case 'starting':
                        $('#progressTitle').text('Initializing Certificate Generation');
                        $('.progress-icon i').removeClass().addClass('fas fa-cog fa-spin');
                        break;
                    case 'preparing':
                        $('#progressTitle').text('Preparing Certificate Generation');
                        $('.progress-icon i').removeClass().addClass('fas fa-folder-open');
                        break;
                    case 'generating':
                        $('#progressTitle').text('Generating Certificates');
                                                $('.progress-icon i').removeClass().addClass('fas fa-certificate');
                        break;
                    case 'finalizing':
                        $('#progressTitle').text('Finalizing and Creating ZIP');
                        $('.progress-icon i').removeClass().addClass('fas fa-file-archive');
                        break;
                    case 'completed':
                        $('#progressTitle').text('Certificate Generation Complete!');
                        $('.progress-icon i').removeClass().addClass('fas fa-check-circle');
                        $('.progress-content').addClass('success-animation');
                        $('#statusMessage').text('All certificates have been generated successfully!');
                        $('#progressActions').show();
                        isCompleted = true;
                        stopProgressTracking();
                        
                        // Auto-hide modal after 3 seconds
                        setTimeout(function() {
                            hideProgressModal();
                        }, 3000);
                        break;
                    case 'error':
                        $('#progressTitle').text('Error Occurred');
                        $('.progress-icon i').removeClass().addClass('fas fa-exclamation-triangle');
                        $('.progress-content').addClass('error-state');
                        $('#statusMessage').text(message || 'An error occurred during generation');
                        $('#progressActions').show();
                        isCompleted = true;
                        stopProgressTracking();
                        break;
                }
                
                // If completed, show completion state
                if (percentage >= 100 && status !== 'error') {
                    setTimeout(function() {
                        updateProgressDisplay({
                            percentage: 100,
                            current: total,
                            total: total,
                            status: 'completed',
                            message: 'All certificates generated successfully!'
                        });
                    }, 1000);
                }
            }
            
            // Simulate progress for demonstration (remove this in production)
            function simulateProgress() {
                const totalClients = <?php echo count($clients); ?>;
                let currentProgress = 0;
                const clientNames = <?php echo json_encode(array_column($clients, 'client_name')); ?>;
                
                const simulationInterval = setInterval(function() {
                    if (currentProgress <= totalClients) {
                        const percentage = Math.round((currentProgress / totalClients) * 100);
                        const status = currentProgress === 0 ? 'starting' : 
                                     currentProgress < totalClients ? 'generating' : 
                                     'finalizing';
                        
                        updateProgressDisplay({
                            percentage: percentage,
                            current: currentProgress,
                            total: totalClients,
                            current_client: clientNames[currentProgress - 1] || 'Initializing...',
                            status: status,
                            message: currentProgress === 0 ? 'Preparing to generate certificates...' :
                                   currentProgress < totalClients ? `Generating certificate for ${clientNames[currentProgress - 1]}` :
                                   'Creating ZIP archive...'
                        });
                        
                        currentProgress++;
                    } else {
                        clearInterval(simulationInterval);
                        updateProgressDisplay({
                            percentage: 100,
                            current: totalClients,
                            total: totalClients,
                            status: 'completed',
                            message: 'All certificates generated successfully!'
                        });
                    }
                }, 800); // Simulate 800ms per certificate
            }
            
            // Start simulation if showing progress
            if (showProgress) {
                setTimeout(simulateProgress, 1000);
            }
            
            // Handle select all checkbox
            $("#selectAll").change(function() {
                $(".client-checkbox").prop('checked', $(this).prop('checked'));
                updateSelectedCount();
            });
            
            // Handle individual checkboxes
            $(document).on('change', '.client-checkbox', function() {
                updateSelectedCount();
                
                // Update "Select All" checkbox
                if (!$(this).prop('checked')) {
                    $("#selectAll").prop('checked', false);
                } else {
                    // Check if all checkboxes are checked
                    if ($('.client-checkbox:checked').length === $('.client-checkbox').length) {
                        $("#selectAll").prop('checked', true);
                    }
                }
            });
            
            // Update selected count and button states
            function updateSelectedCount() {
                const selectedCount = $('.client-checkbox:checked').length;
                $("#selectedCount").text(selectedCount + ' selected');
                
                // Enable/disable buttons based on selection
                $("#sendSelectedBtn, #downloadSelectedBtn").prop('disabled', selectedCount === 0);
            }
            
            // Handle send email button click
            $(".send-email-btn").click(function() {
                const clientId = $(this).data('id');
                const clientName = $(this).data('name');
                const clientEmail = $(this).data('email');
                
                $("#emailClientId").val(clientId);
                $("#emailTo").val(clientEmail);
                
                // Replace placeholder in email message
                let emailMessage = $("#emailMessage").val();
                emailMessage = emailMessage.replace('[Client Name]', clientName);
                $("#emailMessage").val(emailMessage);
                
                // Show modal
                new bootstrap.Modal(document.getElementById('sendEmailModal')).show();
            });
            
            // Handle send selected button click
            $("#sendSelectedBtn").click(function() {
                const selectedIds = $('.client-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();
                
                $("#bulkClientIds").val(selectedIds.join(','));
                $("#bulkCount").text(selectedIds.length);
                
                // Show modal
                new bootstrap.Modal(document.getElementById('bulkSendModal')).show();
            });
            
            // Handle download selected button click
            $("#downloadSelectedBtn").click(function() {
                $('.client-checkbox:checked').each(function() {
                    const certificatePath = $(this).data('path');
                    if (certificatePath) {
                        // Create a temporary link and trigger download
                        const link = document.createElement('a');
                        link.href = certificatePath;
                        link.download = certificatePath.split('/').pop();
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                });
            });
            
            // Handle send email form submission
            $("#sendEmailBtn").click(function() {
                const form = $("#sendEmailForm")[0];
                const formData = new FormData(form);
                
                // Show loading state
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...');
                
                $.ajax({
                    url: 'send_certificate.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                // Show success message
                                showNotification('Certificate sent successfully!', 'success');
                                // Close modal
                                bootstrap.Modal.getInstance(document.getElementById('sendEmailModal')).hide();
                            } else {
                                showNotification(result.message || 'Failed to send certificate', 'error');
                            }
                        } catch (e) {
                            showNotification('Certificate sent successfully!', 'success');
                            bootstrap.Modal.getInstance(document.getElementById('sendEmailModal')).hide();
                        }
                    },
                    error: function() {
                        showNotification('Error sending certificate', 'error');
                    },
                    complete: function() {
                        $("#sendEmailBtn").prop('disabled', false).html('Send Certificate');
                    }
                });
            });
            
            // Handle bulk send form submission
            $("#bulkSendBtn").click(function() {
                const form = $("#bulkSendForm")[0];
                const formData = new FormData(form);
                
                // Show loading state
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...');
                
                $.ajax({
                    url: 'send_certificate.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                showNotification(`Certificates sent to ${result.sent_count} recipients!`, 'success');
                                bootstrap.Modal.getInstance(document.getElementById('bulkSendModal')).hide();
                            } else {
                                showNotification(result.message || 'Failed to send certificates', 'error');
                            }
                        } catch (e) {
                            showNotification('Certificates sent successfully!', 'success');
                            bootstrap.Modal.getInstance(document.getElementById('bulkSendModal')).hide();
                        }
                    },
                    error: function() {
                        showNotification('Error sending certificates', 'error');
                    },
                    complete: function() {
                        $("#bulkSendBtn").prop('disabled', false).html('Send Certificates');
                    }
                });
            });
            
            // Notification function
            function showNotification(message, type) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
                
                const notification = $(`
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                        <i class="${icon} me-2"></i>${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
                
                $('body').append(notification);
                
                // Auto-remove after 5 seconds
                setTimeout(function() {
                    notification.alert('close');
                }, 5000);
            }
            
            // Initialize selected count
            updateSelectedCount();
            
            // Handle escape key to close progress modal
            $(document).keydown(function(e) {
                if (e.keyCode === 27 && isCompleted) { // Escape key
                    hideProgressModal();
                }
            });
            
            // Prevent page refresh during generation
            if (showProgress && !isCompleted) {
                window.addEventListener('beforeunload', function(e) {
                    e.preventDefault();
                    e.returnValue = 'Certificate generation is in progress. Are you sure you want to leave?';
                });
            }
        });
    </script>
    
    <!-- Send Email Modal -->
    <div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendEmailModalLabel">Send Certificate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sendEmailForm" action="send_certificate.php" method="post">
                        <input type="hidden" name="client_id" id="emailClientId">
                        <input type="hidden" name="file_id" value="<?php echo $file_id; ?>">
                        
                        <div class="mb-3">
                            <label for="emailTo" class="form-label">Recipient</label>
                            <input type="email" class="form-control" id="emailTo" name="email_to" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="emailSubject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="emailSubject" name="email_subject"
                                   value="Your Certificate from DTI">
                        </div>
                        
                        <div class="mb-3">
                            <label for="emailMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="emailMessage" name="email_message" rows="4">Dear [Client Name],

Please find attached your certificate from the Department of Trade and Industry.

Thank you for your participation!

Best regards,
DTI Team</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendEmailBtn">Send Certificate</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bulk Send Modal -->
    <div class="modal fade" id="bulkSendModal" tabindex="-1" aria-labelledby="bulkSendModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkSendModalLabel">Send Multiple Certificates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You are about to send certificates to <strong id="bulkCount">0</strong> recipients.</p>
                    <form id="bulkSendForm" action="send_certificate.php" method="post">
                        <input type="hidden" name="file_id" value="<?php echo $file_id; ?>">
                        <input type="hidden" name="client_ids" id="bulkClientIds">
                        
                        <div class="mb-3">
                            <label for="bulkEmailSubject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="bulkEmailSubject" name="email_subject"
                                   value="Your Certificate from DTI">
                        </div>
                        
                        <div class="mb-3">
                            <label for="bulkEmailMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="bulkEmailMessage" name="email_message" rows="4">Dear Participant,

Please find attached your certificate from the Department of Trade and Industry.

Thank you for your participation!

Best regards,
DTI Team</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="bulkSendBtn">Send Certificates</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('footer.php'); ?>
</body>
</html>


