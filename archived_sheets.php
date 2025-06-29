<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
include('dbcon.php');
require 'vendor/autoload.php';

// Page metadata
$pageTitle = "Archived Sheets";
$currentPage = "Archived Sheets";
include('header.php');
include('sidebar.php');

// Initialize arrays and messages
$archived_files = [];
$error_messages = [];
$success_messages = [];

// Check for success messages from redirects
if (isset($_GET['recover']) && $_GET['recover'] == 'success') {
    $success_messages[] = "Sheet recovered successfully.";
}

// Fetch all archived files
$query = "SELECT * FROM files WHERE status = 'Archived' ORDER BY upload_time DESC";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $archived_files[] = [
        'id' => $row['id'],
        'file_name' => $row['file_name'],
        'file_path' => $row['file_path'],
        'upload_time' => $row['upload_time'],
        'cert_type' => $row['cert_type']
    ];
}

// Handle Recover Sheet form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['recover_file_id'])) {
    $file_id = (int)$_POST['recover_file_id'];
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Update the status to 'Unarchived'
        $stmt = $conn->prepare("UPDATE files SET status = 'Unarchived' WHERE id = ?");
        $stmt->bind_param("i", $file_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to recover file");
        }
        
        // Also update related client records
        $stmt = $conn->prepare("UPDATE clients SET status = 'Unarchived' WHERE file_id = ?");
        $stmt->bind_param("i", $file_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to recover client records");
        }
        
        // Commit transaction
        $conn->commit();
        
        // Redirect after successful processing
        header("Location: archived_sheets.php?recover=success");
        exit;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error_messages[] = "Error recovering sheet: " . $e->getMessage();
    }
}

$conn->close(); // Close the database connection
?>

<div class="main-content" style="margin-top: 120px;">
    <div class="container">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-archive"></i> ARCHIVED SHEETS</h2>
                <p class="text-muted">View and recover previously archived sheets</p>
            </div>
            <div>
                <a href="sheet.php" class="btn btn-primary" style="border-radius: 5px;">
                <i class="bi bi-arrow-left"></i> Back to Sheet Management
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

        <!-- Display archived files -->
        <div class="row">
            <?php if (empty($archived_files)): ?>
                <div class="col-12">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle-fill"></i> No archived sheets found. When you archive sheets, they will appear here.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($archived_files as $file): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-file-earmark-excel"></i> <?php echo htmlspecialchars($file['file_name']); ?>
                            </div>
                            <div class="card-body">
                                <div class="file-info">
                                <p><strong>Uploaded:</strong> <?php echo date('F j, Y, g:i a', strtotime($file['upload_time'])); ?></p>
                                    <p><strong>File Path:</strong> <span class="text-truncate d-inline-block" style="max-width: 100%;"><?php echo htmlspecialchars($file['file_path']); ?></span></p>
                                    <p><strong>Certificate Type:</strong> 
                                        <?php 
                                        if (!empty($file['cert_type'])) {
                                            echo htmlspecialchars($file['cert_type']);
                                        } else {
                                            echo '<span class="text-muted">Not assigned</span>';
                                        }
                                        ?>
                                    </p>
                                </div>
                                
                                <!-- Centered Recover Button (Delete button removed) -->
                                <div class="text-center">
                                    <button type="button" class="btn btn-success btn-action" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#recoverModal" 
                                            onclick="populateRecoverModal(<?php echo $file['id']; ?>, '<?php echo htmlspecialchars(addslashes($file['file_name'])); ?>')">
                                        <i class="bi bi-arrow-counterclockwise"></i> Recover
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recover Modal -->
<div class="modal fade" id="recoverModal" tabindex="-1" aria-labelledby="recoverModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="recoverModalLabel">Recover Sheet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to recover the sheet "<span id="recover_file_name"></span>"?</p>
                <p>This will restore the sheet and all associated client records to active status.</p>
                <form id="recoverForm" method="POST">
                    <input type="hidden" id="recover_file_id" name="recover_file_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="recoverForm" class="btn btn-success">
                    <i class="bi bi-arrow-counterclockwise"></i> Recover
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Scoped styles for archived_sheets.php only */
.main-content {
    background-color: #f5f7fa;
    padding: 20px;
    min-height: calc(100vh - 120px);
    background-image: linear-gradient(to right, rgba(0,0,0,0.02) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(0,0,0,0.02) 1px, transparent 1px);
    background-size: 20px 20px;
}

