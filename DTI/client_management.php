
<?php
// Include database connection
include("dbcon.php");

// Set page-specific variables
$pageTitle = "DTI Client Management";
$currentPage = "Client Management";

// Handle client operations
$message = '';
$messageType = '';

// Add new client
if (isset($_POST['add_client'])) {
    // Get form data
    $name = trim($_POST['clientName']);
    $type = trim($_POST['clientType']);
    $region = trim($_POST['clientRegion']);
    $contact = trim($_POST['clientContact']);
    $email = trim($_POST['clientEmail']);
    $address = trim($_POST['clientAddress']);
    
    // Additional fields for citizen type
    $sex = ($type == 'citizen' && isset($_POST['clientSex'])) ? trim($_POST['clientSex']) : NULL;
    $age = ($type == 'citizen' && isset($_POST['clientAge'])) ? intval($_POST['clientAge']) : NULL;
    
    // Generate unique client ID based on type
    $prefix = '';
    switch ($type) {
        case 'citizen':
            $prefix = 'DTI-C-';
            break;
        case 'business':
            $prefix = 'DTI-B-';
            break;
        case 'government':
            $prefix = 'DTI-G-';
            break;
    }
    
// Get the last ID from the database to create a sequential number
$stmt = $conn->prepare("SELECT MAX(id) as max_id FROM clients");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$lastId = $row['max_id'] ?? 0;
$newId = $lastId + 1;
$uniqueId = $prefix . sprintf('%03d', $newId); // Format as 001, 002, etc.
$stmt->close();
    
    // Basic validation
    if (empty($name) || empty($type) || empty($region) || empty($contact) || empty($email)) {
        $message = "Please fill all required fields.";
        $messageType = "danger";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM clients WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $message = "A client with this email already exists.";
            $messageType = "danger";
        } else {
            // Insert into database
                    $stmt = $conn->prepare("INSERT INTO clients (name, business_name, address, contact_number, email, business_type, business_status, date_registered) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                    // Map your variables to the correct columns
                    // Assuming:
                    // $type maps to business_type
                    // $region maps to business_status (or you might need to adjust this)
                    // $contact maps to contact_number
                    // $sex and $age might need to be stored in a different way or added to the table

                    $business_name = $uniqueId; // Store the unique ID in business_name or adjust as needed
                    $business_type = $type;
                    $business_status = $region;
                    $contact_number = $contact;
                    $date_registered = date('Y-m-d'); // Current date if not provided

                    $stmt->bind_param("ssssssss", 
                    $name, 
                    $business_name, 
                    $address, 
                    $contact_number, 
                    $email, 
                    $business_type, 
                    $business_status, 
                    $date_registered
                    );

                    if ($stmt->execute()) {
                    $clientId = $stmt->insert_id;

                    // Handle certificate upload if provided
                    if (isset($_FILES['clientCertificate']) && $_FILES['clientCertificate']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['clientCertificate'];

                    // File validation
                    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $fileType = finfo_file($finfo, $file["tmp_name"]);
                    finfo_close($finfo);

                    if (!in_array($fileType, $allowedTypes)) {
                    $message = "Client added successfully, but certificate upload failed: Invalid file type.";
                    $messageType = "warning";
                    } else if ($file["size"] > 5 * 1024 * 1024) {
                    $message = "Client added successfully, but certificate upload failed: File is too large.";
                    $messageType = "warning";
                    } else {
                    // Create unique filename
                    $targetDir = "uploads/client_certificates/";
                    if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                    }

                    $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                    $uniqueFilename = $uniqueId . '_cert_' . uniqid() . '.' . $fileExtension;
                    $targetFile = $targetDir . $uniqueFilename;

                    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                    // Insert certificate into database
                    $certName = pathinfo($file["name"], PATHINFO_FILENAME);
                    $certStmt = $conn->prepare("INSERT INTO client_certificates (client_id, certificate_name, file_path) VALUES (?, ?, ?)");
                    $certStmt->bind_param("iss", $clientId, $certName, $targetFile);

                    if ($certStmt->execute()) {
                    $message = "Client and certificate added successfully!";
                    $messageType = "success";
                    } else {
                    $message = "Client added successfully, but certificate upload failed: " . $certStmt->error;
                    $messageType = "warning";
                    }
                    $certStmt->close();
                    } else {
                    $message = "Client added successfully, but certificate upload failed.";
                    $messageType = "warning";
                    }
                    }
                    } else {
                    $message = "Client added successfully!";
                    $messageType = "success";
                    }
                    } else {
                    $message = "Error: " . $stmt->error;
$messageType = "danger";
}
$stmt->close();

        }
    }
}

