<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sheet Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="img/logowhite.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    body {
        background-color: #f5f5f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
   
    /* Card Styles */
    .sheet-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        background-color: #ffffff;
        border: none;
    }
   
    .sheet-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
   
    .sheet-card .card-body {
        padding: 1.5rem;
    }
   
    .sheet-card .card-title {
        color: #0d1b57;
        font-weight: 600;
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }
   
    .sheet-card .card-text {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
   
    .sheet-card .card-footer {
        background-color: rgba(0,0,0,0.02);
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 1.5rem;
    }
   
    .sheet-card .sheet-date {
        font-size: 0.85rem;
        color: #6c757d;
    }
   
    .sheet-card .sheet-type {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 30px;
        margin-bottom: 1rem;
    }
   
    .sheet-type-business {
        background-color: #e8f5e9;
        color: #1b5e20;
    }
   
    .sheet-type-tax {
        background-color: #e3f2fd;
        color: #0d47a1;
    }
   
    .sheet-type-dti {
        background-color: #fff3e0;
        color: #e65100;
    }
   
    .sheet-type-export {
        background-color: #f3e5f5;
        color: #7b1fa2;
    }
   
    .sheet-type-import {
        background-color: #e0f7fa;
        color: #006064;
    }
   
    .sheet-type-other {
        background-color: #f5f5f5;
        color: #424242;
    }
   
    /* File icon */
    .file-icon {
        font-size: 24px;
        margin-right: 10px;
        color: #0d1b57;
    }
   
    /* Add Sheet Button */
    .add-sheet-btn {
        background-color: #0d1b57;
        color: white;
        border-radius: 50px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 10px rgba(13, 27, 87, 0.3);
    }
   
    .add-sheet-btn:hover {
        background-color: #162a78;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(13, 27, 87, 0.4);
        color: white;
    }
   
    .add-sheet-btn i {
        margin-right: 8px;
    }
   
    /* Action Buttons */
    .btn-primary-custom {
        background-color: #0d1b57 !important;
        border-color: #0d1b57 !important;
        color: white !important;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
   
    .btn-primary-custom:hover {
        background-color: #162a78; /* Slightly lighter blue on hover */
        border-color: #162a78;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 27, 87, 0.3);
        color: white;
    }
   
    /* Edit button - Yellow */
    .btn-warning-custom {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
        color: #212529 !important; /* Dark text for contrast */
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
   
    .btn-warning-custom:hover {
        background-color: #e0a800; /* Darker yellow on hover */
        border-color: #d39e00;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
        color: #212529;
    }
   
    /* Delete button - Red */
    .btn-danger-custom {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
   
    .btn-danger-custom:hover {
        background-color: #c82333; /* Darker red on hover */
        border-color: #bd2130;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        color: white;
    }
   
    .btn-action {
        padding: 6px 10px;
        font-size: 14px;
    }
   
    /* Search Container Styles */
    .search-container {
        background-color: white;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }
   
    .search-input {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
   
    .search-input:focus {
        border-color: #0d1b57;
        box-shadow: 0 0 0 0.25rem rgba(13, 27, 87, 0.25);
    }
   
    /* Modal Styles */
    .modal-header {
        background: linear-gradient(135deg, #0d1b57 0%, #1a3a8f 100%);
        color: white;
        border-bottom: none;
        border-radius: 10px 10px 0 0;
        padding: 20px 25px;
    }
   
    .modal-title {
        font-weight: 600;
        font-size: 22px;
    }
   
    .modal-header .btn-close {
        color: white;
        filter: invert(1) grayscale(100%) brightness(200%);
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }
   
    .modal-header .btn-close:hover {
        opacity: 1;
    }
   
    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }
   
    .modal-body {
        padding: 25px;
        background-color: #f8f9fa;
    }
   
    .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        border-radius: 0 0 12px 12px;
        padding: 15px 25px;
    }
   
    /* Sheet Details Styles */
    .sheet-details-container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 25px;
        height: 100%;
    }
   
    .sheet-details-header {
        border-bottom: 2px solid #0d1b57;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
   
    .sheet-details-title {
        color: #0d1b57;
        font-weight: 600;
        font-size: 20px;
        margin-bottom: 5px;
    }
   
    .sheet-details-subtitle {
        color: #6c757d;
        font-size: 14px;
    }
   
    .sheet-detail-row {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 15px;
    }
   
    .sheet-detail-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
   
    .sheet-detail-label {
        flex: 0 0 40%;
        color: #495057;
        font-weight: 500;
    }
   
    .sheet-detail-value {
        flex: 0 0 60%;
        color: #212529;
    }
   
    /* Form Styles */
    .form-group {
        margin-bottom: 20px;
    }
   
    .form-label {
        font-weight: 500;
        color: #0d1b57;
        margin-bottom: 8px;
    }
   
    .form-control {
        border-radius: 6px;
        padding: 10px 15px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }
   
    .form-control:focus {
        border-color: #0d1b57;
        box-shadow: 0 0 0 0.25rem rgba(13, 27, 87, 0.25);
    }
   
    .form-text {
        color: #6c757d;
        font-size: 12px;
        margin-top: 5px;
    }
   
    /* Responsive adjustments */
    @media (max-width: 767px) {
        .sheet-detail-row {
            flex-direction: column;
        }
       
        .sheet-detail-label,
        .sheet-detail-value {
            flex: 0 0 100%;
        }
       
        .sheet-detail-label {
            margin-bottom: 5px;
        }
    }
    
    /* Action buttons container */
    .action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
    </style>
</head>
<body>

<?php
// Set page-specific variables
$pageTitle = "DTI Sheet Management";
$currentPage = "Sheets";

// Include additional CSS if needed
$additionalCSS = '
    <!-- Any additional CSS specific to this page -->
';

// Include the header
include('header.php');

// Include the sidebar
include('sidebar.php');
?>

<div class="main-content" style="margin-top: 120px;">
    <div class="container">
        <!-- Search and Add Sheet Section -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6 text-md-start">
                <a href="#" class="btn add-sheet-btn" data-bs-toggle="modal" data-bs-target="#addSheetModal">
                    <i class="fas fa-plus"></i> Upload New Sheet
                </a>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control search-input border-start-0" id="searchSheet" placeholder="Search Sheet's Name">
                </div>
            </div>
        </div>
       
        <!-- Sheets Cards -->
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
    <!-- Sheet Card 1 -->
    <div class="col sheet-item">
        <div class="card sheet-card">
            <div class="card-body">
                <h5 class="card-title">Business Registration Data</h5>
                <p class="card-text">Contains business registration information for Q1 2023</p>
                <div class="sheet-date mb-3">
                    <i class="far fa-calendar-alt me-1"></i> Uploaded on May 15, 2023
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewSheetModal"
                            data-sheet-id="1"
                            data-sheet-name="Business Registration Data"
                            data-sheet-date="May 15, 2023"
                            data-sheet-cert="Business Permit"
                            data-sheet-desc="Contains business registration information for Q1 2023">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-warning-custom btn-sm" data-bs-toggle="modal" data-bs-target="#editSheetModal"
                            data-sheet-id="1"
                            data-sheet-name="Business Registration Data"
                            data-sheet-date="May 15, 2023"
                            data-sheet-cert="Business Permit"
                            data-sheet-desc="Contains business registration information for Q1 2023">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger-custom btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSheetModal"
                            data-sheet-id="1"
                            data-sheet-name="Business Registration Data">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sheet Card 2 -->
    <div class="col sheet-item">
        <div class="card sheet-card">
            <div class="card-body">
                <h5 class="card-title">Tax Clearance Applications</h5>
                <p class="card-text">List of businesses applying for tax clearance certificates</p>
                <div class="sheet-date mb-3">
                    <i class="far fa-calendar-alt me-1"></i> Uploaded on June 20, 2023
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewSheetModal"
                            data-sheet-id="2"
                            data-sheet-name="Tax Clearance Applications"
                            data-sheet-date="June 20, 2023"
                            data-sheet-cert="Tax Clearance"
                            data-sheet-desc="List of businesses applying for tax clearance certificates">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-warning-custom btn-sm" data-bs-toggle="modal" data-bs-target="#editSheetModal"
                            data-sheet-id="2"
                            data-sheet-name="Tax Clearance Applications"
                            data-sheet-date="June 20, 2023"
                            data-sheet-cert="Tax Clearance"
                            data-sheet-desc="List of businesses applying for tax clearance certificates">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger-custom btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSheetModal"
                            data-sheet-id="2"
                            data-sheet-name="Tax Clearance Applications">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sheet Card 3 -->
    <div class="col sheet-item">
        <div class="card sheet-card">
            <div class="card-body">
                <h5 class="card-title">DTI Registration Batch</h5>
                <p class="card-text">Batch processing data for DTI registrations in Metro Manila</p>
                <div class="sheet-date mb-3">
                    <i class="far fa-calendar-alt me-1"></i> Uploaded on July 10, 2023
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewSheetModal"
                            data-sheet-id="3"
                            data-sheet-name="DTI Registration Batch"
                            data-sheet-date="July 10, 2023"
                            data-sheet-cert="DTI Registration"
                            data-sheet-desc="Batch processing data for DTI registrations in Metro Manila">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-warning-custom btn-sm" data-bs-toggle="modal" data-bs-target="#editSheetModal"
                            data-sheet-id="3"
                            data-sheet-name="DTI Registration Batch"
                            data-sheet-date="July 10, 2023"
                            data-sheet-cert="DTI Registration"
                            data-sheet-desc="Batch processing data for DTI registrations in Metro Manila">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger-custom btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSheetModal"
                            data-sheet-id="3"
                            data-sheet-name="DTI Registration Batch">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sheet Card 4 -->
    <div class="col sheet-item">
        <div class="card sheet-card">
            <div class="card-body">
                <h5 class="card-title">Small Business Permits</h5>
                <p class="card-text">Small business permit applications for Quezon City</p>
                <div class="sheet-date mb-3">
                    <i class="far fa-calendar-alt me-1"></i> Uploaded on August 5, 2023
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewSheetModal"
                            data-sheet-id="4"
                            data-sheet-name="Small Business Permits"
                            data-sheet-date="August 5, 2023"
                            data-sheet-cert="Business Permit"
                            data-sheet-desc="Small business permit applications for Quezon City">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-warning-custom btn-sm" data-bs-toggle="modal" data-bs-target="#editSheetModal"
                            data-sheet-id="4"
                            data-sheet-name="Small Business Permits"
                            data-sheet-date="August 5, 2023"
                            data-sheet-cert="Business Permit"
                            data-sheet-desc="Small business permit applications for Quezon City">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger-custom btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSheetModal"
                            data-sheet-id="4"
                            data-sheet-name="Small Business Permits">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sheet Card 5 -->
    <div class="col sheet-item">
        <div class="card sheet-card">
            <div class="card-body">
                <h5 class="card-title">Export License Data</h5>
                <p class="card-text">Export license applications for agricultural products</p>
                <div class="sheet-date mb-3">
                    <i class="far fa-calendar-alt me-1"></i> Uploaded on September 12, 2023
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewSheetModal"
                            data-sheet-id="5"
                            data-sheet-name="Export License Data"
                            data-sheet-date="September 12, 2023"
                            data-sheet-cert="Export License"
                            data-sheet-desc="Export license applications for agricultural products">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-warning-custom btn-sm" data-bs-toggle="modal" data-bs-target="#editSheetModal"
                            data-sheet-id="5"
                            data-sheet-name="Export License Data"
                            data-sheet-date="September 12, 2023"
                            data-sheet-cert="Export License"
                            data-sheet-desc="Export license applications for agricultural products">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger-custom btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSheetModal"
                            data-sheet-id="5"
                            data-sheet-name="Export License Data">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sheet Card 6 -->
    <div class="col sheet-item">
        <div class="card sheet-card">
            <div class="card-body">
                <h5 class="card-title">Import Commodity Data</h5>
                <p class="card-text">Import license applications for essential commodities</p>
                <div class="sheet-date mb-3">
                    <i class="far fa-calendar-alt me-1"></i> Uploaded on October 8, 2023
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewSheetModal"
                            data-sheet-id="6"
                            data-sheet-name="Import Commodity Data"
                            data-sheet-date="October 8, 2023"
                            data-sheet-cert="Import License"
                            data-sheet-desc="Import license applications for essential commodities">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-warning-custom btn-sm" data-bs-toggle="modal" data-bs-target="#editSheetModal"
                            data-sheet-id="6"
                            data-sheet-name="Import Commodity Data"
                            data-sheet-date="October 8, 2023"
                            data-sheet-cert="Import License"
                            data-sheet-desc="Import license applications for essential commodities">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger-custom btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSheetModal"
                            data-sheet-id="6"
                            data-sheet-name="Import Commodity Data">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Sheet pagination">
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
    </div>
</div>

<!-- View Sheet Modal -->
<div class="modal fade" id="viewSheetModal" tabindex="-1" aria-labelledby="viewSheetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSheetModalLabel">Sheet Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="sheet-details-container">
                    <div class="sheet-details-header">
                        <h4 class="sheet-details-title" id="viewSheetName">Business Registration Data</h4>
                        <p class="sheet-details-subtitle" id="viewSheetDate">Uploaded on May 15, 2023</p>
                    </div>
                   
                    <div class="sheet-detail-row">
                        <div class="sheet-detail-label">Certificate Type:</div>
                        <div class="sheet-detail-value" id="viewSheetCertType">Business Permit</div>
                    </div>
                   
                    <div class="sheet-detail-row">
                        <div class="sheet-detail-label">Description:</div>
                        <div class="sheet-detail-value" id="viewSheetDesc">
                        Contains business registration information for Q1 2023
                        </div>
                    </div>
                   
                    <div class="sheet-detail-row">
                        <div class="sheet-detail-label">File Format:</div>
                        <div class="sheet-detail-value">Microsoft Excel (.xlsx)</div>
                    </div>
                   
                    <div class="sheet-detail-row">
                        <div class="sheet-detail-label">Last Modified:</div>
                        <div class="sheet-detail-value">May 18, 2023</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success">
                    <i class="fas fa-download me-2"></i> Download Excel
                </button>
                <button type="button" class="btn btn-warning-custom" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editSheetModal">
                    <i class="fas fa-edit me-2"></i> Edit Sheet
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Sheet Modal -->
<div class="modal fade" id="addSheetModal" tabindex="-1" aria-labelledby="addSheetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSheetModalLabel">Upload New Sheet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSheetForm">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Sheet Information</h5>
                           
                            <div class="form-group mb-3">
                                <label for="sheetName" class="form-label">Sheet Name</label>
                                <input type="text" class="form-control" id="sheetName" required>
                            </div>
                           
                            <div class="form-group mb-3">
                                <label for="sheetCertType" class="form-label">Certificate Type</label>
                                <select class="form-select" id="sheetCertType" required>
                                    <option value="" selected disabled>Select certificate type</option>
                                    <option value="business">Business Permit</option>
                                    <option value="tax">Tax Clearance</option>
                                    <option value="dti">DTI Registration</option>
                                    <option value="export">Export License</option>
                                    <option value="import">Import License</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                           
                            <div class="form-group mb-3">
                                <label for="sheetDesc" class="form-label">Description</label>
                                <textarea class="form-control" id="sheetDesc" rows="3"></textarea>
                            </div>
                        </div>
                       
                        <!-- File Upload -->
                        <div class="col-md-6">
                            <h5 class="mb-3">File Upload</h5>
                           
                            <div class="form-group mb-3">
                                <label for="sheetFile" class="form-label">Excel File</label>
                                <input type="file" class="form-control" id="sheetFile" accept=".xlsx, .xls" required>
                                <div class="form-text">Only Excel files (.xlsx, .xls) are supported</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addSheetForm" class="btn btn-primary-custom">
                    <i class="fas fa-upload me-2"></i> Upload Sheet
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Sheet Modal -->
<div class="modal fade" id="viewSheetModal" tabindex="-1" aria-labelledby="viewSheetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSheetModalLabel">Sheet Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="sheet-details-container">
                    <div class="sheet-details-header">
                        <h4 class="sheet-details-title" id="viewSheetName">Business Registration Data</h4>
                        <p class="sheet-details-subtitle" id="viewSheetDate">Uploaded on May 15, 2023</p>
                    </div>
                   
                    <div class="sheet-detail-row">
                        <div class="sheet-detail-label">Certificate Type:</div>
                        <div class="sheet-detail-value" id="viewSheetCertType">Business Permit</div>
                    </div>
                   
                    <div class="sheet-detail-row">
                        <div class="sheet-detail-label">Description:</div>
                        <div class="sheet-detail-value" id="viewSheetDesc">
                        Contains business registration information for Q1 2023
                        </div>
                    </div>
                   
                    <div class="sheet-detail-row">
                        <div class="sheet-detail-label">File Format:</div>
                        <div class="sheet-detail-value">Microsoft Excel (.xlsx)</div>
                    </div>
                   
                    <div class="sheet-detail-row">
                        <div class="sheet-detail-label">Last Modified:</div>
                        <div class="sheet-detail-value">May 18, 2023</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success">
                    <i class="fas fa-download me-2"></i> Download Excel
                </button>
                <button type="button" class="btn btn-warning-custom" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editSheetModal">
                    <i class="fas fa-edit me-2"></i> Edit Sheet
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Sheet Modal -->
<div class="modal fade" id="editSheetModal" tabindex="-1" aria-labelledby="editSheetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSheetModalLabel">Edit Sheet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editSheetForm">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Sheet Information</h5>
                           
                            <div class="form-group mb-3">
                                <label for="editSheetName" class="form-label">Sheet Name</label>
                                <input type="text" class="form-control" id="editSheetName" value="Business Registration Data" required>
                            </div>
                           
                            <div class="form-group mb-3">
                                <label for="editSheetCertType" class="form-label">Certificate Type</label>
                                <select class="form-select" id="editSheetCertType" required>
                                    <option value="business" selected>Business Permit</option>
                                    <option value="tax">Tax Clearance</option>
                                    <option value="dti">DTI Registration</option>
                                    <option value="export">Export License</option>
                                    <option value="import">Import License</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                           
                            <div class="form-group mb-3">
                                <label for="editSheetDesc" class="form-label">Description</label>
                                <textarea class="form-control" id="editSheetDesc" rows="3">Contains business registration information for Q1 2023</textarea>
                            </div>
                        </div>
                       
                        <!-- File Upload -->
                        <div class="col-md-6">
                            <h5 class="mb-3">File Management</h5>
                           
                            <div class="form-group mb-3">
                                <label for="editSheetFile" class="form-label">Replace Excel File (Optional)</label>
                                <input type="file" class="form-control" id="editSheetFile" accept=".xlsx, .xls">
                                <div class="form-text">Leave empty to keep the existing file</div>
                            </div>
                           
                            <div class="form-group mb-3">
                                <label class="form-label">Current File Information</label>
                                <div class="card bg-light p-3">
                                    <p class="mb-1"><strong>Filename:</strong> business_reg_q1_2023.xlsx</p>
                                    <p class="mb-1"><strong>Size:</strong> 1.2 MB</p>
                                    <p class="mb-0"><strong>Last Modified:</strong> May 18, 2023</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editSheetForm" class="btn btn-primary-custom">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Delete Sheet Modal -->
<div class="modal fade" id="deleteSheetModal" tabindex="-1" aria-labelledby="deleteSheetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteSheetModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the sheet "<span id="deleteSheetName">Business Registration Data</span>"?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> This action cannot be undone. All data in this sheet will be permanently removed.</p>
                <input type="hidden" id="deleteSheetId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt me-2"></i> Delete Sheet
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    
    // View Sheet Modal Data
$('#viewSheetModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
    var sheetId = button.data('sheet-id')
    var sheetName = button.data('sheet-name')
    var sheetDate = button.data('sheet-date')
    var sheetCert = button.data('sheet-cert')
    var sheetDesc = button.data('sheet-desc')
    
    var modal = $(this)
    modal.find('#viewSheetName').text(sheetName)
    modal.find('#viewSheetDate').text('Uploaded on ' + sheetDate)
    modal.find('#viewSheetCertType').text(sheetCert)
    modal.find('#viewSheetDesc').text(sheetDesc)
})

    // Edit Sheet Modal Data
    $('#editSheetModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var sheetId = button.data('sheet-id')
        var sheetName = button.data('sheet-name')
        var sheetDate = button.data('sheet-date')
        var sheetCert = button.data('sheet-cert')
        var sheetDesc = button.data('sheet-desc')
        
        var modal = $(this)
        modal.find('#editSheetName').val(sheetName)
        
        // Set the correct certificate type in the dropdown
        var certTypeSelect = modal.find('#editSheetCertType')
        certTypeSelect.find('option').each(function() {
            if ($(this).text() === sheetCert) {
                $(this).prop('selected', true)
            }
        })
        
        modal.find('#editSheetDesc').val(sheetDesc)
    })
    
    // Delete Sheet Modal Data
    $('#deleteSheetModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var sheetId = button.data('sheet-id')
        var sheetName = button.data('sheet-name')
        
        var modal = $(this)
        modal.find('#deleteSheetName').text(sheetName)
        modal.find('#deleteSheetId').val(sheetId)
    })
    
    // Filter sheets by certificate type
    $('.filter-btn').on('click', function() {
        var filterValue = $(this).data('filter')
        
        if (filterValue === 'all') {
            $('.sheet-item').show()
        } else {
            $('.sheet-item').hide()
            $('.sheet-type-' + filterValue).closest('.sheet-item').show()
        }
        
        // Update active filter button
        $('.filter-btn').removeClass('active')
        $(this).addClass('active')
    })
    
    // Search functionality
    $('#searchSheet').on('keyup', function() {
        var searchValue = $(this).val().toLowerCase()
        
        $('.sheet-item').each(function() {
            var sheetName = $(this).find('.card-title').text().toLowerCase()
            var sheetDesc = $(this).find('.card-text').text().toLowerCase()
            var sheetType = $(this).find('.sheet-type').text().toLowerCase()
            
            if (sheetName.includes(searchValue) || 
                sheetDesc.includes(searchValue) || 
                sheetType.includes(searchValue)) {
                $(this).show()
            } else {
                $(this).hide()
            }
        })
    })
    
    // Form submission handlers
    $('#addSheetForm').on('submit', function(e) {
        e.preventDefault()
        // Here you would typically handle the form submission via AJAX
        // For demo purposes, we'll just close the modal
        $('#addSheetModal').modal('hide')
        
        // Show success message
        showAlert('success', 'Sheet uploaded successfully!')
    })
    
    $('#editSheetForm').on('submit', function(e) {
        e.preventDefault()
        // Here you would typically handle the form submission via AJAX
        // For demo purposes, we'll just close the modal
        $('#editSheetModal').modal('hide')
        
        // Show success message
        showAlert('success', 'Sheet updated successfully!')
    })
    
    $('#confirmDeleteBtn').on('click', function() {
        // Here you would typically handle the deletion via AJAX
        // For demo purposes, we'll just close the modal
        $('#deleteSheetModal').modal('hide')
        
        // Show success message
        showAlert('success', 'Sheet deleted successfully!')
    })
    
    // Function to show alerts
    function showAlert(type, message) {
        var alertClass = 'alert-info'
        var icon = '<i class="fas fa-info-circle me-2"></i>'
        
        if (type === 'success') {
            alertClass = 'alert-success'
            icon = '<i class="fas fa-check-circle me-2"></i>'
        } else if (type === 'warning') {
            alertClass = 'alert-warning'
            icon = '<i class="fas fa-exclamation-triangle me-2"></i>'
        } else if (type === 'danger') {
            alertClass = 'alert-danger'
            icon = '<i class="fas fa-times-circle me-2"></i>'
        }
        
        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                        icon + message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>'
        
        // Insert the alert at the top of the main content
        $('.main-content .container').prepend(alertHtml)
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close')
        }, 5000)
    }
</script>

<?php
// Include the footer
include('footer.php');
?>
</body>
</html>



