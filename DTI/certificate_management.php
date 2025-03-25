<?php
// Include database connection
include("dbcon.php");

// Set page-specific variables
$pageTitle = "DTI Certificate Management";
$currentPage = "Certificate Management";

// Handle certificate operations
$message = '';
$messageType = '';

// Add new certificate
if (isset($_POST['add_certificate'])) {
    // Get form data
    $name = trim($_POST['certName']);
    $description = trim($_POST['certDesc']);
    $file = $_FILES['certFile'];
   
    // Basic validation
    if (empty($name) || $file['error'] !== UPLOAD_ERR_OK) {
        $message = "Please fill all required fields and upload a valid file.";
        $messageType = "danger";
    } else {
        // File upload validation
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $file["tmp_name"]);
        finfo_close($finfo);
        $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
       
        if (!in_array($fileExtension, ['pdf', 'jpg', 'jpeg', 'png'])) {
            $message = "Invalid file type. Only JPG, PNG and PDF are allowed.";
            $messageType = "danger";
        } else if ($file["size"] > 5 * 1024 * 1024) {
            $message = "File is too large. Maximum size is 5MB.";
            $messageType = "danger";
        } else {
            // Create unique filename
            $targetDir = "uploads/certificates/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
           
            $uniqueFilename = uniqid('cert_') . '.' . $fileExtension;
            $targetFile = $targetDir . $uniqueFilename;
           
            // Move uploaded file
            if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO certificates (name, description, file_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $description, $targetFile);
               
                if ($stmt->execute()) {
                    $message = "Certificate added successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error: " . $stmt->error;
                    $messageType = "danger";
                }
                $stmt->close();
            } else {
                $message = "Error uploading file.";
                $messageType = "danger";
            }
        }
    }
}

// Edit certificate
if (isset($_POST['edit_certificate'])) {
    $id = $_POST['editCertId'];
    $name = trim($_POST['editCertName']);
    $description = trim($_POST['editCertDesc']);
   
    // Update without changing the file
    $stmt = $conn->prepare("UPDATE certificates SET name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $description, $id);
   
    // Execute the update query
    if ($stmt->execute()) {
        $message = "Certificate updated successfully!";
        $messageType = "success";
    } else {
        $message = "Error: " . $stmt->error;
        $messageType = "danger";
    }
   
    $stmt->close();
}


// Delete certificate
if (isset($_POST['delete_certificate'])) {
    $id = $_POST['deleteCertId'];
   
    // Get the file path to delete the file
    $stmt = $conn->prepare("SELECT file_path FROM certificates WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $filePath = $row['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file
        }
    }
    $stmt->close();
   
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM certificates WHERE id = ?");
    $stmt->bind_param("i", $id);
   
    if ($stmt->execute()) {
        $message = "Certificate deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error: " . $stmt->error;
        $messageType = "danger";
    }
    $stmt->close();
}

// Include the header
include('header.php');

// Include the sidebar
include('sidebar.php');
?>