// Edit client
if (isset($_POST['edit_client'])) {
    $id = $_POST['editClientId'];
    $name = trim($_POST['editClientName']);
    $type = trim($_POST['editClientType']);
    $region = trim($_POST['editClientRegion']);
    $contact = trim($_POST['editClientContact']);
    $email = trim($_POST['editClientEmail']);
    $address = trim($_POST['editClientAddress']);
    
    // Additional fields for citizen type
    $sex = ($type == 'citizen' && isset($_POST['editClientSex'])) ? trim($_POST['editClientSex']) : NULL;
    $age = ($type == 'citizen' && isset($_POST['editClientAge'])) ? intval($_POST['editClientAge']) : NULL;
    
    // Basic validation
    if (empty($name) || empty($type) || empty($region) || empty($contact) || empty($email)) {
        $message = "Please fill all required fields.";
        $messageType = "danger";
    } else {
        // Check if email already exists for other clients
        $stmt = $conn->prepare("SELECT id FROM clients WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $message = "Another client with this email already exists.";
            $messageType = "danger";
        } else {
            // Update client in database
            $stmt = $conn->prepare("UPDATE clients SET name = ?, type = ?, region = ?, contact = ?, email = ?, address = ?, sex = ?, age = ? WHERE id = ?");
            $stmt->bind_param("sssssssii", $name, $type, $region, $contact, $email, $address, $sex, $age, $id);
            
            if ($stmt->execute()) {
                // Handle certificate upload if provided
                if (isset($_FILES['editClientCertificate']) && $_FILES['editClientCertificate']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['editClientCertificate'];
                    
                    // File validation
                    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $fileType = finfo_file($finfo, $file["tmp_name"]);
                    finfo_close($finfo);
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        $message = "Client updated successfully, but certificate upload failed: Invalid file type.";
                        $messageType = "warning";
                    } else if ($file["size"] > 5 * 1024 * 1024) {
                        $message = "Client updated successfully, but certificate upload failed: File is too large.";
                        $messageType = "warning";
                    } else {
                        // Get client unique ID
                        $idStmt = $conn->prepare("SELECT unique_id FROM clients WHERE id = ?");
                        $idStmt->bind_param("i", $id);
                        $idStmt->execute();
                        $idResult = $idStmt->get_result();
                        $idRow = $idResult->fetch_assoc();
                        $uniqueId = $idRow['unique_id'];
                        $idStmt->close();
                        
                        // Create unique filename
                        $targetDir = "uploads/client_certificates/";
                        if (!file_exists($targetDir)) {
                            mkdir($targetDir, 0777, true);
                        }
                        
                        $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                        $uniqueFilename = $uniqueId . '_cert_' . uniqid() . '.' . $fileExtension;
                        $targetFile = $targetDir . $uniqueFilename;
                        
                        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                            // Insert certificate into database
                            $certName = pathinfo($file["name"], PATHINFO_FILENAME);
                            $certStmt = $conn->prepare("INSERT INTO client_certificates (client_id, name, file_path) VALUES (?, ?, ?)");
                            $certStmt->bind_param("iss", $id, $certName, $targetFile);
                            
                            if ($certStmt->execute()) {
                                $message = "Client and new certificate updated successfully!";
                                $messageType = "success";
                            } else {
                                $message = "Client updated successfully, but certificate upload failed: " . $certStmt->error;
                                $messageType = "warning";
                            }
                            $certStmt->close();
                        } else {
                            $message = "Client updated successfully, but certificate upload failed.";
                            $messageType = "warning";
                        }
                    }
                } else {
                    $message = "Client updated successfully!";
                    $messageType = "success";
                }
            } else {
                $message = "Error: " . $stmt->error;
                $messageType = "danger";
            }
            $stmt->close();
        }
    }
}

