<?php
include('dbcon.php');
include("logincode.php");


// Validate file_id
if (!isset($_GET['file_id']) || empty($_GET['file_id'])) {
    die("Error: No file selected.");
}
$file_id = intval($_GET['file_id']);

// Fetch file info
$file_query = "SELECT f.id, f.file_name, f.cert_type, f.zip_file, c.name as cert_name, c.file_path as cert_file_path
               FROM files f
               LEFT JOIN certificates c ON f.cert_type = c.id
               WHERE f.id = ?";
$stmt = $conn->prepare($file_query);
$stmt->bind_param("i", $file_id);
$stmt->execute();
$file_result = $stmt->get_result();
$file_data = $file_result->fetch_assoc();

if (!$file_data) die("Error: File not found.");

// Get clients with their certificate paths
$client_query = "SELECT id, client_name, reference_id, email, file_path FROM clients WHERE file_id = ? ORDER BY client_name ASC";
$stmt = $conn->prepare($client_query);
$stmt->bind_param("i", $file_id);
$stmt->execute();
$client_result = $stmt->get_result();
$clients = [];
while ($row = $client_result->fetch_assoc()) {
    $clients[] = $row;
}

if (count($clients) === 0) die("Error: No clients found for this file.");

// Check if certificates have been generated
$certificates_exist = false;
foreach ($clients as $client) {
    if (!empty($client['file_path']) && $client['file_path'] !== '#') {
        $certificates_exist = true;
        break;
    }
}

// Process any messages
$message = '';
$messageType = '';
if (isset($_SESSION['certificate_message'])) {
    $message = $_SESSION['certificate_message'];
    $messageType = $_SESSION['certificate_message_type'] ?? 'info';
    unset($_SESSION['certificate_message']);
    unset($_SESSION['certificate_message_type']);
}

