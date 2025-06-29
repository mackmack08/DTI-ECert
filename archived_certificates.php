<?php
include("dbcon.php");
include("logincode.php");

// Set page-specific variables
$pageTitle = "Archived Certificates";
$currentPage = "Archived Certificates";

// Initialize message arrays
$success_messages = [];
$error_messages = [];

// Check for success messages from redirects
if (isset($_GET['recover']) && $_GET['recover'] == 'success') {
    $success_messages[] = "Certificate restored successfully.";
}

// Handle unarchive operation
if (isset($_POST['unarchive_certificate'])) {
    $id = $_POST['unarchiveCertId'];
    
    // Update status to Unarchived
    $stmt = $conn->prepare("UPDATE certificates SET status = 'Unarchived' WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Redirect after successful processing
        header("Location: archived_certificates.php?recover=success");
        exit;
    } else {
        $error_messages[] = "Error restoring certificate: " . $stmt->error;
    }
    $stmt->close();
}

// Delete certificate permanently
if (isset($_POST['delete_certificate'])) {
    $id = $_POST['deleteCertId'];
    
    // First get the file path to delete the physical file
    $stmt = $conn->prepare("SELECT file_path FROM certificates WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $file_path = $row['file_path'];
        
        // Delete the physical file if it exists
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Now delete the record from the database
        $delete_stmt = $conn->prepare("DELETE FROM certificates WHERE id = ?");
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            $success_messages[] = "Certificate permanently deleted successfully!";
        } else {
            $error_messages[] = "Error deleting certificate: " . $delete_stmt->error;
        }
        $delete_stmt->close();
    } else {
        $error_messages[] = "Certificate not found!";
    }
    $stmt->close();
}

