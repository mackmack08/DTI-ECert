<?php
ob_start();         // Start output buffering
session_start();
include('dbcon.php');

// Set page-specific variables
$pageTitle = "DTI Client Management";
$currentPage = "Client Management";

// Get all available sheet names for the dropdown
$sheetQuery = "SELECT DISTINCT f.id, f.file_name
               FROM files f
               INNER JOIN clients c ON f.id = c.file_id
               WHERE f.status = 'Unarchived' AND c.status = 'Unarchived'
               ORDER BY f.upload_time DESC";
$sheetResult = $conn->query($sheetQuery);
$availableSheets = [];
if ($sheetResult && $sheetResult->num_rows > 0) {
    while ($row = $sheetResult->fetch_assoc()) {
        $availableSheets[] = [
            'id' => $row['id'],
            'file_name' => $row['file_name']
        ];
    }
}

// Fetch clients for display with filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$selectedSheet = isset($_GET['sheet_id']) ? intval($_GET['sheet_id']) : 0;
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 10;
$offset = ($page - 1) * $itemsPerPage;

// Prepare the base query - now including file_path for certificates
$baseQuery = "SELECT c.id, c.reference_id, c.staff, c.client_name, c.client_type, c.region, c.contact, c.email,
              c.file_id, f.file_name as sheet_name, c.timestamp, c.file_path
              FROM clients c
              LEFT JOIN files f ON c.file_id = f.id
              WHERE c.status = 'Unarchived'";
$countQuery = "SELECT COUNT(*) as total FROM clients c WHERE c.status = 'Unarchived'";

// Add search condition if search term is provided
if (!empty($search)) {
    $searchTerm = "%$search%";
    $baseQuery .= " AND (c.client_name LIKE ? OR c.email LIKE ? OR c.reference_id LIKE ? OR f.file_name LIKE ?)";
    $countQuery .= " AND (c.client_name LIKE ? OR c.email LIKE ? OR c.reference_id LIKE ? OR EXISTS (SELECT 1 FROM files f WHERE f.id = c.file_id AND f.file_name LIKE ?))";
}

// Add sheet filter if selected
if ($selectedSheet > 0) {
    $baseQuery .= " AND c.file_id = ?";
    $countQuery .= " AND c.file_id = ?";
}

// Add date range filter if provided
if (!empty($startDate) && !empty($endDate)) {
    $baseQuery .= " AND DATE(c.timestamp) BETWEEN ? AND ?";
    $countQuery .= " AND DATE(c.timestamp) BETWEEN ? AND ?";
}

// Add pagination
$baseQuery .= " ORDER BY c.id DESC LIMIT ?, ?";

// Prepare count statement with appropriate parameters
$countStmt = $conn->prepare($countQuery);

// Bind parameters based on which filters are active
$bindTypes = "";
$bindParams = [];
if (!empty($search)) {
    $bindTypes .= "ssss";
    $bindParams[] = $searchTerm;
    $bindParams[] = $searchTerm;
    $bindParams[] = $searchTerm;
    $bindParams[] = $searchTerm;
}
if ($selectedSheet > 0) {
    $bindTypes .= "i";
    $bindParams[] = $selectedSheet;
}
if (!empty($startDate) && !empty($endDate)) {
    $bindTypes .= "ss";
    $bindParams[] = $startDate;
    $bindParams[] = $endDate;
}