// Set page-specific variables
$pageTitle = "View Certificates";
$currentPage = "Certificate Management";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/OIP.png" type="image/x-icon">
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
            overflow: hidden;
            box-shadow: var(--box-shadow);
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

        .select-all-container {
            background-color: white;
            padding: 8px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-right: auto; /* This pushes the container to the left */
            display: flex;
            align-items: center;
        }

        .selected-count {
            font-size: 0.9rem;
            color: var(--primary-color);
            font-weight: 600;
            margin-left: 10px;
        }

        /* Make buttons wider */
        .action-buttons .btn {
            min-width: 150px; /* Set minimum width for buttons */
            text-align: center;
            justify-content: center;
            white-space: nowrap;
            padding-left: 15px;
            padding-right: 15px;
        }

        /* Table cell alignment */
        .align-middle {
            vertical-align: middle !important;
        }

        .text-center {
            text-align: center !important;
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
            transform: translateY(-50%);
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
        /* Print button styling */
            .btn-info {
                background-color: #17a2b8;
                border-color: #17a2b8;
                color: white;
            }

            .btn-info:hover {
                background-color: #138496;
                border-color: #117a8b;
                transform: translateY(-2px);
            }

            .btn-outline-info {
                color: #17a2b8;
                border-color: #17a2b8;
            }

            .btn-outline-info:hover {
                background-color: #17a2b8;
                color: white;
            }

            /* Progress bar styling */
            .progress {
                height: 20px;
            }

            .progress-bar {
                background-color: var(--primary-color);
                transition: width 0.3s ease;
            }

            /* Print list styling */
            .list-group-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .print-status {
                font-size: 0.85rem;
                font-weight: 500;
            }

            .print-status.pending {
                color: #6c757d;
            }

            .print-status.printing {
                color: #17a2b8;
            }

            .print-status.completed {
                color: #28a745;
            }

            .print-status.error {
                color: #dc3545;
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
                <a href="sheet.php" class="btn btn-primary back-button">
                    <i class="fas fa-arrow-left me-2"></i> Back to Sheet Management
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
                    
                    <?php if (!$certificates_exist): ?>
                        <div class="alert alert-warning">
                            <p><strong>Notice:</strong> No certificates have been generated for this file yet.</p>
                            <a href="client_certificates.php?file_id=<?php echo $file_id; ?>" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i> Generate Certificates
                            </a>
                        </div>
                    <?php else: ?>
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
                                
                                <button type="button" class="btn btn-info btn-sm" id="printSelectedBtn" disabled>
                                    <i class="fas fa-print me-1"></i> Print Selected
                                </button>
                                
                                <button type="button" class="btn btn-warning btn-sm" id="printAllBtn" <?php echo !$certificates_exist ? 'disabled' : ''; ?>>
                                    <i class="fas fa-print me-1"></i> Print All
                                </button>
                                
                                <?php if (!empty($file_data['zip_file']) && file_exists($file_data['zip_file'])): ?>
                                <a href="<?php echo htmlspecialchars($file_data['zip_file']); ?>" class="btn btn-secondary btn-sm" download>
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
                                        <th style="width: 5%">
                                        </th>
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
                                        // Modified to only check if file_path exists in database, not physical file
                                        $has_certificate = !empty($client['file_path']) && $client['file_path'] !== '#';
                                    ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td>
                                            <?php if ($has_certificate): ?>
                                            <div class="form-check">
                                                <input class="form-check-input client-checkbox" type="checkbox" 
                                                       value="<?php echo $client['id']; ?>" 
                                                       data-path="<?php echo htmlspecialchars($client['file_path']); ?>">
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($client['reference_id']); ?></td>
                                        <td>
                                            <div class="client-name"><?php echo htmlspecialchars($client['client_name']); ?></div>
                                            <div class="client-email"><?php echo htmlspecialchars($client['email']); ?></div>
                                        </td>
                                        <td>
                                            <?php if ($has_certificate): ?>
                                                <span class="badge bg-success">Generated</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Not Generated</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                        <div class="action-buttons-cell">
                                            <?php if ($has_certificate): ?>
                                                <a href="<?php echo htmlspecialchars($client['file_path']); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                                <a href="<?php echo htmlspecialchars($client['file_path']); ?>" class="btn btn-outline-success btn-sm" download>
                                                    <i class="fas fa-download me-1"></i> Download
                                                </a>
                                                <button type="button" class="btn btn-outline-info btn-sm print-single-btn"
                                                        data-path="<?php echo htmlspecialchars($client['file_path']); ?>"
                                                        data-name="<?php echo htmlspecialchars($client['client_name']); ?>">
                                                    <i class="fas fa-print me-1"></i> Print
                                                </button>
                                                <button type="button" class="btn btn-outline-warning btn-sm send-email-btn"
                                                        data-id="<?php echo $client['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($client['client_name']); ?>"
                                                        data-email="<?php echo htmlspecialchars($client['email']); ?>">
                                                    <i class="fas fa-envelope me-1"></i> Send
                                                </button>
                                            <?php else: ?>
                                                <a href="client_certificates.php?file_id=<?php echo $file_id; ?>&client_id=<?php echo $client['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-plus-circle me-1"></i> Generate
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Modal -->
    <div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printModalLabel">Print Certificates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="printProgress" style="display: none;">
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: 0%" id="progressBar"></div>
                        </div>
                        <p id="progressText">Preparing certificates for printing...</p>
                    </div>
                    
                    <div id="printOptions">
                        <h6>Print Options</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="printWithDelay" checked>
                            <label class="form-check-label" for="printWithDelay">
                                Add delay between prints (recommended for multiple certificates)
                            </label>
                        </div>
                        <div class="mb-3">
                            <label for="printDelay" class="form-label">Delay between prints (seconds):</label>
                            <input type="number" class="form-control" id="printDelay" value="3" min="1" max="10">
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Each certificate will open in a new tab for printing. Please allow pop-ups for this site.
                        </div>
                    </div>
                    
                    <div id="printList" style="display: none;">
                        <h6>Certificates to Print:</h6>
                        <ul id="certificateList" class="list-group"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="startPrintBtn">Start Printing</button>
                </div>
            </div>
        </div>
    </div>

    
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
    
    <!-- Bootstrap and jQuery Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            let printQueue = [];
            let currentPrintIndex = 0;
            let printModal;

            // Debug function to log checkbox states
            function logCheckboxStates() {
                console.log("All checkboxes count: " + $('.client-checkbox').length);
                console.log("Checked checkboxes count: " + $('.client-checkbox:checked').length);
                console.log("Select All checked: " + $('#selectAll').prop('checked'));
            }

            // Handle select all checkbox with improved implementation
            $("#selectAll").on('click', function() {
                var isChecked = $(this).prop('checked');
                console.log("Select All clicked, setting checkboxes to: " + isChecked);
                
                // Get all checkboxes
                $('.client-checkbox').prop('checked', isChecked);
                
                // Update the count display
                updateSelectedCount();
                
                // Log the state for debugging
                logCheckboxStates();
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
                
                // Log the state for debugging
                logCheckboxStates();
            });

            // Update selected count and button states
            function updateSelectedCount() {
                const selectedCount = $('.client-checkbox:checked').length;
                $("#selectedCount").text(selectedCount + ' selected');
                
                // Enable/disable buttons based on selection
                $("#sendSelectedBtn, #downloadSelectedBtn, #printSelectedBtn").prop('disabled', selectedCount === 0);
            }

            // Handle print single certificate
            $(document).on('click', '.print-single-btn', function() {
                const certificatePath = $(this).data('path');
                const clientName = $(this).data('name');
                
                if (certificatePath) {
                    printCertificate(certificatePath, clientName);
                }
            });

            // Handle print selected certificates
            $("#printSelectedBtn").click(function() {
                const selectedCertificates = [];
                
                $('.client-checkbox:checked').each(function() {
                    const certificatePath = $(this).data('path');
                    const row = $(this).closest('tr');
                    const clientName = row.find('.client-name').text().trim();
                    
                    if (certificatePath) {
                        selectedCertificates.push({
                            path: certificatePath,
                            name: clientName
                        });
                    }
                });
                
                if (selectedCertificates.length > 0) {
                    showPrintModal(selectedCertificates);
                }
            });

            // Handle print all certificates
            $("#printAllBtn").click(function() {
                const allCertificates = [];
                
                $('.client-checkbox').each(function() {
                    const certificatePath = $(this).data('path');
                    const row = $(this).closest('tr');
                    const clientName = row.find('.client-name').text().trim();
                    
                    if (certificatePath) {
                        allCertificates.push({
                            path: certificatePath,
                            name: clientName
                        });
                    }
                });
                
                if (allCertificates.length > 0) {
                    showPrintModal(allCertificates);
                }
            });

            // Show print modal with certificate list
            function showPrintModal(certificates) {
                printQueue = certificates;
                currentPrintIndex = 0;
                
                // Update modal title
                $("#printModalLabel").text(`Print ${certificates.length} Certificate${certificates.length > 1 ? 's' : ''}`);
                
                // Show certificate list
                const listHtml = certificates.map((cert, index) => 
                    `<li class="list-group-item">
                        <span>${cert.name}</span>
                        <span class="print-status pending" id="status-${index}">Pending</span>
                    </li>`
                ).join('');
                
                $("#certificateList").html(listHtml);
                $("#printList").show();
                
                // Show modal
                printModal = new bootstrap.Modal(document.getElementById('printModal'));
                printModal.show();
            }

            // Start printing process
            $("#startPrintBtn").click(function() {
                if (printQueue.length === 0) return;
                
                // Hide options and show progress
                $("#printOptions").hide();
                $("#printProgress").show();
                $("#startPrintBtn").prop('disabled', true);
                
                // Start printing
                printNextCertificate();
            });

            // Print next certificate in queue
            function printNextCertificate() {
                if (currentPrintIndex >= printQueue.length) {
                    // All certificates printed
                    $("#progressText").text("All certificates have been sent to printer!");
                    $("#progressBar").css('width', '100%');
                    $("#startPrintBtn").text('Close').prop('disabled', false);
                    return;
                }
                
                const certificate = printQueue[currentPrintIndex];
                const progress = ((currentPrintIndex + 1) / printQueue.length) * 100;
                
                // Update progress
                $("#progressBar").css('width', progress + '%');
                $("#progressText").text(`Printing certificate ${currentPrintIndex + 1} of ${printQueue.length}: ${certificate.name}`);
                
                // Update status
                $(`#status-${currentPrintIndex}`).removeClass('pending').addClass('printing').text('Printing...');
                
                // Print certificate
                printCertificate(certificate.path, certificate.name, function() {
                    // Mark as completed
                    $(`#status-${currentPrintIndex}`).removeClass('printing').addClass('completed').text('Completed');
                    
                    currentPrintIndex++;
                    
                    // Continue with next certificate
                    if ($("#printWithDelay").is(':checked') && currentPrintIndex < printQueue.length) {
                        const delay = parseInt($("#printDelay").val()) * 1000;
                        setTimeout(printNextCertificate, delay);
                    } else {
                        printNextCertificate();
                    }
                });
            }

            // Print individual certificate
            function printCertificate(certificatePath, clientName, callback) {
                try {
                    // Open certificate in new window for printing
                    const printWindow = window.open(certificatePath, '_blank', 'width=800,height=600');
                    
                    if (printWindow) {
                        // Wait for the document to load, then print
                        printWindow.onload = function() {
                            setTimeout(function() {
                                printWindow.print();
                                if (callback) callback();
                            }, 1000);
                        };
                        
                        // Fallback if onload doesn't work
                        setTimeout(function() {
                            if (printWindow.document.readyState === 'complete') {
                                printWindow.print();
                                if (callback) callback();
                            }
                        }, 2000);
                    } else {
                        alert('Pop-up blocked! Please allow pop-ups for this site to enable printing.');
                        if (callback) callback();
                    }
                } catch (error) {
                    console.error('Error printing certificate:', error);
                    alert('Error printing certificate for ' + clientName);
                    if (callback) callback();
                }
            }

            // Reset modal when closed
            $('#printModal').on('hidden.bs.modal', function() {
                $("#printOptions").show();
                        $("#printProgress").hide();
                        $("#printList").hide();
                        $("#startPrintBtn").text('Start Printing').prop('disabled', false);
                        printQueue = [];
                        currentPrintIndex = 0;
                    });

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
                        $("#sendEmailForm").submit();
                    });

                    // Handle bulk send form submission
                    $("#bulkSendBtn").click(function() {
                        $("#bulkSendForm").submit();
                    });

                    // Initialize selected count
                    updateSelectedCount();
                    
                    // Log initial state for debugging
                    console.log("Initial state:");
                    logCheckboxStates();

                    // Add keyboard shortcuts
                    $(document).keydown(function(e) {
                        // Ctrl+P for print all
                        if (e.ctrlKey && e.which === 80) {
                            e.preventDefault();
                            if (!$("#printAllBtn").prop('disabled')) {
                                $("#printAllBtn").click();
                            }
                        }
                        
                        // Ctrl+Shift+P for print selected
                        if (e.ctrlKey && e.shiftKey && e.which === 80) {
                            e.preventDefault();
                            if (!$("#printSelectedBtn").prop('disabled')) {
                                $("#printSelectedBtn").click();
                            }
                        }
                    });

                    // Show keyboard shortcuts tooltip
                    $("#printAllBtn").attr('title', 'Print All Certificates (Ctrl+P)');
                    $("#printSelectedBtn").attr('title', 'Print Selected Certificates (Ctrl+Shift+P)');
                });
            </script>


    
    <?php include('footer.php'); ?>
</body>
</html>