// Include the header
include('header.php');
// Include the sidebar
include('sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Certificates</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        /* Main container styling */
        .main-content {
            background-color: #f5f7fa;
            padding: 20px;
            min-height: calc(100vh - 120px);
            background-image: linear-gradient(to right, rgba(0,0,0,0.02) 1px, transparent 1px),
                            linear-gradient(to bottom, rgba(0,0,0,0.02) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        /* Header section styling */
        .page-header {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 15px;
            text-align: left;
        }

        h2 {
            color: #2c3e50;
            font-weight: 600;
            font-size: 24px;
            margin-bottom: 5px;
            text-align: left;
        }

        h2 i {
            margin-right: 10px;
            color: #6c757d;
        }

        .text-muted {
            text-align: left;
        }

        /* Back button styling */
        .btn-primary {
            background-color: #01043A;
            border-color: #01043A;
            color: white;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 4px !important;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-primary i {
            margin-right: 8px;
        }

        /* Alert styling */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 8px;
            transition: opacity 0.5s ease-out;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            width: 100%;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-info {
            color: #0c5460;
            background-color: #e6f3ff !important; 
            color: #01043A !important;
            border-color: #b8daff !important;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        .alert i {
            margin-right: 10px;
        }

        .alert-info i {
            font-size: 1.2rem;
            color: #01043A;
        }

        /* Card styling */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            overflow: hidden;
            margin-bottom: 20px;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background-color: #01043A;
            color: white;
            font-weight: 600;
            padding: 12px 15px;
            border-bottom: none;
            font-size: 16px;
        }

        .card-header i {
            margin-right: 10px;
        }

        .card-body {
            padding: 20px;
            background-color: white;
            display: flex;
            flex-direction: column;
        }

        .card-body p {
            margin-bottom: 10px;
            color: #555;
            font-size: 14px;
        }

        .card-body p strong {
            color: #333;
            font-weight: 600;
            margin-right: 5px;
        }

        /* Archived badge */
        .archived-badge {
            display: inline-block;
            background-color: #6c757d;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        /* Button styling */
        .text-center {
            text-align: center !important;
            margin-top: 15px;
        }

        /* Updated button styles */
        .btn-action {
            font-size: 13px;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-left: 5px;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Modal styling */
        .modal-content {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }

        .modal-header.bg-success {
            background-color: #28a745 !important;
            color: white;
        }

        .modal-header.bg-danger {
            background-color: #dc3545 !important;
            color: white;
        }

        .modal-title {
            color: white;
            font-weight: 600;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-body p {
            color: #555;
            margin-bottom: 10px;
        }

        .modal-body p strong {
            color: #333;
        }

        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 15px 20px;
            border-radius: 0 0 10px 10px;
        }

        .btn-secondary {
            background-color: #95a5a6;
            border-color: #95a5a6;
            color: white;
            border-radius: 4px !important;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
            border-color: #7f8c8d;
        }

        /* Add archived badge to each card */
        .card::before {
            content: "Archived";
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #FF8C00;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 500;
            z-index: 1;
        }

        /* Certificate preview */
        .certificate-preview {
            width: 100%;
            height: 180px;
            object-fit: fill;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        /* File info and actions */
        .file-info {
            flex-grow: 1;
        }

        .file-actions {
            margin-top: 15px;
        }

        /* Certificate date styling */
        .certificate-date {
            color: #7f8c8d;
            font-size: 14px;
        }

        /* Action buttons */
        .action-buttons {
            margin-top: auto;
            display: flex;
            gap: 5px;
        }

        /* Custom info alert styling */
        .custom-info-alert {
            background-color: #e6f3ff !important;
            color: #01043A !important;
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .col-md-6 {
                margin-bottom: 20px;
            }
            
            .text-center {
                text-align: center !important;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .page-header div:last-child {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>

<div class="main-content" style="margin-top: 120px;">
    <div class="container">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-archive" style="font-weight: 1000;"></i> ARCHIVED CERTIFICATES</h2>
                <p class="text-muted">View and recover previously archived certificates</p>
            </div>
            <div>
                <a href="certificate_management.php" class="btn btn-primary" style="border-radius: 5px;">
                <i class="bi bi-arrow-left"></i> Back to Certificate Management
                </a>
            </div>
        </div>

        <!-- Display success messages -->
        <?php if (!empty($success_messages)): ?>
            <?php foreach ($success_messages as $message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Display error messages -->
        <?php if (!empty($error_messages)): ?>
            <?php foreach ($error_messages as $message): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Certificates Cards -->
        <div class="row">
            <?php
            // Fetch archived certificates from the database
            $result = $conn->query("SELECT * FROM certificates WHERE status = 'Archived' ORDER BY upload_date DESC");

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Format date
                    $uploadDate = date('F j, Y', strtotime($row['upload_date']));
                   
                    // Determine file type for display
                    $fileExtension = strtolower(pathinfo($row['file_path'], PATHINFO_EXTENSION));
                    $fileTypeDisplay = 'Document';
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                        $fileTypeDisplay = 'Image';
                    } else if ($fileExtension === 'pdf') {
                        $fileTypeDisplay = 'PDF Document';
                    }

                    // Ensure file path is a full URL
                    $filePath = $row['file_path'];
                    // Check if the path is relative and convert to absolute URL if needed
                    if (!preg_match('/^https?:\/\//', $filePath)) {
                        // Get the directory where the script is running
                        $baseDir = dirname($_SERVER['SCRIPT_NAME']);
                        $baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                        
                        // If baseDir is root, avoid double slash
                        if ($baseDir == '/') $baseDir = '';
                        
                        // Create proper URL
                        $filePath = $baseURL . $baseDir . '/' . ltrim($filePath, '/');
                    }

                    echo '<div class="col-md-6 col-lg-4 mb-4">';
                    echo '    <div class="card certificate-card">';
                    echo '        <div class="card-body">';
                    
                    // Add certificate preview image
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                        echo '        <img src="' . htmlspecialchars($filePath) . '" class="certificate-preview" alt="Certificate Preview">';
                    } else if ($fileExtension === 'pdf') {
                        echo '        <img src="assets/images/pdf-icon.png" class="certificate-preview" alt="PDF Certificate">';
                    } else {
                        echo '        <img src="assets/images/document-icon.png" class="certificate-preview" alt="Document Certificate">';
                    }
                    
                    echo '            <h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
                    echo '            <div class="certificate-date mb-3">';
                    echo '                <i class="bi bi-calendar"></i> Uploaded on ' . $uploadDate;
                    echo '            </div>';
                    echo '            <div class="text-center">';
                    echo '                <button class="btn btn-success btn-action" data-bs-toggle="modal" data-bs-target="#unarchiveCertificateModal"';
                    echo '                        data-cert-id="' . $row['id'] . '"';
                    echo '                        data-cert-name="' . htmlspecialchars(addslashes($row['name'])) . '">';
                    echo '                    <i class="bi bi-arrow-counterclockwise"></i> Restore</button>';
                    echo '                <button class="btn btn-danger btn-action" data-bs-toggle="modal" data-bs-target="#deleteCertificateModal"';
                    echo '                        data-cert-id="' . $row['id'] . '"';
                    echo '                        data-cert-name="' . htmlspecialchars(addslashes($row['name'])) . '">';
                    echo '                    <i class="bi bi-trash"></i> Delete</button>';
                    echo '            </div>';
                    echo '        </div>';
                    echo '    </div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12">';
                echo '    <div class="alert alert-info" role="alert">';
                echo '        <i class="bi bi-info-circle-fill"></i> No archived certificates found. When you archive certificates, they will appear here.';
                echo '    </div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>

<!-- Unarchive Certificate Modal -->
<div class="modal fade" id="unarchiveCertificateModal" tabindex="-1" aria-labelledby="unarchiveCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="unarchiveCertificateModalLabel">Restore Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restore the certificate "<span id="unarchiveCertName"></span>"?</p>
                <p>This will make the certificate available again in the main certificate list.</p>
                <form id="unarchiveCertificateForm" method="POST">
                    <input type="hidden" id="unarchiveCertId" name="unarchiveCertId" value="">
                    <input type="hidden" name="unarchive_certificate" value="1">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="unarchiveCertificateForm" class="btn btn-success">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Certificate Modal -->
<div class="modal fade" id="deleteCertificateModal" tabindex="-1" aria-labelledby="deleteCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCertificateModalLabel">Delete Certificate Permanently</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <strong>permanently delete</strong> the certificate "<span id="deleteCertName"></span>"?</p>
                <p class="text-danger"><i class="bi bi-exclamation-triangle-fill"></i> This action cannot be undone. The certificate will be permanently removed from the system.</p>
                <form id="deleteCertificateForm" method="POST">
                    <input type="hidden" id="deleteCertId" name="deleteCertId" value="">
                    <input type="hidden" name="delete_certificate" value="1">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteCertificateForm" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Delete Permanently
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// JavaScript for certificate management functionality
document.addEventListener('DOMContentLoaded', function() {
    // Unarchive certificate modal
    const unarchiveCertificateModal = document.getElementById('unarchiveCertificateModal');
    if (unarchiveCertificateModal) {
        unarchiveCertificateModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const certId = button.getAttribute('data-cert-id');
            const certName = button.getAttribute('data-cert-name');
            
            // Update modal content
            document.getElementById('unarchiveCertId').value = certId;
            document.getElementById('unarchiveCertName').textContent = certName;
        });
    }

    // Delete certificate modal
    const deleteCertificateModal = document.getElementById('deleteCertificateModal');
    if (deleteCertificateModal) {
        deleteCertificateModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const certId = button.getAttribute('data-cert-id');
            const certName = button.getAttribute('data-cert-name');
            
            // Update modal content
            document.getElementById('deleteCertId').value = certId;
            document.getElementById('deleteCertName').textContent = certName;
        });
    }
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert-success, .alert-danger');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
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

