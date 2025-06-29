<?php
include("dbcon.php");
include("logincode.php");
// Set page-specific variables
$pageTitle = "Certificate Management";
$currentPage = "Certificate Management";

// Initialize message arrays for notifications
$success_message = '';
$error_message = '';

// Handle certificate operations
if (isset($_POST['edit_certificate'])) {
    $id = $_POST['editCertId'];
    $name = trim($_POST['editCertName']);
    $description = trim($_POST['editCertDesc']);
   
    // Check if a new file was uploaded
    if (isset($_FILES['editCertFile']) && $_FILES['editCertFile']['error'] == 0) {
        $uploadDir = 'uploads/certificates/';
       
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
       
        $fileName = $_FILES['editCertFile']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
       
        if (in_array($fileExtension, $allowedExtensions)) {
            // Generate unique filename
            $newFileName = uniqid() . '_' . $fileName;
            $uploadPath = $uploadDir . $newFileName;
           
            if (move_uploaded_file($_FILES['editCertFile']['tmp_name'], $uploadPath)) {
                // Get old file path to delete it
                $oldFileQuery = $conn->prepare("SELECT file_path FROM certificates WHERE id = ?");
                $oldFileQuery->bind_param("i", $id);
                $oldFileQuery->execute();
                $oldFileResult = $oldFileQuery->get_result();
                $oldFile = $oldFileResult->fetch_assoc();
               
                // Update with new file
                $stmt = $conn->prepare("UPDATE certificates SET name = ?, description = ?, file_path = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $description, $uploadPath, $id);
               
                if ($stmt->execute()) {
                    // Delete old file if it exists
                    if ($oldFile && file_exists($oldFile['file_path'])) {
                        unlink($oldFile['file_path']);
                    }
                    $success_message = "Certificate updated successfully with new template!";
                } else {
                    $error_message = "Error updating certificate: " . $stmt->error;
                }
                $oldFileQuery->close();
            } else {
                $error_message = "Error uploading new certificate file.";
            }
        } else {
            $error_message = "Invalid file type. Only JPG, JPEG, PNG, and PDF files are allowed.";
        }
    } else {
        // Update without changing the file
        $stmt = $conn->prepare("UPDATE certificates SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
       
        if ($stmt->execute()) {
            $success_message = "Certificate updated successfully!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    }
   
    if (isset($stmt)) {
        $stmt->close();
    }
}

// Delete certificate
if (isset($_POST['delete_certificate'])) {
    $id = $_POST['deleteCertId'];
   
    // Delete from database
    $stmt = $conn->prepare("UPDATE certificates SET status = 'Archived' WHERE id = ?");
    $stmt->bind_param("i", $id);
   
    if ($stmt->execute()) {
        $success_message = "Certificate archived successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Include the header
include('header.php');
// Include the sidebar
include('sidebar.php');

// Check for session messages and then clear them
if(isset($_SESSION['status']) && isset($_SESSION['status_type'])) {
    if($_SESSION['status_type'] == 'success') {
        $success_message = $_SESSION['status'];
    } else if($_SESSION['status_type'] == 'error' || $_SESSION['status_type'] == 'danger') {
        $error_message = $_SESSION['status'];
    }
   
    // Clear the session variables
    unset($_SESSION['status']);
    unset($_SESSION['status_type']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Management</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="shortcut icon" href="img/OIP.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        /* Add styles to match sheet.php */
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
       
        .certificate-item {
            margin-bottom: 20px;
        }
       
        .certificate-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
       
        .certificate-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
       
        .certificate-preview {
            width: 100%;
            height: 180px;
            object-fit: fill;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
       
        .certificate-date {
            color: #7f8c8d;
            font-size: 14px;
        }
       
        .action-buttons {
            margin-top: auto;
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
       
        .btn-primary-custom {
            background-color: #01043A !important;
            color: white !important;
            border: none !important;
        }
       
        .btn-primary-custom:hover {
            background-color: #01043A !important;
            opacity: 0.9;
        }
       
        .btn-warning-custom {
            background-color: #FFD700 !important;
            color: #212529 !important;
            border: none !important;
        }
       
        .btn-warning-custom:hover {
            background-color: #FFC107 !important;
        }
       
        .btn-danger-custom {
            background-color: #FF8C00 !important;
            color: white !important;
            border: none !important;
        }
       
        .btn-danger-custom:hover {
            background-color: #FF7000 !important;
        }

        .btn-text-settings {
            background-color: #6f42c1 !important;
            color: white !important;
            border: none !important;
        }

        .btn-text-settings:hover {
            background-color: #5a2d91 !important;
        }
       
        .btn-upload-new, .btn-primary {
            background-color: #006400 !important;
            color: white !important;
            border: none !important;
            border-radius: 4px !important;
        }
       
        .btn-upload-new:hover, .btn-primary:hover {
            background-color: #005000 !important;
        }
       
        .btn-archived-sheets, .btn-archived-certs {
            background-color: #FF8C00 !important;
            color: white !important;
            border: none !important;
            border-radius: 4px !important;
        }
       
        .btn-archived-sheets:hover, .btn-archived-certs:hover {
            background-color: #FF7000 !important;
        }
       
        .btn-settings {
            background-color: #01043A !important;
            color: white !important;
            border: none !important;
            border-radius: 4px !important;
        }
       
        .btn-settings:hover {
            background-color: #0038A8 !important;
        }
       
        /* Search bar styling */
        .search-container {
            max-width: 400px;
            width: 100%;
        }
       
        .btn-search {
            background-color: #01043A;
            color: white;
            border-color: #01043A;
            height: 36px;
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            border-radius: 3.5px;
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
            height: 36px;
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }
       
        .input-group .form-control:focus {
            box-shadow: none;
            border-color: #0038A8;
        }
       
        .input-group-append .btn {
            border-left: none;
        }
       
        /* Action buttons container */
        .action-buttons-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
       
        /* Alert styling */
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
            position: relative;
            padding-right: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
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
       
        /* Modal styling */
        .modal-header {
            background-color: #01043A;
            color: white;
            padding: 15px 25px;
        }
       
        .modal-header .close {
            color: white;
        }
       
        .modal-body {
            padding: 25px;
        }
       
        .modal-footer {
            padding: 15px 25px;
            background-color: #f8f9fa;
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

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons .btn {
                margin-bottom: 5px;
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

        /* Text Settings Button Styling - Scoped to modal only */
#editCertificateModal .modal-footer .btn-text-settings {
    background-color: #FFD700 !important;
    color: #212529 !important;
    border: none !important;
    border-radius: 4px !important;
    padding: 0.375rem 0.75rem !important;
    font-size: 0.875rem !important;
    font-weight: 500 !important;
    line-height: 1.8 !important;
    text-decoration: none !important;
    display: inline-block !important;
    vertical-align: middle !important;
    transition: all 0.15s ease-in-out !important;
    min-width: auto !important;
    height: 38px !important;
    box-sizing: border-box !important;
}

#editCertificateModal .modal-footer .btn-text-settings:hover {
    background-color: #FFC107 !important;
    color: #212529 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 4px rgba(255, 215, 0, 0.3) !important;
    text-decoration: none !important;
}

#editCertificateModal .modal-footer .btn-text-settings:focus {
    background-color: #FFC107 !important;
    color: #212529 !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25) !important;
    outline: none !important;
    text-decoration: none !important;
}

#editCertificateModal .modal-footer .btn-text-settings:active {
    background-color: #FFCA2C !important;
    color: #212529 !important;
    transform: translateY(0) !important;
    text-decoration: none !important;
}

/* Ensure the button aligns properly with other modal footer buttons */
#editCertificateModal .modal-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 0.5rem !important;
}

#editCertificateModal .modal-footer .btn {
    margin: 0 !important;
    flex-shrink: 0 !important;
}

/* Responsive adjustments for mobile */
@media (max-width: 768px) {
    #editCertificateModal .modal-footer {
        flex-direction: column !important;
        gap: 0.5rem !important;
    }
    
    #editCertificateModal .modal-footer .btn {
        width: 100% !important;
    }
    
    #editCertificateModal .modal-footer .btn-text-settings {
        width: 100% !important;
        text-align: center !important;
    }
}

    </style>
</head>

<div class="main-content" style="margin-top: 120px;">
    <div class="container">
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
                <div class="input-group">
                    <input type="text" class="form-control search-input" id="searchCertificate" placeholder="Search certificates...">
                    <div class="input-group-append">
                        <button class="btn btn-search" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
           
                        <!-- Action buttons in the upper right -->
            <div class="col-md-6">
                <div class="action-buttons-container">
                    <a href="archived_certificates.php" class="btn btn-archived-certs">
                        <i class="bi bi-archive"></i> Archived Certificates
                    </a>
                    <a href="generator.php" class="btn btn-upload-new">
                        <i class="bi bi-plus"></i> Add New Certificate
                    </a>
                    
                </div>
            </div>
        </div>

        <!-- Certificates Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
            <?php
            // Fetch certificates from the database
            $result = $conn->query("SELECT * FROM certificates WHERE status = 'Unarchived' ORDER BY upload_date DESC");
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

                    echo '<div class="col certificate-item">';
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
                    echo '            <div class="action-buttons">';
                    echo '                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewCertificateModal"';
                    echo '                        data-cert-id="' . $row['id'] . '"';
                    echo '                        data-cert-name="' . htmlspecialchars($row['name']) . '"';
                    echo '                        data-cert-date="' . $uploadDate . '"';
                    echo '                        data-cert-type="' . $fileTypeDisplay . '"';
                    echo '                        data-cert-desc="' . htmlspecialchars($row['description']) . '"';
                    echo '                        data-cert-file="' . htmlspecialchars($filePath) . '">';
                    echo '                    <i class="bi bi-eye"></i> View</button>';
                    echo '                <button class="btn btn-warning-custom btn-sm" data-bs-toggle="modal" data-bs-target="#editCertificateModal"';
                    echo '                        data-cert-id="' . $row['id'] . '"';
                    echo '                        data-cert-name="' . htmlspecialchars($row['name']) . '"';
                    echo '                        data-cert-date="' . $uploadDate . '"';
                    echo '                        data-cert-desc="' . htmlspecialchars($row['description']) . '"';
                    echo '                        data-cert-file="' . htmlspecialchars($filePath) . '">';
                    echo '                    <i class="bi bi-pencil"></i> Edit</button>';
                    
                    // Add Text Settings button for each certificate
                    

                    echo '                <button class="btn btn-danger-custom btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCertificateModal"';
                    echo '                        data-cert-id="' . $row['id'] . '"';
                    echo '                        data-cert-name="' . htmlspecialchars($row['name']) . '">';
                    echo '                    <i class="bi bi-archive"></i> Archive</button>';
                    echo '            </div>';
                    echo '        </div>';
                    echo '    </div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12 text-center" style="width: 100%;">';
                echo '    <div class="alert custom-info-alert" style="width: 100%;">';
                echo '        <i class="bi bi-info-circle me-2"></i> No certificates have been added yet.';
                echo '    </div>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($result) && $result->num_rows > 9): // Only show pagination if there are enough items ?>
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Certificate pagination">
                    <ul class="pagination justify-content-end">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- View Certificate Modal -->
<div class="modal fade" id="viewCertificateModal" tabindex="-1" aria-labelledby="viewCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCertificateModalLabel">Certificate Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left side - Certificate Info -->
                    <div class="col-md-5">
                        <div class="certificate-details-container">
                            <div class="certificate-details-header">
                                <h4 class="certificate-details-title" id="viewCertName">Certificate Name</h4>
                                <p class="certificate-details-subtitle" id="viewUploadDate">Uploaded on</p>
                            </div>

                            <div class="certificate-detail-row">
                                <div class="certificate-detail-label">File Type:</div>
                                <div class="certificate-detail-value" id="viewCertType">Type</div>
                            </div>
                            <div class="certificate-detail-row">
                                <div class="certificate-detail-label">Description:</div>
                                <div class="certificate-detail-value" id="viewCertDesc">Description</div>
                            </div>
                        </div>
                    </div>
                    <!-- Right side - Certificate Image/PDF -->
                    <div class="col-md-7">
                        <div class="certificate-details-container d-flex flex-column align-items-center justify-content-center">
                            <!-- PDF viewer will be inserted here by JavaScript -->
                            <img src="" alt="Certificate Preview" class="img-fluid mb-3" id="viewCertificateImage"
                            style="max-height: 400px; width: auto;"
                            onerror="this.src='assets/images/image-not-found.png'; this.onerror='';">
                            <div class="mt-3">
                                <a href="#" class="btn btn-success" id="downloadCertBtn" download>
                                    <i class="bi bi-download"></i> Download Certificate
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Certificate Modal -->
<div class="modal fade" id="editCertificateModal" tabindex="-1" aria-labelledby="editCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCertificateModalLabel">Edit Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCertificateForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="editCertId" name="editCertId" value="">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Certificate Information</h5>
                           
                            <div class="form-group mb-3">
                                <label for="editCertName" class="form-label">Certificate Name</label>
                                <input type="text" class="form-control" id="editCertName" name="editCertName" required>
                            </div>
                           
                            <div class="form-group mb-3">
                                <label for="editCertDesc" class="form-label">Description</label>
                                <textarea class="form-control" id="editCertDesc" name="editCertDesc" rows="3"></textarea>
                            </div>
                           
                            <div class="form-group mb-3">
                                <label for="editCertFile" class="form-label">Change Certificate Template (Optional)</label>
                                <input type="file" class="form-control" id="editCertFile" name="editCertFile" accept=".jpg,.jpeg,.png,.pdf">
                                <div class="form-text">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i>
                                        Leave empty to keep current template. Supported formats: JPG, JPEG, PNG, PDF
                                    </small>
                                </div>
                            </div>
                        </div>
                       
                        <!-- File Preview -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Current Certificate Template</h5>
                           
                            <div class="form-group mb-3">
                                <div class="card bg-light p-3 text-center">
                                    <img src="" alt="Current Certificate" class="img-fluid mx-auto" style="max-height: 250px; border-radius: 8px;" id="editCertPreview">
                                    <div class="mt-2">
                                        <small class="text-muted" id="editCertFileName">Current template</small>
                                    </div>
                                </div>
                            </div>
                           
                            <div class="alert alert-info" role="alert">
                                <i class="bi bi-lightbulb"></i>
                                <strong>Tip:</strong> Upload a new template to replace the current one. The old template will be automatically removed.
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="edit_certificate" value="1">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" class="btn btn-text-settings btn-sm" id="textSettingsBtn">
                    <i class="bi bi-gear"></i> Text Settings
                </a>
                <button type="submit" form="editCertificateForm" class="btn btn-primary-custom">
                    <i class="bi bi-save"></i> Save Changes
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Delete Certificate Modal -->
<div class="modal fade" id="deleteCertificateModal" tabindex="-1" aria-labelledby="deleteCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="deleteCertificateModalLabel">Archive Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to archive the certificate "<span id="deleteCertName">Business Permit</span>"?</p>
                <p class="text-info"><i class="bi bi-info-circle"></i> The certificate will be moved to the archive and can be restored later if needed.</p>
                <form id="deleteCertificateForm" method="POST">
                    <input type="hidden" id="deleteCertId" name="deleteCertId" value="">
                    <input type="hidden" name="delete_certificate" value="1">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" form="deleteCertificateForm" class="btn btn-danger-custom">
                    <i class="bi bi-archive"></i> Archive
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// JavaScript for certificate management functionality
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchCertificate');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const certificateItems = document.querySelectorAll('.certificate-item');
           
            certificateItems.forEach(item => {
                const certName = item.querySelector('.card-title').textContent.toLowerCase();
                const certDesc = item.querySelector('.card-text')?.textContent.toLowerCase() || '';
               
                if (certName.includes(searchTerm) || certDesc.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Edit certificate modal
    const editCertificateModal = document.getElementById('editCertificateModal');
    if (editCertificateModal) {
        editCertificateModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const certId = button.getAttribute('data-cert-id');
            const certName = button.getAttribute('data-cert-name');
            const certDesc = button.getAttribute('data-cert-desc');
            const certFile = button.getAttribute('data-cert-file');
           
            // Update modal content
            document.getElementById('editCertId').value = certId;
            document.getElementById('editCertName').value = certName;
            document.getElementById('editCertDesc').value = certDesc;
            
            // UPDATE THE TEXT SETTINGS LINK WITH THE CERTIFICATE ID
            document.getElementById('textSettingsBtn').href = `certificate_name&id_pos.php?cert_id=${certId}`;
            
            // Clear file input
            document.getElementById('editCertFile').value = '';
            
            // Update preview image and filename
            const editCertPreview = document.getElementById('editCertPreview');
            const editCertFileName = document.getElementById('editCertFileName');
            const fileExt = certFile.split('.').pop().toLowerCase();
            const fileName = certFile.split('/').pop();
            
            editCertFileName.textContent = fileName;
            
            if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
                editCertPreview.src = certFile;
                editCertPreview.style.display = 'block';
            } else if (fileExt === 'pdf') {
                editCertPreview.src = 'assets/images/pdf-icon.png';
                editCertPreview.style.display = 'block';
            } else {
                editCertPreview.src = 'assets/images/document-icon.png';
                editCertPreview.style.display = 'block';
            }
        });
       
        // Handle file input change for preview
        const editCertFile = document.getElementById('editCertFile');
        if (editCertFile) {
            editCertFile.addEventListener('change', function(event) {
                const file = event.target.files[0];
                const editCertPreview = document.getElementById('editCertPreview');
                const editCertFileName = document.getElementById('editCertFileName');
               
                if (file) {
                    const fileExt = file.name.split('.').pop().toLowerCase();
                    editCertFileName.textContent = file.name + ' (New template)';
                    editCertFileName.style.color = '#28a745';
                    editCertFileName.style.fontWeight = 'bold';
                   
                    if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            editCertPreview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else if (fileExt === 'pdf') {
                        editCertPreview.src = 'assets/images/pdf-icon.png';
                    } else {
                        editCertPreview.src = 'assets/images/document-icon.png';
                    }
                } else {
                    // Reset to original if no file selected
                    editCertFileName.style.color = '';
                    editCertFileName.style.fontWeight = '';
                }
            });
        }
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

    // View certificate modal
    const viewCertificateModal = document.getElementById('viewCertificateModal');
    if (viewCertificateModal) {
        viewCertificateModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const certId = button.getAttribute('data-cert-id');
            const certName = button.getAttribute('data-cert-name');
            const certDate = button.getAttribute('data-cert-date');
            const certType = button.getAttribute('data-cert-type');
            const certDesc = button.getAttribute('data-cert-desc');
            const certFile = button.getAttribute('data-cert-file');
           
            // Update modal content
            document.getElementById('viewCertName').textContent = certName;
            document.getElementById('viewUploadDate').textContent = 'Uploaded on ' + certDate;
            document.getElementById('viewCertType').textContent = certType;
            document.getElementById('viewCertDesc').textContent = certDesc;
           
            // Set download link
            const downloadBtn = document.getElementById('downloadCertBtn');
            downloadBtn.href = certFile;
            downloadBtn.setAttribute('download', certName);
           
            // Display image or PDF viewer
            const viewCertificateImage = document.getElementById('viewCertificateImage');
            const fileExt = certFile.split('.').pop().toLowerCase();
           
            if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
                viewCertificateImage.src = certFile;
                viewCertificateImage.style.display = 'block';
            } else if (fileExt === 'pdf') {
                // Create PDF embed if it's a PDF
                viewCertificateImage.style.display = 'none';
                const pdfContainer = viewCertificateImage.parentElement;
               
                // Remove existing PDF viewer if any
                const existingEmbed = pdfContainer.querySelector('embed');
                if (existingEmbed) {
                    pdfContainer.removeChild(existingEmbed);
                }
               
                // Create new PDF viewer
                const pdfEmbed = document.createElement('embed');
                pdfEmbed.src = certFile;
                pdfEmbed.type = 'application/pdf';
                pdfEmbed.style.width = '100%';
                pdfEmbed.style.height = '400px';
                pdfEmbed.className = 'mb-3';
               
                // Insert before the buttons
                pdfContainer.insertBefore(pdfEmbed, pdfContainer.querySelector('.mt-3'));
            }
        });
    }
   
    // Form validation for edit certificate
    const editCertificateForm = document.getElementById('editCertificateForm');
    if (editCertificateForm) {
        editCertificateForm.addEventListener('submit', function(e) {
            const certName = document.getElementById('editCertName').value.trim();
            const fileInput = document.getElementById('editCertFile');
           
            if (!certName) {
                e.preventDefault();
                alert('Please enter a certificate name.');
                return false;
            }
           
            // Validate file if uploaded
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                const maxSize = 10 * 1024 * 1024; // 10MB
               
                if (!allowedTypes.includes(file.type)) {
                    e.preventDefault();
                    alert('Please upload a valid file type (JPG, JPEG, PNG, or PDF).');
                    return false;
                }
               
                if (file.size > maxSize) {
                    e.preventDefault();
                    alert('File size must be less than 10MB.');
                    return false;
                }
            }
           
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
            submitBtn.disabled = true;
           
            // Re-enable button after a delay (in case of errors)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    }
   
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.notification-alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
   
    // File drag and drop functionality for edit modal
    const editFileInput = document.getElementById('editCertFile');
    const editPreviewContainer = editFileInput?.closest('.col-md-6');
   
    if (editPreviewContainer) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            editPreviewContainer.addEventListener(eventName, preventDefaults, false);
        });
       
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
       
        ['dragenter', 'dragover'].forEach(eventName => {
            editPreviewContainer.addEventListener(eventName, highlight, false);
        });
       
        ['dragleave', 'drop'].forEach(eventName => {
            editPreviewContainer.addEventListener(eventName, unhighlight, false);
        });
       
        function highlight(e) {
            editPreviewContainer.style.backgroundColor = '#f8f9fa';
            editPreviewContainer.style.border = '2px dashed #007bff';
        }
       
        function unhighlight(e) {
            editPreviewContainer.style.backgroundColor = '';
            editPreviewContainer.style.border = '';
        }
       
        editPreviewContainer.addEventListener('drop', handleDrop, false);
       
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
           
            if (files.length > 0) {
                editFileInput.files = files;
                // Trigger change event
                const event = new Event('change', { bubbles: true });
                editFileInput.dispatchEvent(event);
            }
        }
    }
});
</script>

<?php
// Include the footer
include('footer.php');
?>
</body>
</html>

                