.main-content .container {
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
}

.main-content .page-header {
    margin-bottom: 1.5rem;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 15px;
    text-align: left;
}

.main-content h2 {
    color: #2c3e50;
    font-weight: 600;
    font-size: 24px;
    margin-bottom: 5px;
    text-align: left;
}

.main-content h2 i {
    margin-right: 10px;
    color: #6c757d;
}

.main-content .text-muted {
    text-align: left;
}

.main-content .btn-primary {
    background-color: #01043A;
    border-color: #01043A;
    color: white;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 4px !important;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin-top: 10px;
}

.main-content .btn-primary:hover {
    background-color: #2980b9;
    border-color: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.main-content .btn-primary i {
    margin-right: 8px;
}

.main-content .alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 8px;
    transition: opacity 0.5s ease-out;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.main-content .alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.main-content .alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.main-content .alert-info {
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

.main-content .alert i {
    margin-right: 10px;
}

.main-content .alert-info i {
    font-size: 1.2rem;
    color: #01043A;
}

.main-content .card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    height: 100%;
    overflow: hidden;
    position: relative;
}

.main-content .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
}

.main-content .card-header {
    background-color: #01043A;
    color: white;
    font-weight: 600;
    padding: 12px 15px;
    border-bottom: none;
    font-size: 16px;
}

.main-content .card-header i {
    margin-right: 10px;
}

.main-content .card-body {
    padding: 20px;
    background-color: white;
    display: flex;
    flex-direction: column;
}

.main-content .card-body p {
    margin-bottom: 10px;
    color: #555;
    font-size: 14px;
}

.main-content .card-body p strong {
    color: #333;
    font-weight: 600;
    margin-right: 5px;
}

.main-content .archived-badge {
    display: inline-block;
    background-color: #6c757d;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 10px;
}

.main-content .text-center {
    text-align: center !important;
    margin-top: 15px;
}

.main-content .btn-action {
    font-size: 13px;
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-left: 5px;
}

.main-content .btn-success {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

.main-content .btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.main-content .file-icon {
    font-size: 40px;
    margin-bottom: 15px;
    color: #3498db;
}

.main-content .file-info {
    flex-grow: 1;
}

.main-content .file-actions {
    margin-top: 15px;
}

.main-content .card::before {
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

@media (max-width: 768px) {
    .main-content .col-md-6 {
        margin-bottom: 20px;
    }
    
    .main-content .text-center {
        text-align: center !important;
    }
    
    .main-content .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .main-content .page-header div:last-child {
        margin-top: 15px;
    }
}

/* Modal specific styles */
.modal-header.bg-success {
    background-color: #28a745 !important;
    color: white !important;
}

.modal-header.bg-danger {
    background-color: #dc3545 !important;
    color: white !important;
}

.modal-header .btn-close {
    color: white;
    opacity: 0.8;
}

.modal-header .btn-close:hover {
    opacity: 1;
}

.modal-body p {
    margin-bottom: 15px;
}

.modal-footer .btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.modal-footer .btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.modal-footer .btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.modal-footer .btn {
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 4px;
}

.modal-footer .btn i {
    margin-right: 5px;
}
</style>

<script>
    // Function to populate the recover modal with file data
    function populateRecoverModal(fileId, fileName) {
        document.getElementById('recover_file_id').value = fileId;
        document.getElementById('recover_file_name').textContent = fileName;
    }
    
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
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