// Delete client
if (isset($_POST['delete_client'])) {
    $id = $_POST['deleteClientId'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Get all certificates to delete files
        $certStmt = $conn->prepare("SELECT file_path FROM client_certificates WHERE client_id = ?");
        $certStmt->bind_param("i", $id);
        $certStmt->execute();
        $certResult = $certStmt->get_result();
        
        // Delete certificate files
        while ($certRow = $certResult->fetch_assoc()) {
            $filePath = $certRow['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $certStmt->close();
        
        // Delete certificates from database
        $deleteCertStmt = $conn->prepare("DELETE FROM client_certificates WHERE client_id = ?");
        $deleteCertStmt->bind_param("i", $id);
        $deleteCertStmt->execute();
        $deleteCertStmt->close();
        
        // Delete client from database
        $deleteClientStmt = $conn->prepare("DELETE FROM clients WHERE id = ?");
        $deleteClientStmt->bind_param("i", $id);
        $deleteClientStmt->execute();
        $deleteClientStmt->close();
        
        // Commit transaction
        $conn->commit();
        
        $message = "Client and all associated certificates deleted successfully!";
        $messageType = "success";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
        $messageType = "danger";
    }
}

// Fetch clients for display
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 10;
$offset = ($page - 1) * $itemsPerPage;

// Prepare the base query
$baseQuery = "SELECT * FROM clients";
$countQuery = "SELECT COUNT(*) as total FROM clients";

// Add search condition if search term is provided
if (!empty($search)) {
    $searchTerm = "%$search%";
    $baseQuery .= " WHERE name LIKE ? OR email LIKE ? OR unique_id LIKE ?";
    $countQuery .= " WHERE name LIKE ? OR email LIKE ? OR unique_id LIKE ?";
}

// Add pagination
$baseQuery .= " ORDER BY id DESC LIMIT ?, ?";

// Get total count for pagination
if (!empty($search)) {
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
} else {
    $countStmt = $conn->prepare($countQuery);
}

$countStmt->execute();
$countResult = $countStmt->get_result();
$countRow = $countResult->fetch_assoc();
$totalClients = $countRow['total'];
$totalPages = ceil($totalClients / $itemsPerPage);
$countStmt->close();

// Get clients for current page
if (!empty($search)) {
    $stmt = $conn->prepare($baseQuery);
    $stmt->bind_param("sssii", $searchTerm, $searchTerm, $searchTerm, $offset, $itemsPerPage);
} else {
    $stmt = $conn->prepare($baseQuery);
    $stmt->bind_param("ii", $offset, $itemsPerPage);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sheet Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="img/logowhite.png" type="image/x-icon">
    <link rel="stylesheet" href="client_management.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


</head>
<body>

<?php
// Include the header and sidebar
include('header.php');
include('sidebar.php');
?>

<div class="main-content" style="margin-top: 120px;">
    <div class="container">
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <!-- Search and Add Client Section -->
        <div class="row mb-4 align-items-center">
        <div class="col-md-6 text-md-start">
                <a href="#" class="btn add-client-btn" data-bs-toggle="modal" data-bs-target="#addClientModal">
                    <i class="fas fa-plus"></i> Add New Client
                </a>
            </div>
            <div class="col-md-6">
                <form method="GET" action="">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control search-input border-start-0" 
                               placeholder="Search Client's Name" value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary-custom">Search</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Clients Table -->
        <div class="client-table">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 10%">ID</th>
                        <th style="width: 25%">Client Name</th>
                        <th style="width: 15%">Type</th>
                        <th style="width: 15%">Region</th>
                        <th style="width: 15%">Contact</th>
                        <th style="width: 20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($client = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($client['business_name'] ?? 'N/A'); // Using business_name instead of unique_id ?></td>
        <td>
            <div class="client-name"><?php echo htmlspecialchars($client['name']); ?></div>
            <div class="client-email"><?php echo htmlspecialchars($client['email']); ?></div>
        </td>
        <td>
            <?php
            $typeClass = '';
            $typeLabel = '';
            
            // Using business_type instead of type
            $businessType = $client['business_type'] ?? '';
            
            switch($businessType) {
                case 'citizen':
                    $typeClass = 'client-type-citizen';
                    $typeLabel = 'Citizen';
                    break;
                case 'business':
                    $typeClass = 'client-type-business';
                    $typeLabel = 'Business';
                    break;
                case 'government':
                    $typeClass = 'client-type-government';
                    $typeLabel = 'Government';
                    break;
                default:
                    $typeClass = '';
                    $typeLabel = $businessType;
            }
            ?>
            <span class="client-type-badge <?php echo $typeClass; ?>"><?php echo $typeLabel; ?></span>
        </td>
        <td><?php echo htmlspecialchars($client['business_status'] ?? 'N/A'); // Using business_status instead of region ?></td>
        <td><?php echo htmlspecialchars($client['contact_number'] ?? 'N/A'); // Using contact_number instead of contact ?></td>
        <td>
            <button class="btn btn-primary-custom btn-sm btn-action me-1"
                    data-bs-toggle="modal"
                    data-bs-target="#viewClientModal"
                    data-client-id="<?php echo $client['id']; ?>"
                    data-client-name="<?php echo htmlspecialchars($client['name']); ?>"
                    data-client-type="<?php echo htmlspecialchars($client['business_type'] ?? ''); ?>"
                    data-client-type-label="<?php echo $typeLabel; ?>"
                    data-client-unique-id="<?php echo htmlspecialchars($client['business_name'] ?? ''); ?>"
                    data-client-region="<?php echo htmlspecialchars($client['business_status'] ?? ''); ?>"
                    data-client-contact="<?php echo htmlspecialchars($client['contact_number'] ?? ''); ?>"
                    data-client-email="<?php echo htmlspecialchars($client['email']); ?>"
                    data-client-address="<?php echo htmlspecialchars($client['address'] ?? ''); ?>">
                <i class="fas fa-eye"></i> View
            </button>
            <button class="btn btn-warning-custom btn-sm btn-action me-1"
                    data-bs-toggle="modal"
                    data-bs-target="#editClientModal"
                    data-client-id="<?php echo $client['id']; ?>"
                    data-client-name="<?php echo htmlspecialchars($client['name']); ?>"
                    data-client-type="<?php echo htmlspecialchars($client['business_type'] ?? ''); ?>"
                    data-client-region="<?php echo htmlspecialchars($client['business_status'] ?? ''); ?>"
                    data-client-contact="<?php echo htmlspecialchars($client['contact_number'] ?? ''); ?>"
                    data-client-email="<?php echo htmlspecialchars($client['email']); ?>"
                    data-client-address="<?php echo htmlspecialchars($client['address'] ?? ''); ?>">
                <i class="fas fa-edit"></i> Edit
            </button>
            <a href="generator.php?client_id=<?php echo $client['id']; ?>" class="btn btn-success-custom btn-sm btn-action me-1">
                <i class="fas fa-certificate"></i> Certificate
            </a>
            <button class="btn btn-danger-custom btn-sm btn-action"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteClientModal"
                    data-client-id="<?php echo $client['id']; ?>"
                    data-client-name="<?php echo htmlspecialchars($client['name']); ?>">
                <i class="fas fa-trash-alt"></i> Delete
            </button>
        </td>
    </tr>
<?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <?php echo empty($search) ? 'No clients found in the database.' : 'No clients match your search criteria.'; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Client pagination">
                    <ul class="pagination justify-content-end">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>" tabindex="-1" <?php echo ($page <= 1) ? 'aria-disabled="true"' : ''; ?>>Previous</a>
                        </li>
                        
                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>" <?php echo ($page >= $totalPages) ? 'aria-disabled="true"' : ''; ?>>Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>

        <!-- View Client Modal -->
        <div class="modal fade" id="viewClientModal" tabindex="-1" aria-labelledby="viewClientModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewClientModalLabel">Client Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Left side - Client Info -->
                            <div class="col-md-6">
                                <div class="client-details-container">
                                    <div class="client-details-header">
                                        <h4 class="client-details-title" id="viewClientName">Loading...</h4>
                                        <p class="client-details-subtitle">
                                            <span class="badge bg-primary" id="viewClientType">Loading...</span>
                                        </p>
                                    </div>
                                    
                                    <div class="client-detail-row">
                                        <div class="client-detail-label">Unique ID:</div>
                                        <div class="client-detail-value" id="viewClientUniqueId">Loading...</div>
                                    </div>
                                    
                                    <div class="client-detail-row citizen-only">
                                        <div class="client-detail-label">Sex:</div>
                                        <div class="client-detail-value" id="viewClientSex">Loading...</div>
                                    </div>
                                    
                                    <div class="client-detail-row citizen-only">
                                        <div class="client-detail-label">Age:</div>
                                        <div class="client-detail-value" id="viewClientAge">Loading...</div>
                                    </div>
                                    
                                    <div class="client-detail-row">
                                        <div class="client-detail-label">Region of Residence:</div>
                                        <div class="client-detail-value" id="viewClientRegion">Loading...</div>
                                    </div>
                                    
                                    <div class="client-detail-row">
                                        <div class="client-detail-label">Contact Number:</div>
                                        <div class="client-detail-value" id="viewClientContact">Loading...</div>
                                    </div>
                                    
                                    <div class="client-detail-row">
                                        <div class="client-detail-label">Email Address:</div>
                                        <div class="client-detail-value" id="viewClientEmail">Loading...</div>
                                    </div>
                                    
                                    <div class="client-detail-row">
                                        <div class="client-detail-label">Address:</div>
                                        <div class="client-detail-value" id="viewClientAddress">Loading...</div>
                                    </div>
                                    
                                    <div class="mt-4" id="certificatesList">
                                        <h5 class="mb-3">Client Certificates</h5>
                                        <div class="list-group" id="viewClientCertificates">
                                            <!-- Certificates will be loaded here via AJAX -->
                                            <div class="text-center py-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mt-2">Loading certificates...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right side - Feedback and Additional Info -->
                            <div class="col-md-6">
                                <div class="client-details-container">
                                    <div class="client-details-header">
                                        <h4 class="client-details-title">Client Feedback</h4>
                                        <p class="client-details-subtitle" id="viewClientFeedbackDate">Loading feedback data...</p>
                                    </div>
                                    
                                    <div id="viewClientFeedback">
                                        <!-- Feedback will be loaded here via AJAX -->
                                        <div class="text-center py-3">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2">Loading feedback data...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning-custom" id="viewToEditBtn">
                            <i class="fas fa-edit me-2"></i> Edit Client
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Client Modal -->
        <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addClientForm" method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="add_client" value="1">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Basic Information</h5>
                                    
                                    <div class="form-group mb-3">
                                        <label for="clientName" class="form-label">Full Name / Organization Name</label>
                                        <input type="text" class="form-control" id="clientName" name="clientName" required>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="clientType" class="form-label">Client Type</label>
                                        <select class="form-select" id="clientType" name="clientType" required>
                                            <option value="" selected disabled>Select client type</option>
                                            <option value="citizen">Citizen</option>
                                            <option value="business">Business</option>
                                            <option value="government">Government</option>
                                        </select>
                                    </div>
                                    
                                    <div class="row citizen-only">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="clientSex" class="form-label">Sex</label>
                                                <select class="form-select" id="clientSex" name="clientSex">
                                                    <option value="" selected disabled>Select sex</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="clientAge" class="form-label">Age</label>
                                                <input type="number" class="form-control" id="clientAge" name="clientAge" min="1" max="120">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="clientRegion" class="form-label">Region of Residence</label>
                                        <select class="form-select" id="clientRegion" name="clientRegion" required>
                                            <option value="" selected disabled>Select region</option>
                                            <option value="NCR">NCR</option>
                                            <option value="CAR">CAR</option>    
                                            <option value="Region I">Region I</option>
                                            <option value="Region II">Region II</option>
                                            <option value="Region III">Region III</option>
                                            <option value="Region IV-A">Region IV-A</option>
                                            <option value="Region IV-B">Region IV-B</option>
                                            <option value="Region V">Region V</option>
                                            <option value="Region VI">Region VI</option>
                                            <option value="Region VII">Region VII</option>
                                            <option value="Region VIII">Region VIII</option>
                                            <option value="Region IX">Region IX</option>
                                            <option value="Region X">Region X</option>
                                            <option value="Region XI">Region XI</option>
                                            <option value="Region XII">Region XII</option>
                                            <option value="Region XIII">Region XIII</option>
                                            <option value="BARMM">BARMM</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Contact Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Contact Information</h5>
                                    
                                    <div class="form-group mb-3">
                                        <label for="clientContact" class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" id="clientContact" name="clientContact" required>
                                        <div class="form-text">Mobile number or landline</div>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="clientEmail" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="clientEmail" name="clientEmail" required>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="clientAddress" class="form-label">Complete Address</label>
                                        <textarea class="form-control" id="clientAddress" name="clientAddress" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="clientCertificate" class="form-label">Upload Certificate (Optional)</label>
                                        <input type="file" class="form-control" id="clientCertificate" name="clientCertificate">
                                        <div class="form-text">You can upload client certificates later</div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="fas fa-plus me-2"></i> Add Client
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Client Modal -->
        <div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editClientModalLabel">Edit Client</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editClientForm" method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="edit_client" value="1">
                            <input type="hidden" name="editClientId" id="editClientId" value="">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Basic Information</h5>
                                    
                                    <div class="form-group mb-3">
                                        <label for="editClientName" class="form-label">Full Name / Organization Name</label>
                                        <input type="text" class="form-control" id="editClientName" name="editClientName" required>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="editClientType" class="form-label">Client Type</label>
                                        <select class="form-select" id="editClientType" name="editClientType" required>
                                            <option value="citizen">Citizen</option>
                                            <option value="business">Business</option>
                                            <option value="government">Government</option>
                                        </select>
                                    </div>
                                    
                                    <div class="row citizen-only">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="editClientSex" class="form-label">Sex</label>
                                                <select class="form-select" id="editClientSex" name="editClientSex">
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="editClientAge" class="form-label">Age</label>
                                                <input type="number" class="form-control" id="editClientAge" name="editClientAge" min="1" max="120">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="editClientRegion" class="form-label">Region of Residence</label>
                                        <select class="form-select" id="editClientRegion" name="editClientRegion" required>
                                            <option value="NCR">NCR</option>
                                            <option value="CAR">CAR</option>
                                            <option value="Region I">Region I</option>
                                            <option value="Region II">Region II</option>
                                            <option value="Region III">Region III</option>
                                            <option value="Region IV-A">Region IV-A</option>
                                            <option value="Region IV-B">Region IV-B</option>
                                            <option value="Region V">Region V</option>
                                            <option value="Region VI">Region VI</option>
                                            <option value="Region VII">Region VII</option>
                                            <option value="Region VIII">Region VIII</option>
                                            <option value="Region IX">Region IX</option>
                                            <option value="Region X">Region X</option>
                                            <option value="Region XI">Region XI</option>
                                            <option value="Region XII">Region XII</option>
                                            <option value="Region XIII">Region XIII</option>
                                            <option value="BARMM">BARMM</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Contact Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">Contact Information</h5>
                                    
                                    <div class="form-group mb-3">
                                        <label for="editClientContact" class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" id="editClientContact" name="editClientContact" required>
                                        <div class="form-text">Mobile number or landline</div>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="editClientEmail" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="editClientEmail" name="editClientEmail" required>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="editClientAddress" class="form-label">Complete Address</label>
                                        <textarea class="form-control" id="editClientAddress" name="editClientAddress" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="editClientCertificate" class="form-label">Upload New Certificate (Optional)</label>
                                        <input type="file" class="form-control" id="editClientCertificate" name="editClientCertificate">
                                        <div class="form-text">Leave empty to keep existing certificates</div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Client Modal -->
        <div class="modal fade" id="deleteClientModal" tabindex="-1" aria-labelledby="deleteClientModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteClientModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete the client "<span id="deleteClientName">Loading...</span>"?</p>
                        <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> This action cannot be undone. All client data and certificates will be permanently removed.</p>
                        <form id="deleteClientForm" method="POST" action="">
                            <input type="hidden" name="delete_client" value="1">
                            <input type="hidden" id="deleteClientId" name="deleteClientId" value="">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" form="deleteClientForm" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-2"></i> Delete Client
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="client_management.js"></script>
<script src="darkmode.js"></script>

<?php
// Include the footer
include('footer.php');
?>

</body>
</html>



