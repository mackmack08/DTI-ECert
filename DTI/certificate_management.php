<?php
// Include database connection
include("dbcon.php");

// Set page-specific variables
$pageTitle = "DTI Certificate Management";
$currentPage = "Certificate Management";

// Handle certificate operations
$message = '';
$messageType = '';

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
            <a href="generator.php" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill fw-bold text-white">
                <i class="fas fa-plus me-2"></i> Add New Certificate
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
            echo '            <h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
            echo '            <p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
            echo '            <div class="certificate-date mb-3">';
            echo '                <i class="far fa-calendar-alt me-1"></i> Uploaded on ' . $uploadDate;
            echo '            </div>';
            echo '            <div class="action-buttons">';
            echo '                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewCertificateModal"';
            echo '                        data-cert-id="' . $row['id'] . '"';
            echo '                        data-cert-name="' . htmlspecialchars($row['name']) . '"';
            echo '                        data-cert-date="' . $uploadDate . '"';
            echo '                        data-cert-type="' . $fileTypeDisplay . '"';
            echo '                        data-cert-desc="' . htmlspecialchars($row['description']) . '"';
            echo '                        data-cert-file="' . htmlspecialchars($filePath) . '">';
            echo '                    <i class="fas fa-eye"></i> View</button>';
            echo '                <button class="btn btn-warning-custom btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#editCertificateModal"';
            echo '                        data-cert-id="' . $row['id'] . '"';
            echo '                        data-cert-name="' . htmlspecialchars($row['name']) . '"';
            echo '                        data-cert-date="' . $uploadDate . '"';
            echo '                        data-cert-desc="' . htmlspecialchars($row['description']) . '"';
            echo '                        data-cert-file="' . htmlspecialchars($filePath) . '">';
            echo '                    <i class="fas fa-edit"></i> Edit</button>';
            echo '                <button class="btn btn-danger-custom btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#deleteCertificateModal"';
            echo '                        data-cert-id="' . $row['id'] . '"';
            echo '                        data-cert-name="' . htmlspecialchars($row['name']) . '">';
            echo '                    <i class="fas fa-trash-alt"></i> Delete</button>';
            echo '            </div>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';
        }
    } else {
        echo '<div class="col-12 text-center">';
        echo '    <div class="alert alert-info">';
        echo '        <i class="fas fa-info-circle me-2"></i> No certificates found. Add your first certificate using the button above.';
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
                            <!-- In your HTML, add an onerror handler to the image -->
                            <img src="" alt="Certificate Preview" class="img-fluid mb-3" id="viewCertificateImage" 
                            style="max-height: 400px; width: auto;" 
                            onerror="this.src='assets/images/image-not-found.png'; this.onerror='';">


                            <div class="mt-3">
                                <a href="#" class="btn btn-success" id="downloadCertBtn" download>
                                    <i class="fas fa-download me-2"></i> Download Certificate
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
                const certDesc = item.querySelector('.card-text').textContent.toLowerCase();
                
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
            
            // Update preview image
            const editCertPreview = document.getElementById('editCertPreview');
            const fileExt = certFile.split('.').pop().toLowerCase();
            
            if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
                editCertPreview.src = certFile;
                editCertPreview.style.display = 'block';
            } else if (fileExt === 'pdf') {
                // For PDF, show a placeholder or PDF icon
                editCertPreview.src = 'assets/images/pdf-icon.png'; // Replace with your PDF icon path
                editCertPreview.style.display = 'block';
            } else {
                // For other file types
                editCertPreview.src = 'assets/images/document-icon.png'; // Replace with your document icon path
                editCertPreview.style.display = 'block';
            }
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
            
            console.log('Certificate file path:', certFile); // Debug log
            
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
            
            // Set up edit button
            document.querySelector('.edit-from-view').onclick = function() {
                // Hide this modal and show edit modal
                const viewModal = bootstrap.Modal.getInstance(viewCertificateModal);
                viewModal.hide();
                
                // Trigger edit modal with same data
                setTimeout(() => {
                    const editButton = document.querySelector(`button[data-bs-target="#editCertificateModal"][data-cert-id="${certId}"]`);
                    if (editButton) {
                        editButton.click();
                    }
                }, 500);
            };
        });
    }
});
</script>
<?php
// Include the footer
include('footer.php');
?>