// Only bind parameters if we have any
if (!empty($bindParams)) {
    $countStmt->bind_param($bindTypes, ...$bindParams);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$countRow = $countResult->fetch_assoc();
$totalClients = $countRow['total'];
$totalPages = ceil($totalClients / $itemsPerPage);
$countStmt->close();

// Get clients for current page
$stmt = $conn->prepare($baseQuery);
// Bind parameters for the main query (same as count query plus pagination)
$bindTypes .= "ii"; // Add two integers for LIMIT ?, ?
$bindParams[] = $offset;
$bindParams[] = $itemsPerPage;
if (!empty($bindParams)) {
    $stmt->bind_param($bindTypes, ...$bindParams);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="img/logos.png" type="image/x-icon">
    <link rel="stylesheet" href="client_management.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Certificate view link styling */
        .certificate-view-link {
            display: inline-block;
            background-color: #17a2b8;
            color: white !important;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            text-decoration: none;
            white-space: nowrap;
            transition: background-color 0.2s ease;
        }
        
        .certificate-view-link:hover {
            background-color: #138496;
            color: white !important;
            text-decoration: none;
        }
        
        /* Compact filter section styling */
        .filter-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .filter-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #495057;
        }
        .filter-form .form-control,
        .filter-form .form-select {
            font-size: 0.85rem;
            padding: 0.25rem 0.5rem;
        }
        /* Blue background with white text for input group labels */
        .filter-form .input-group-text {
            font-size: 0.85rem;
            padding: 0.25rem 0.5rem;
            background-color: #01043A;
            color: white;
        }
        .filter-badge {
            display: inline-block;
            background-color: #e9ecef;
            color: #495057;
            font-size: 0.7rem;
            padding: 0.15rem 0.4rem;
            border-radius: 0.25rem;
            margin-right: 0.4rem;
            margin-bottom: 0.2rem;
        }
      
        .certificate-view-link {
            color: #28a745;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .certificate-view-link:hover {
            color: #218838;
            text-decoration: underline;
        }

        .no-certificate {
            color: #6c757d;
            font-style: italic;
            font-size: 0.9rem;
        }


        .active-filters {
            margin-top: 5px;
            font-size: 0.8rem;
        }
        .btn-group-sm>.btn, .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            background-color: #01043A;
        }
        
        .timestamp-col {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        /* Truncate long text with ellipsis */
        .truncate {
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }
        
        /* Specific column width controls */
        .client-table th, .client-table td {
            vertical-align: middle;
        }
        
        .client-name {
            font-weight: 600;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .client-email {
            font-size: 0.85rem;
            color: #6c757d;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .client-type {
            max-width: 100px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .reference-id {
            max-width: 100px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Fixed width for action buttons column */
        .action-buttons {
            display: flex;
            flex-wrap: nowrap;
            justify-content: flex-start;
            min-width: 220px;
        }
        
        /* Add tooltip functionality */
        [data-tooltip] {
            position: relative;
            cursor: pointer;
        }
        
        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 10;
            margin-bottom: 5px;
        }
        
        /* Certificate preview styles */
        .certificate-preview {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .certificate-actions {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }
        .certificate-embed {
            background-color: white;
            border-radius: 4px;
            overflow: hidden;
            height: 500px;
        }
        .certificate-embed iframe {
            display: block;
            width: 100%;
            height: 100%;
            border: none;
        }
        
        /* No certificate message */
        .no-certificate {
            color: #6c757d;
            font-size: 0.8rem;
            font-style: italic;
        }
    </style>
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
        
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-title mb-2">
                <i class="fas fa-filter me-2"></i> Filter Clients
            </div>
            
            <form method="GET" action="" class="filter-form" id="filterForm">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Search</span>
                            <input type="text" name="search" id="search" class="form-control form-control-sm"
                                   placeholder="Client name, email, ID..."
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Sheet</span>
                            <select name="sheet_id" id="sheet_id" class="form-select form-select-sm">
                                <option value="0">All Sheets</option>
                                <?php foreach ($availableSheets as $sheet): ?>
                                    <option value="<?php echo $sheet['id']; ?>" <?php echo ($selectedSheet == $sheet['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sheet['file_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Date</span>
                            <input type="date" name="start_date" id="start_date" class="form-control form-control-sm"
                                   value="<?php echo htmlspecialchars($startDate); ?>">
                            <span class="input-group-text">to</span>
                            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm"
                                   value="<?php echo htmlspecialchars($endDate); ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-1 d-flex align-items-center">
                        <div class="btn-group btn-group-sm">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="client_management.php" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- Active Filters Display -->
            <?php if (!empty($search) || $selectedSheet > 0 || (!empty($startDate) && !empty($endDate))): ?>
            <div class="active-filters mt-2">
                <small class="text-muted me-2">Active filters:</small>
                
                <?php if (!empty($search)): ?>
                <span class="filter-badge">
                    Search: <?php echo htmlspecialchars($search); ?>
                </span>
                <?php endif; ?>
                
                <?php if ($selectedSheet > 0):
                                    $sheetName = "";
                    foreach ($availableSheets as $sheet) {
                        if ($sheet['id'] == $selectedSheet) {
                            $sheetName = $sheet['file_name'];
                            break;
                        }
                    }
                ?>
                <span class="filter-badge">
                    Sheet: <?php echo htmlspecialchars($sheetName); ?>
                </span>
                <?php endif; ?>
                
                <?php if (!empty($startDate) && !empty($endDate)): ?>
                <span class="filter-badge">
                    Date: <?php echo htmlspecialchars($startDate); ?> to <?php echo htmlspecialchars($endDate); ?>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Results Summary -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0"><b>CLIENT RECORDS</b></h5>
                <small class="text-muted">Showing <?php echo min($totalClients, $itemsPerPage); ?> of <?php echo $totalClients; ?> records</small>
            </div>
            <!-- Add Client Button -->
            <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClientModal">
                    <i class="fas fa-plus"></i> Add Client
                </button>
            </div>
        </div>
        
        <!-- Clients Table -->
        <div class="client-table">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 15%">ID</th>
                        <th style="width: 25%">Client Name</th>
                        <th style="width: 15%">Staff Name</th>
                        <th style="width: 20%">Certificate</th>
                        <th style="width: 25%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($client = $result->fetch_assoc()):
                        // Check if client has a certificate based on your generation pattern
                        $hasCertificate = false;
                        $certificatePath = '';
                        
                        // Method 1: Check file_path field (this should work based on your generation code)
                        if (!empty($client['file_path'])) {
                            // Handle both absolute URLs and relative paths
                            if (preg_match('/^https?:\/\//', $client['file_path'])) {
                                // It's already an absolute URL
                                $certificatePath = $client['file_path'];
                                $hasCertificate = true;
                            } else {
                                // It's a relative path, check if file exists
                                if (file_exists($client['file_path'])) {
                                    // Convert to absolute URL
                                    $baseDir = dirname($_SERVER['SCRIPT_NAME']);
                                    $baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                                    if ($baseDir == '/') $baseDir = '';
                                    $certificatePath = $baseURL . $baseDir . '/' . ltrim($client['file_path'], '/');
                                    $hasCertificate = true;
                                }
                            }
                        }
                        
                        // Method 2: Check standard generation pattern if file_path is empty
                        if (!$hasCertificate && !empty($client['reference_id'])) {
                            $safe_client_name = preg_replace('/[^a-zA-Z0-9]/', '_', strtoupper($client['client_name']));
                            $possiblePaths = [
                                "uploads/certificate_{$client['reference_id']}_{$safe_client_name}.pdf",
                                "uploads/certificate_{$client['reference_id']}.pdf"
                            ];
                            
                            foreach ($possiblePaths as $path) {
                                if (file_exists($path)) {
                                    // Convert to absolute URL
                                    $baseDir = dirname($_SERVER['SCRIPT_NAME']);
                                    $baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                                    if ($baseDir == '/') $baseDir = '';
                                    $certificatePath = $baseURL . $baseDir . '/' . ltrim($path, '/');
                                    $hasCertificate = true;
                                    
                                    // Update the database with the found path
                                    $update_query = "UPDATE clients SET file_path = ? WHERE id = ?";
                                    $stmt = $conn->prepare($update_query);
                                    $stmt->bind_param("si", $certificatePath, $client['id']);
                                    $stmt->execute();
                                    break;
                                }
                            }
                        }
                    ?>
                        <tr>
                            <td>
                                <span class="reference-id" data-tooltip="<?php echo htmlspecialchars($client['reference_id']); ?>">
                                    <?php echo htmlspecialchars($client['reference_id']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="client-name" data-tooltip="<?php echo htmlspecialchars($client['client_name']); ?>">
                                    <?php echo htmlspecialchars($client['client_name']); ?>
                                </div>
                                <div class="client-email" data-tooltip="<?php echo htmlspecialchars($client['email']); ?>">
                                    <?php echo htmlspecialchars($client['email']); ?>
                                </div>
                            </td>
                            <td>
                                <span class="staff" data-tooltip="<?php echo htmlspecialchars($client['staff']); ?>">
                                    <?php echo htmlspecialchars($client['staff']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($hasCertificate): ?>
                                    <!-- Show View PDF link when certificate is found -->
                                    <a href="javascript:void(0)" 
                                    class="certificate-view-link" 
                                    onclick="viewCertificate('<?php echo htmlspecialchars($certificatePath); ?>')"
                                    title="View Certificate PDF">
                                        <i class="fas fa-file-pdf me-1"></i>
                                        View PDF
                                    </a>
                                <?php else: ?>
                                    <span class="no-certificate">No certificate issued</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-primary-custom btn-sm btn-action me-1"
                                            onclick="viewClient(<?php echo $client['id']; ?>)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-warning-custom btn-sm btn-action me-1"
                                            onclick="editClient(<?php echo $client['id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger-custom btn-sm btn-action"
                                            onclick="deleteClient(<?php echo $client['id']; ?>, '<?php echo htmlspecialchars($client['client_name']); ?>')">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-info-circle me-2"></i>
                                <?php
                                if (!empty($search) || $selectedSheet > 0 || (!empty($startDate) && !empty($endDate))) {
                                    echo 'No clients match your filter criteria.';
                                } else {
                                    echo 'No clients found in the database.';
                                }
                                ?>
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
                            <a class="page-link" href="?page=<?php echo $page-1; ?><?php
                                echo !empty($search) ? '&search='.urlencode($search) : '';
                                echo $selectedSheet > 0 ? '&sheet_id='.$selectedSheet : '';
                                echo !empty($startDate) ? '&start_date='.urlencode($startDate) : '';
                                echo !empty($endDate) ? '&end_date='.urlencode($endDate) : '';
                            ?>" tabindex="-1" <?php echo ($page <= 1) ? 'aria-disabled="true"' : ''; ?>>Previous</a>
                        </li>
                        
                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php
                                    echo !empty($search) ? '&search='.urlencode($search) : '';
                                    echo $selectedSheet > 0 ? '&sheet_id='.$selectedSheet : '';
                                    echo !empty($startDate) ? '&start_date='.urlencode($startDate) : '';
                                    echo !empty($endDate) ? '&end_date='.urlencode($endDate) : '';
                                ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?><?php
                                echo !empty($search) ? '&search='.urlencode($search) : '';
                                echo $selectedSheet > 0 ? '&sheet_id='.$selectedSheet : '';
                                echo !empty($startDate) ? '&start_date='.urlencode($startDate) : '';
                                echo !empty($endDate) ? '&end_date='.urlencode($endDate) : '';
                            ?>" <?php echo ($page >= $totalPages) ? 'aria-disabled="true"' : ''; ?>>Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Modals -->
        <?php include('client_modals.php'); ?>
    </div>
</div>

<!-- Include the Add Client Modal -->
<div id="clientModalContainer">
    <?php include('add_client_modal.php'); ?>
</div>

<!-- Certificate Modal -->
<div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="certificateModalLabel">Certificate Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="certificate-preview">
                    <div class="certificate-actions">
                        <a href="#" id="openCertificate" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fas fa-external-link-alt"></i> Open in New Tab
                        </a>
                        <a href="#" id="downloadCertificate" class="btn btn-sm btn-success" download>
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                    <div class="certificate-embed">
                        <iframe id="certificateFrame" src="" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- View Client Modal -->
<div class="modal fade" id="viewClientModal" tabindex="-1" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewClientModalLabel">Client Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewClientModalContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Client Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editClientModalLabel">Edit Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editClientModalContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to handle date range validation
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        // Ensure end date is not before start date
        endDateInput.addEventListener('change', function() {
            if (startDateInput.value && this.value && this.value < startDateInput.value) {
                alert('End date cannot be before start date');
                this.value = startDateInput.value;
            }
        });
        
        // Ensure start date is not after end date
        startDateInput.addEventListener('change', function() {
            if (endDateInput.value && this.value && this.value > endDateInput.value) {
                alert('Start date cannot be after end date');
                this.value = endDateInput.value;
            }
        });
        
        // Auto-submit form when sheet selection changes
        document.getElementById('sheet_id').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
        
        // Initialize tooltips
        initTooltips();
    });

    // Function to handle tooltip display
    function initTooltips() {
        // This is a simple tooltip implementation
        // For more advanced tooltips, you could use Bootstrap's tooltip component
        // or a library like tippy.js
        
        // The tooltip functionality is already implemented via CSS
        // The [data-tooltip] attribute is used to store and display the tooltip text
    }

    // Function to view certificate
    function viewCertificate(filePath) {
        // Set the iframe source
        document.getElementById('certificateFrame').src = filePath;
        
        // Set the download and open links
        document.getElementById('downloadCertificate').href = filePath;
        document.getElementById('openCertificate').href = filePath;
        
        // Show the modal
        var certificateModal = new bootstrap.Modal(document.getElementById('certificateModal'));
        certificateModal.show();
    }

    // Functions to handle client actions
    function viewClient(clientId) {
        // Load client details via AJAX and show in modal
        $.ajax({
            url: 'view_client.php',
            type: 'GET',
            data: { id: clientId },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    alert('Error: ' + response.error);
                    return;
                }
                
                // Build the client details HTML
                let html = buildClientDetailsHTML(response);
                $('#viewClientModalContent').html(html);
                
                var viewClientModal = new bootstrap.Modal(document.getElementById('viewClientModal'));
                viewClientModal.show();
            },
            error: function() {
                alert('Error loading client details.');
            }
        });
    }

    function buildClientDetailsHTML(response) {
        const client = response.client;
        const feedback = response.feedback;
        const file = response.file;
        
        let html = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Basic Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Reference ID:</strong></td><td>${client.reference_id || 'N/A'}</td></tr>
                                                <tr><td><strong>Name:</strong></td><td>${client.client_name || 'N/A'}</td></tr>
                        <tr><td><strong>Email:</strong></td><td>${client.email || 'N/A'}</td></tr>
                        <tr><td><strong>Type:</strong></td><td>${client.client_type || 'N/A'}</td></tr>
                        <tr><td><strong>Sex:</strong></td><td>${client.sex || 'N/A'}</td></tr>
                        <tr><td><strong>Age:</strong></td><td>${client.age || 'N/A'}</td></tr>
                        <tr><td><strong>Region:</strong></td><td>${client.region || 'N/A'}</td></tr>
                        <tr><td><strong>Contact:</strong></td><td>${client.contact || 'N/A'}</td></tr>
                        <tr><td><strong>Registration Date:</strong></td><td>${client.timestamp || 'N/A'}</td></tr>
                        <tr><td><strong>Completion Date:</strong></td><td>${client.completion_date || 'Not completed'}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Certificate Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Certificate Type:</strong></td><td>${file.cert_type || 'N/A'}</td></tr>
                        <tr><td><strong>File ID:</strong></td><td>${file.file_id || 'N/A'}</td></tr>
                        <tr><td><strong>Status:</strong></td><td>${file.file_path ? '<span class="badge bg-success">Certificate Issued</span>' : '<span class="badge bg-warning">No Certificate</span>'}</td></tr>
                    </table>
                    
                    ${file.file_path ? `
                        <div class="mt-3">
                            <h6 class="text-primary">Certificate Preview</h6>
                            <div class="certificate-actions mb-2">
                                <a href="${file.file_path}" class="btn btn-sm btn-primary" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Open in New Tab
                                </a>
                                <a href="${file.file_path}" class="btn btn-sm btn-success" download>
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                            <div class="certificate-embed" style="height: 300px;">
                                <iframe src="${file.file_path}" style="width: 100%; height: 100%; border: none;"></iframe>
                            </div>
                        </div>
                    ` : ''}
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="text-primary">Feedback Details</h6>
                    <div class="accordion" id="feedbackAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingObjectives">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseObjectives">
                                    Service Rating - Objectives
                                </button>
                            </h2>
                            <div id="collapseObjectives" class="accordion-collapse collapse" data-bs-parent="#feedbackAccordion">
                                <div class="accordion-body">
                                    <table class="table table-sm">
                                        <tr><td><strong>Objectives Achieved:</strong></td><td>${feedback.service_rating_objectives.objectives_achieved || 'N/A'}</td></tr>
                                        <tr><td><strong>Info Received:</strong></td><td>${feedback.service_rating_objectives.info_received || 'N/A'}</td></tr>
                                        <tr><td><strong>Relevance Value:</strong></td><td>${feedback.service_rating_objectives.relevance_value || 'N/A'}</td></tr>
                                        <tr><td><strong>Duration Sufficient:</strong></td><td>${feedback.service_rating_objectives.duration_sufficient || 'N/A'}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingAccess">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAccess">
                                    Service Access & Functionality
                                </button>
                            </h2>
                            <div id="collapseAccess" class="accordion-collapse collapse" data-bs-parent="#feedbackAccordion">
                                <div class="accordion-body">
                                    <table class="table table-sm">
                                        <tr><td><strong>Sign Up Access:</strong></td><td>${feedback.service_access_functionality.sign_up_access || 'N/A'}</td></tr>
                                        <tr><td><strong>Audio Video Sync:</strong></td><td>${feedback.service_access_functionality.audio_video_sync || 'N/A'}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSpeaker">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSpeaker">
                                    Resource Speaker
                                </button>
                            </h2>
                            <div id="collapseSpeaker" class="accordion-collapse collapse" data-bs-parent="#feedbackAccordion">
                                <div class="accordion-body">
                                    <h6>Quality</h6>
                                    <table class="table table-sm">
                                        <tr><td><strong>Knowledge:</strong></td><td>${feedback.resource_speaker.quality.knowledge || 'N/A'}</td></tr>
                                        <tr><td><strong>Clarity:</strong></td><td>${feedback.resource_speaker.quality.clarity || 'N/A'}</td></tr>
                                        <tr><td><strong>Engagement:</strong></td><td>${feedback.resource_speaker.quality.engagement || 'N/A'}</td></tr>
                                        <tr><td><strong>Visual Relevance:</strong></td><td>${feedback.resource_speaker.quality.visual_relevance || 'N/A'}</td></tr>
                                    </table>
                                    <h6>Interaction</h6>
                                    <table class="table table-sm">
                                        <tr><td><strong>Answer Questions:</strong></td><td>${feedback.resource_speaker.interaction.answer_questions || 'N/A'}</td></tr>
                                        <tr><td><strong>Chat Responsiveness:</strong></td><td>${feedback.resource_speaker.interaction.chat_responsiveness || 'N/A'}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingModerator">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseModerator">
                                    Moderator
                                </button>
                            </h2>
                            <div id="collapseModerator" class="accordion-collapse collapse" data-bs-parent="#feedbackAccordion">
                                <div class="accordion-body">
                                    <table class="table table-sm">
                                        <tr><td><strong>Manage Discussion:</strong></td><td>${feedback.moderator.manage_discussion || 'N/A'}</td></tr>
                                        <tr><td><strong>Monitor Raises Questions:</strong></td><td>${feedback.moderator.monitor_raises_questions || 'N/A'}</td></tr>
                                        <tr><td><strong>Manage Program:</strong></td><td>${feedback.moderator.manage_program || 'N/A'}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingHost">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHost">
                                    Host/Secretariat
                                </button>
                            </h2>
                            <div id="collapseHost" class="accordion-collapse collapse" data-bs-parent="#feedbackAccordion">
                                <div class="accordion-body">
                                    <table class="table table-sm">
                                        <tr><td><strong>Technical Assistance:</strong></td><td>${feedback.host_secretariat.technical_assistance || 'N/A'}</td></tr>
                                        <tr><td><strong>Admittance Management:</strong></td><td>${feedback.host_secretariat.admittance_management || 'N/A'}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOverall">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOverall">
                                    Overall Feedback
                                </button>
                            </h2>
                            <div id="collapseOverall" class="accordion-collapse collapse" data-bs-parent="#feedbackAccordion">
                                <div class="accordion-body">
                                    <table class="table table-sm">
                                        <tr><td><strong>Satisfaction Rating:</strong></td><td>${feedback.overall.satisfaction_rating || 'N/A'}</td></tr>
                                        <tr><td><strong>Dissatisfied Reasons:</strong></td><td>${feedback.overall.dissatisfied_reasons || 'N/A'}</td></tr>
                                        <tr><td><strong>Improvement Suggestions:</strong></td><td>${feedback.overall.improvement_suggestions || 'N/A'}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        return html;
    }

    function editClient(clientId) {
        // Load client edit form via AJAX and show in modal
        $.ajax({
            url: 'edit_client_form.php',
            type: 'GET',
            data: { id: clientId },
            success: function(response) {
                $('#editClientModalContent').html(response);
                var editClientModal = new bootstrap.Modal(document.getElementById('editClientModal'));
                editClientModal.show();
            },
            error: function() {
                alert('Error loading client edit form.');
            }
        });
    }

    function deleteClient(clientId, clientName) {
        if (confirm('Are you sure you want to delete client: ' + clientName + '?')) {
            window.location.href = 'delete_client.php?id=' + clientId;
        }
    }
</script>

<script src="client_management.js"></script>
<script src="darkmode.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Include the footer
include('footer.php');
?>
</body>
</html>