<div class="main-content" style="margin-top: 120px;">
    <div class="container">
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php if ($messageType == 'success'): ?>
                <i class="fas fa-check-circle me-2"></i>
            <?php elseif ($messageType == 'danger'): ?>
                <i class="fas fa-times-circle me-2"></i>
            <?php endif; ?>
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Search and Add Certificate Section -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6 text-md-start">
                <a href="#" class="btn add-certificate-btn" data-bs-toggle="modal" data-bs-target="#addCertificateModal">
                    <i class="fas fa-plus"></i> Add New Certificate
                </a>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control search-input border-start-0" id="searchCertificate" placeholder="Search Certificate's Name">
                </div>
            </div>
        </div>

        <!-- Certificates Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
            <?php
            // Fetch certificates from the database
            $result = $conn->query("SELECT * FROM certificates ORDER BY upload_date DESC");
           
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
                   
                    echo '<div class="col certificate-item">';
                    echo '<div class="card certificate-card">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
                    echo '<div class="certificate-date mb-3">';
                    echo '<i class="far fa-calendar-alt me-1"></i> Uploaded on ' . $uploadDate;
                    echo '</div>';
                    echo '<div class="action-buttons">';
                    echo '<button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewCertificateModal"
                            data-cert-id="' . $row['id'] . '"
                            data-cert-name="' . htmlspecialchars($row['name']) . '"
                            data-cert-date="' . $uploadDate . '"
                            data-cert-type="' . $fileTypeDisplay . '"
                            data-cert-desc="' . htmlspecialchars($row['description']) . '"
                            data-cert-file="' . htmlspecialchars($row['file_path']) . '">';
                    echo '<i class="fas fa-eye"></i> View</button>';
                    echo '<button class="btn btn-warning-custom btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#editCertificateModal"
                            data-cert-id="' . $row['id'] . '"
                            data-cert-name="' . htmlspecialchars($row['name']) . '"
                            data-cert-date="' . $uploadDate . '"
                            data-cert-desc="' . htmlspecialchars($row['description']) . '"
                            data-cert-file="' . htmlspecialchars($row['file_path']) . '">';
                    echo '<i class="fas fa-edit"></i> Edit</button>';
                    echo '<button class="btn btn-danger-custom btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#deleteCertificateModal"
                            data-cert-id="' . $row['id'] . '"
                            data-cert-name="' . htmlspecialchars($row['name']) . '">';
                    echo '<i class="fas fa-trash-alt"></i> Delete</button>';
                    echo '</div></div></div></div>';
                }
            } else {
                echo '<div class="col-12 text-center">';
                echo '<div class="alert alert-info">';
                echo '<i class="fas fa-info-circle me-2"></i> No certificates found. Add your first certificate using the button above.';
                echo '</div>';
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
                            <img src="" alt="Certificate Preview" class="img-fluid mb-3" id="viewCertificateImage" style="max-height: 400px; width: auto;">
                            <div class="mt-3">
                                <a href="#" class="btn btn-success" id="downloadCertBtn" download>
                                    <i class="fas fa-download me-2"></i> Download Certificate
                                </a>
                                <a href="#" class="btn btn-secondary" id="printCertBtn">
                                    <i class="fas fa-print me-2"></i> Print Certificate
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning-custom edit-from-view">
                    <i class="fas fa-edit me-2"></i> Edit Certificate
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Add Certificate Modal -->
<div class="modal fade" id="addCertificateModal" tabindex="-1" aria-labelledby="addCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCertificateModalLabel">Add New Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCertificateForm" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Certificate Information</h5>
                           
                            <div class="form-group mb-3">
                                <label for="certName" class="form-label">Certificate Name</label>
                                <input type="text" class="form-control" id="certName" name="certName" required>
                            </div>
                           
                            <div class="form-group mb-3">
                                <label for="certDesc" class="form-label">Description</label>
                                <textarea class="form-control" id="certDesc" name="certDesc" rows="3"></textarea>
                            </div>
                        </div>
                       
                        <!-- File Upload -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Certificate Upload</h5>
                           
                            <div class="form-group mb-3">
                                <label for="certFile" class="form-label">Certificate File</label>
                                <input type="file" class="form-control" id="certFile" name="certFile" accept=".pdf,.jpg,.jpeg,.png" required>
                                <div class="form-text">Upload certificate file (PDF, JPG, PNG)</div>
                            </div>
                           
                            <div class="form-group mb-3">
                                <label class="form-label">Preview</label>
                                <div class="card bg-light p-3 d-flex justify-content-center align-items-center" style="min-height: 200px;">
                                    <div id="previewPlaceholder" class="text-center text-muted">
                                        <i class="fas fa-certificate fa-3x mb-2"></i>
                                        <p>Certificate preview will appear here</p>
                                    </div>
                                    <img id="certPreview" class="img-fluid" style="max-height: 180px; display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="add_certificate" value="1">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addCertificateForm" class="btn btn-primary-custom">
                    <i class="fas fa-plus me-2"></i> Add Certificate
                </button>
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
                        </div>
                       
                        <!-- File Upload -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Certificate File</h5>
                           
                        
                           
                            <div class="form-group mb-3">
                                <label class="form-label">Current Certificate</label>
                                <div class="card bg-light p-3 text-center">
                                    <img src="" alt="Current Certificate" class="img-fluid mx-auto" style="max-height: 180px;" id="editCertPreview">
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="edit_certificate" value="1">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editCertificateForm" class="btn btn-primary-custom">
                    <i class="fas fa-save me-2"></i> Save Changes
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
                <h5 class="modal-title" id="deleteCertificateModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the certificate "<span id="deleteCertName">Business Permit</span>"?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> This action cannot be undone. The certificate will be permanently removed.</p>
                <form id="deleteCertificateForm" method="POST">
                    <input type="hidden" id="deleteCertId" name="deleteCertId" value="">
                    <input type="hidden" name="delete_certificate" value="1">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteCertificateForm" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-2"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="certificate_management.js"></script>
<?php
// Include the footer
include('footer.php');
?>
