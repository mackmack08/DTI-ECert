<?php
session_start();
include('dbcon.php');

// Check if user is authenticated
if(!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== TRUE) {
    header('Location: login.php');
    exit();
}

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Certificates</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            padding-top: 40px;
        }

        .card {
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            padding: 20px;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 18px;
            padding: 10px 15px;
            border-radius: 4px 4px 0 0;
        }

        .btn {
            margin: 5px;
        }

        .table th {
            background-color: #007bff;
            color: white;
        }

        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php
    include('header.php');
    include('sidebar.php');
    ?>
    
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-file-earmark-pdf"></i> View Certificates
                        </div>
                        <div class="card-body">
                            <h4 class="mb-3">Certificates for: <b><?php echo htmlspecialchars($file_data['file_name']); ?></b></h4>
                            
                            <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!$certificates_exist): ?>
                                <div class="alert alert-warning">
                                    <p><strong>Notice:</strong> No certificates have been generated for this file yet.</p>
                                    <a href="generate_all_certificates.php?file_id=<?php echo $file_id; ?>" class="btn btn-primary">
                                        <i class="bi bi-file-earmark-plus"></i> Generate Certificates
                                    </a>
                                </div>
                            <?php else: ?>
                                <!-- Download ZIP if available -->
                                <?php if (!empty($file_data['zip_file'])): ?>
                                <div class="text-center mb-4">
                                    <a href="<?php echo htmlspecialchars($file_data['zip_file']); ?>" class="btn btn-primary btn-lg">
                                        <i class="bi bi-download"></i> Download All Certificates (ZIP)
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Form for sending certificates -->
                                <form method="post" action="send_certificate.php" id="certificateForm">
                                    <input type="hidden" name="file_id" value="<?php echo $file_id; ?>">
                                    
                                
                                    
                                    <!-- Action Buttons -->
                                    <div class="action-buttons">
                                            <!-- Select All checkbox -->
                                    <div class="select-all-container" style="margin-right: 10%;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                            <label class="form-check-label" for="selectAll">Select All</label>
                                        </div>
                                        <span class="selected-count" id="selectedCount">0 selected</span>
                                    </div>

                                        <button type="submit" name="send_selected" class="btn btn-warning" id="sendSelectedBtn" disabled>
                                            <i class="bi bi-send"></i> Send Selected
                                        </button>
                                        
                                        <button type="submit" name="send_all" class="btn btn-success">
                                            <i class="bi bi-send-check-fill"></i> Send All Certificates
                                        </button>
                                    </div>
                                
                                    <!-- Individual Certificates -->
                                    <h5 class="mb-3 text-center">Individual Certificates</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped text-center">
                                            <thead>
                                                <tr>
                                                    <th width="5%">Select</th>
                                                    <th width="5%">#</th>
                                                    <th width="30%">Client Name</th>
                                                    <th width="15%">Reference ID</th>
                                                    <th width="20%">Email</th>
                                                    <th width="25%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $validClientCount = 0;
                                                foreach ($clients as $index => $client):
                                                    // Fetch the file path from the database
                                                    $file_path = $client['file_path'] ?? '';
                                                    
                                                    // Skip clients without certificates
                                                    if (empty($file_path) || $file_path === '#') {
                                                        continue;
                                                    }
                                                    
                                                    $validClientCount++;
                                                    
                                                    // Ensure the file path is a full URL
                                                    if (!preg_match('/^https?:\/\//', $file_path)) {
                                                        // Get the directory where the script is running
                                                        $baseDir = dirname($_SERVER['SCRIPT_NAME']);
                                                        $baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                                                        
                                                        // If baseDir is root, avoid double slash
                                                        if ($baseDir == '/') $baseDir = '';
                                                        
                                                        // Create proper URL
                                                        $file_path = $baseURL . $baseDir . '/' . ltrim($file_path, '/');
                                                    }
                                                ?>
                                                <tr class="align-middle">
                                                        <td style="width: 40px;">
                                                            <div class="form-check">
                                                                <input class="form-check-input client-checkbox" type="checkbox"
                                                                    name="selected_clients[]" value="<?php echo $client['id']; ?>"
                                                                    id="client<?php echo $client['id']; ?>">
                                                            </div>
                                                        </td>
                                                        <td style="width: 50px;"><?php echo $validClientCount; ?></td>
                                                        <td style="width: 25%;"><?php echo htmlspecialchars($client['client_name']); ?></td>
                                                        <td style="width: 15%;"><?php echo htmlspecialchars($client['reference_id'] ?? 'N/A'); ?></td>
                                                        <td style="width: 20%;"><?php echo htmlspecialchars($client['email']); ?></td>
                                                        <td style="width: 30%;">
                                                            <div class="d-flex flex-nowrap">
                                                                <!-- View button opens the certificate in a new tab -->
                                                                <a href="<?php echo htmlspecialchars($file_path); ?>" class="btn btn-sm btn-outline-primary me-1" target="_blank">
                                                                    <i class="bi bi-eye"></i> View
                                                                </a>
                                                                <!-- Download button allows the user to download the certificate -->
                                                                <a href="<?php echo htmlspecialchars($file_path); ?>" class="btn btn-sm btn-outline-success me-1" download>
                                                                    <i class="bi bi-download"></i> Download
                                                                </a>
                                                                <!-- Individual send button -->
                                                                <button type="button" class="btn btn-sm btn-outline-warning send-individual"
                                                                        data-client-id="<?php echo $client['id']; ?>"
                                                                        data-client-name="<?php echo htmlspecialchars($client['client_name']); ?>">
                                                                    <i class="bi bi-send"></i> Send
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            <?php endif; ?>
                                
                            <!-- Back Button -->
                            <div class="text-center mt-4">
                                <a href="client_certificates.php?file_id=<?php echo $file_id; ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Certificate Management
                                </a>
                            </div>
                        </div> <!-- End Card Body -->
                    </div> <!-- End Card -->
                </div> <!-- End Col -->
            </div> <!-- End Row -->
        </div> <!-- End Container -->
    </div> <!-- End Main Content -->
    
    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmSendModal" tabindex="-1" aria-labelledby="confirmSendModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmSendModalLabel">Confirm Send</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmMessage">Are you sure you want to send this certificate?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSendBtn">Send</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('footer.php'); ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select All functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const clientCheckboxes = document.querySelectorAll('.client-checkbox');
            const sendSelectedBtn = document.getElementById('sendSelectedBtn');
            const selectedCountDisplay = document.getElementById('selectedCount');
            
            // Function to update the send selected button state and count display
            function updateSelectionState() {
                const checkedCount = document.querySelectorAll('.client-checkbox:checked').length;
                sendSelectedBtn.disabled = checkedCount === 0;
                
                if (checkedCount > 0) {
                    sendSelectedBtn.innerHTML = `<i class="bi bi-send"></i> Send ${checkedCount} Selected`;
                    selectedCountDisplay.textContent = `${checkedCount} selected`;
                } else {
                    sendSelectedBtn.innerHTML = `<i class="bi bi-send"></i> Send Selected`;
                    selectedCountDisplay.textContent = `0 selected`;
                }
            }
            
            // Select All checkbox event
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    
                    clientCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    
                    updateSelectionState();
                });
            }
            
            // Individual checkboxes event
            clientCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Update "Select All" checkbox
                                        // Update "Select All" checkbox
                                        const allChecked = document.querySelectorAll('.client-checkbox:checked').length === clientCheckboxes.length;
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = allChecked;
                        selectAllCheckbox.indeterminate = !allChecked && document.querySelectorAll('.client-checkbox:checked').length > 0;
                    }
                    
                    updateSelectionState();
                });
            });
            
            // Individual send buttons
            const sendIndividualButtons = document.querySelectorAll('.send-individual');
            const confirmSendModal = new bootstrap.Modal(document.getElementById('confirmSendModal'));
            const confirmMessage = document.getElementById('confirmMessage');
            const confirmSendBtn = document.getElementById('confirmSendBtn');
            
            sendIndividualButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const clientId = this.getAttribute('data-client-id');
                    const clientName = this.getAttribute('data-client-name');
                    
                    // Update modal content
                    confirmMessage.textContent = `Are you sure you want to send the certificate to ${clientName}?`;
                    
                    // Set up confirmation button
                    confirmSendBtn.onclick = function() {
                        // Create a temporary form to submit
                        const tempForm = document.createElement('form');
                        tempForm.method = 'POST';
                        tempForm.action = 'send_certificate.php';
                        
                        // Add necessary fields
                        const clientIdInput = document.createElement('input');
                        clientIdInput.type = 'hidden';
                        clientIdInput.name = 'client_id';
                        clientIdInput.value = clientId;
                        
                        const fileIdInput = document.createElement('input');
                        fileIdInput.type = 'hidden';
                        fileIdInput.name = 'file_id';
                        fileIdInput.value = <?php echo $file_id; ?>;
                        
                        // Append inputs to form
                        tempForm.appendChild(clientIdInput);
                        tempForm.appendChild(fileIdInput);
                        
                        // Append form to body and submit
                        document.body.appendChild(tempForm);
                        tempForm.submit();
                        
                        // Hide modal
                        confirmSendModal.hide();
                    };
                    
                    // Show modal
                    confirmSendModal.show();
                });
            });
            
            // Form submission confirmation for sending multiple certificates
            const certificateForm = document.getElementById('certificateForm');
            
            if (certificateForm) {
                certificateForm.addEventListener('submit', function(e) {
                    const isAllSubmit = e.submitter && e.submitter.name === 'send_all';
                    const isSelectedSubmit = e.submitter && e.submitter.name === 'send_selected';
                    
                    if (isAllSubmit) {
                        // Confirm sending all certificates
                        if (!confirm('Are you sure you want to send certificates to ALL clients? This may take some time.')) {
                            e.preventDefault();
                        }
                    } else if (isSelectedSubmit) {
                        // Check if any clients are selected
                        const selectedClients = document.querySelectorAll('.client-checkbox:checked');
                        
                        if (selectedClients.length === 0) {
                            alert('Please select at least one client to send certificates to.');
                            e.preventDefault();
                        } else {
                            // Confirm sending selected certificates
                            if (!confirm(`Are you sure you want to send certificates to ${selectedClients.length} selected client(s)?`)) {
                                e.preventDefault();
                            }
                        }
                    }
                });
            }
            
            // Initialize selection state
            updateSelectionState();
        });
    </script>
</body>
</html>

