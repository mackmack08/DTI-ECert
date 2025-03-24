<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificate Management</title>
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
    .certificate-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        background-color: #ffffff;
        border: none;
    }
   
    .certificate-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
   
    .certificate-card .card-body {
        padding: 1.5rem;
    }
   
    .certificate-card .card-title {
        color: #0d1b57;
        font-weight: 600;
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }
   
    .certificate-card .card-text {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
   
    .certificate-card .certificate-date {
        font-size: 0.85rem;
        color: #6c757d;
    }
   
    .certificate-card .certificate-type {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 30px;
        margin-bottom: 1rem;
    }
   
    .certificate-type-business {
        background-color: #e8f5e9;
        color: #1b5e20;
    }
   
    .certificate-type-tax {
        background-color: #e3f2fd;
        color: #0d47a1;
    }
   
    .certificate-type-dti {
        background-color: #fff3e0;
        color: #e65100;
    }
   
    .certificate-type-export {
        background-color: #f3e5f5;
        color: #7b1fa2;
    }
   
    .certificate-type-import {
        background-color: #e0f7fa;
        color: #006064;
    }
   
    .certificate-type-other {
        background-color: #f5f5f5;
        color: #424242;
    }
   
    /* File icon */
    .file-icon {
        font-size: 24px;
        margin-right: 10px;
        color: #0d1b57;
    }
   
    /* Add Certificate Button */
    .add-certificate-btn {
        background-color: #0d1b57;
        color: white;
        border-radius: 50px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 10px rgba(13, 27, 87, 0.3);
    }
   
    .add-certificate-btn:hover {
        background-color: #162a78;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(13, 27, 87, 0.4);
        color: white;
    }
   
    .add-certificate-btn i {
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
   
    /* Certificate Details Styles */
    .certificate-details-container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 25px;
        height: 100%;
    }
   
    .certificate-details-header {
        border-bottom: 2px solid #0d1b57;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
   
    .certificate-details-title {
        color: #0d1b57;
        font-weight: 600;
        font-size: 20px;
        margin-bottom: 5px;
    }
   
    .certificate-details-subtitle {
        color: #6c757d;
        font-size: 14px;
    }
   
    .certificate-detail-row {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 15px;
    }
   
    .certificate-detail-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
   
    .certificate-detail-label {
        flex: 0 0 40%;
        color: #495057;
        font-weight: 500;
    }
   
    .certificate-detail-value {
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
   
    /* Add Certificate Card */
    .add-certificate-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        background-color: #f8f9fa;
        border: 2px dashed #0d1b57;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        cursor: pointer;
    }
   
    .add-certificate-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        background-color: #e9ecef;
    }
   
    .add-icon {
        width: 60px;
        height: 60px;
        background-color: #0d1b57;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
        transition: transform 0.3s ease;
    }
   
    .add-certificate-card:hover .add-icon {
        transform: scale(1.1);
    }
   
    .add-text {
        color: #0d1b57;
        font-weight: 600;
        text-align: center;
    }
   
    /* Certificate image */
    .certificate-image {
        width: 100%;
        height: auto;
        max-height: 150px;
        object-fit: cover;
        border-radius: 8px 8px 0 0;
    }
   
    /* Action buttons container */
    .action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
   
    /* Responsive adjustments */
    @media (max-width: 767px) {
        .certificate-detail-row {
            flex-direction: column;
        }
       
        .certificate-detail-label,
        .certificate-detail-value {
            flex: 0 0 100%;
        }
       
        .certificate-detail-label {
            margin-bottom: 5px;
        }
    }
    </style>
</head>
<body>

<?php
// Set page-specific variables
$pageTitle = "DTI Certificate Management";
$currentPage = "Certificate Management";

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
        <!-- Search and Add Certificate Section -->
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
    <!-- Certificate Card 1 -->
    <div class="col certificate-item">
        <div class="card certificate-card">
            <div class="card-body">
                <h5 class="card-title">Business Permit</h5>
                <p class="card-text">Official document that authorizes a business to operate within a specific jurisdiction.</p>
                <div class="certificate-date mb-3">
                    <i class="far fa-calendar-alt me-1"></i> Uploaded on May 15, 2023
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewCertificateModal"
                            data-cert-id="1"
                            data-cert-name="Business Permit"
                            data-cert-date="May 15, 2023"
                            data-cert-type="Business Document"
                            data-cert-desc="Official document that authorizes a business to operate within a specific jurisdiction.">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-warning-custom btn-sm" data-bs-toggle="modal" data-bs-target="#editCertificateModal"
                            data-cert-id="1"
                            data-cert-name="Business Permit"
                            data-cert-date="May 15, 2023"
                            data-cert-type="Business Document"
                            data-cert-desc="Official document that authorizes a business to operate within a specific jurisdiction.">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger-custom btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCertificateModal"
                            data-cert-id="1"
                            data-cert-name="Business Permit">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Certificate Card 2 -->
    <div class="col certificate-item">
        <div class="card certificate-card">
            <div class="card-body">
                <h5 class="card-title">Tax Clearance</h5>
                <p class="card-text">Document certifying that a business has paid all required taxes and has no outstanding tax liabilities.</p>
                <div class="certificate-date mb-3">
                    <i class="far fa-calendar-alt me-1"></i> Uploaded on June 20, 2023
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewCertificateModal"
                            data-cert-id="2"
                            data-cert-name="Tax Clearance"
                            data-cert-date="June 20, 2023"
                            data-cert-type="Tax Document"
                            data-cert-desc="Document certifying that a business has paid all required taxes and has no outstanding tax liabilities.">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-warning-custom btn-sm" data-bs-toggle="modal" data-bs-target="#editCertificateModal"
                            data-cert-id="2"
                            data-cert-name="Tax Clearance"
                            data-cert-date="June 20, 2023"
                            data-cert-type="Tax Document"
                            data-cert-desc="Document certifying that a business has paid all required taxes and has no outstanding tax liabilities.">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger-custom btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCertificateModal"
                            data-cert-id="2"
                            data-cert-name="Tax Clearance">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Certificate Card 3 -->
    <div class="col certificate-item">
        <div class="card certificate-card">
            <div class="card-body">
                <h5 class="card-title">DTI Registration</h5>
                <p class="card-text">Certificate of business name registration issued by the Department of Trade and Industry.</p>
                <div class="certificate-date mb-3">
                    <i class="far fa-calendar-alt me-1"></i> Uploaded on July 10, 2023
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewCertificateModal"
                            data-cert-id="3"
                            data-cert-name="DTI Registration"
                            data-cert-date="July 10, 2023"
                            data-cert-type="Registration Document"
                            data-cert-desc="Certificate of business name registration issued by the Department of Trade and Industry.">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-warning-custom btn-sm" data-bs-toggle="modal" data-bs-target="#editCertificateModal"
                            data-cert-id="3"
                            data-cert-name="DTI Registration"
                            data-cert-date="July 10, 2023"
                            data-cert-type="Registration Document"
                            data-cert-desc="Certificate of business name registration issued by the Department of Trade and Industry.">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger-custom btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCertificateModal"
                            data-cert-id="3"
                            data-cert-name="DTI Registration">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Certificate Card 4 -->
    <div class="col certificate-item">
        <div class="card certificate-card">
            <div class="card-body">
                <h5 class="card-title">Export License</h5>
                <p class="card-text">Official permit allowing businesses to export specific goods to international markets.</p>
                <div class="certificate-date mb-3">
                    <i class="far fa-calendar-alt me-1"></i> Uploaded on August 5, 2023
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewCertificateModal"
                            data-cert-id="4"
                            data-cert-name="Export License"
                            data-cert-date="August 5, 2023"
                            data-cert-type="License"
                            data-cert-desc="Official permit allowing businesses to export specific goods to international markets.">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-warning-custom btn-sm" data-bs-toggle="modal" data-bs-target="#editCertificateModal"
                            data-cert-id="4"
                            data-cert-name="Export License"
                            data-cert-date="August 5, 2023"
                            data-cert-type="License"
                            data-cert-desc="Official permit allowing businesses to export specific goods to international markets.">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger-custom btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCertificateModal"
                            data-cert-id="4"
                            data-cert-name="Export License">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Certificate Card 5 -->
    <div class="col certificate-item">
        <div class="card certificate-card">
            <div class="card-body">
                <h5 class="card-title">Import License</h5>
                <p class="card-text">Legal authorization for businesses to import regulated goods from foreign countries.</p>
                <div class="certificate-date mb-3">
                    <i class="far fa-calendar-alt me-1"></i> Uploaded on September 12, 2023
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#viewCertificateModal"
                            data-cert-id="5"
                            data-cert-name="Import License"
                            data-cert-date="September 12, 2023"
                            data-cert-type="License"
                            data-cert-desc="Legal authorization for businesses to import regulated goods from foreign countries.">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn btn-warning-custom btn-sm" data-bs-toggle="modal" data-bs-target="#editCertificateModal"
                            data-cert-id="5"
                            data-cert-name="Import License"
                            data-cert-date="September 12, 2023"
                            data-cert-type="License"
                            data-cert-desc="Legal authorization for businesses to import regulated goods from foreign countries.">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger-custom btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCertificateModal"
                            data-cert-id="5"
                            data-cert-name="Import License">
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
                                <h4 class="certificate-details-title" id="viewCertName">Business Permit</h4>
                                <p class="certificate-details-subtitle" id="viewUploadDate">Uploaded on May 15, 2023</p>
                            </div>
                            
                            <div class="certificate-detail-row">
                                <div class="certificate-detail-label">Certificate Type:</div>
                                <div class="certificate-detail-value" id="viewCertType">Business Document</div>
                            </div>
                            
                            <div class="certificate-detail-row">
                                <div class="certificate-detail-label">Description:</div>
                                <div class="certificate-detail-value" id="viewCertDesc">
                                    Official document that authorizes a business to operate within a specific jurisdiction.
                                </div>
                            </div>
                            
                            <div class="certificate-detail-row">
                                <div class="certificate-detail-label">File Format:</div>
                                <div class="certificate-detail-value">PDF Document</div>
                                </div>
                            
                            <div class="certificate-detail-row">
                                <div class="certificate-detail-label">Last Modified:</div>
                                <div class="certificate-detail-value">May 18, 2023</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right side - Certificate Image -->
                    <div class="col-md-7">
                        <div class="certificate-details-container d-flex flex-column align-items-center justify-content-center">
                            <img src="img/SampleCertificate.png" alt="Certificate Preview" class="img-fluid mb-3" id="viewCertificateImage" style="max-height: 400px; width: auto;">
                            <div class="mt-3">
                                <a href="#" class="btn btn-success">
                                    <i class="fas fa-download me-2"></i> Download Certificate
                                </a>
                                <a href="#" class="btn btn-secondary">
                                    <i class="fas fa-print me-2"></i> Print Certificate
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning-custom" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editCertificateModal">
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
                <form id="addCertificateForm">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Certificate Information</h5>
                            
                            <div class="form-group mb-3">
                                <label for="certName" class="form-label">Certificate Name</label>
                                <input type="text" class="form-control" id="certName" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="certType" class="form-label">Certificate Type</label>
                                <select class="form-select" id="certType" required>
                                    <option value="" selected disabled>Select certificate type</option>
                                    <option value="business">Business Document</option>
                                    <option value="tax">Tax Document</option>
                                    <option value="dti">Registration Document</option>
                                    <option value="export">License</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="certDesc" class="form-label">Description</label>
                                <textarea class="form-control" id="certDesc" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <!-- File Upload -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Certificate Upload</h5>
                            
                            <div class="form-group mb-3">
                                <label for="certFile" class="form-label">Certificate File</label>
                                <input type="file" class="form-control" id="certFile" accept=".pdf,.jpg,.jpeg,.png" required>
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
                <form id="editCertificateForm">
                    <input type="hidden" id="editCertId" value="">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Certificate Information</h5>
                            
                            <div class="form-group mb-3">
                                <label for="editCertName" class="form-label">Certificate Name</label>
                                <input type="text" class="form-control" id="editCertName" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="editCertType" class="form-label">Certificate Type</label>
                                <select class="form-select" id="editCertType" required>
                                    <option value="business">Business Document</option>
                                    <option value="tax">Tax Document</option>
                                    <option value="dti">Registration Document</option>
                                    <option value="export">License</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="editCertDesc" class="form-label">Description</label>
                                <textarea class="form-control" id="editCertDesc" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <!-- File Upload -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Certificate File</h5>
                            
                            <div class="form-group mb-3">
                                <label for="editCertFile" class="form-label">Replace Certificate (Optional)</label>
                                <input type="file" class="form-control" id="editCertFile" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text">Leave empty to keep the current certificate</div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Current Certificate</label>
                                <div class="card bg-light p-3 text-center">
                                    <img src="img/cert2.png" alt="Current Certificate" class="img-fluid mx-auto" style="max-height: 180px;" id="editCertPreview">
                                </div>
                            </div>
                        </div>
                    </div>
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
                <input type="hidden" id="deleteCertId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt me-2"></i> Delete Certificate
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Document ready function
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        
        // View Certificate Modal Data
        $('#viewCertificateModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var certId = button.data('cert-id')
            var certName = button.data('cert-name')
            var certDate = button.data('cert-date')
            var certType = button.data('cert-type')
            var certDesc = button.data('cert-desc')
            
            var modal = $(this)
            modal.find('#viewCertName').text(certName)
            modal.find('#viewUploadDate').text('Uploaded on ' + certDate)
            modal.find('#viewCertType').text(certType)
            modal.find('#viewCertDesc').text(certDesc)
        })
        
        // Edit Certificate Modal Data
        $('#editCertificateModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var certId = button.data('cert-id')
            var certName = button.data('cert-name')
            var certDate = button.data('cert-date')
            var certType = button.data('cert-type')
            var certDesc = button.data('cert-desc')
            
            var modal = $(this)
            modal.find('#editCertId').val(certId)
            modal.find('#editCertName').val(certName)
            
            // Set the correct certificate type in the dropdown
            var certTypeSelect = modal.find('#editCertType')
            certTypeSelect.find('option').each(function() {
                if ($(this).text() === certType) {
                    $(this).prop('selected', true)
                }
            })
            
            modal.find('#editCertDesc').val(certDesc)
        })
        
        // Delete Certificate Modal Data
        $('#deleteCertificateModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var certId = button.data('cert-id')
            var certName = button.data('cert-name')
            
            var modal = $(this)
            modal.find('#deleteCertName').text(certName)
            modal.find('#deleteCertId').val(certId)
        })
        
        // Search functionality
        $('#searchCertificate').on('keyup', function() {
            var searchValue = $(this).val().toLowerCase()
            
            $('.certificate-item').each(function() {
                var certName = $(this).find('.card-title').text().toLowerCase()
                var certDesc = $(this).find('.card-text').text().toLowerCase()
                var certType = $(this).find('.certificate-type').text().toLowerCase()
                
                if (certName.includes(searchValue) || 
                    certDesc.includes(searchValue) || 
                    certType.includes(searchValue)) {
                    $(this).show()
                } else {
                    $(this).hide()
                }
            })
        })
        
        // Form submission handlers
        $('#addCertificateForm').on('submit', function(e) {
            e.preventDefault()
            // Here you would typically handle the form submission via AJAX
            
            // For demo purposes, just close the modal and show a success message
            $('#addCertificateModal').modal('hide')
            showAlert('success', 'Certificate added successfully!')
        })
        
        $('#editCertificateForm').on('submit', function(e) {
            e.preventDefault()
            // Here you would typically handle the form submission via AJAX
            
            // For demo purposes, just close the modal and show a success message
            $('#editCertificateModal').modal('hide')
            showAlert('success', 'Certificate updated successfully!')
        })
        
        $('#confirmDeleteBtn').on('click', function() {
            // Here you would typically handle the deletion via AJAX
            
            // For demo purposes, just close the modal and show a success message
            $('#deleteCertificateModal').modal('hide')
            showAlert('success', 'Certificate deleted successfully!')
        })
        
        // Certificate file preview
        $('#certFile').on('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewPlaceholder').hide();
                    $('#certPreview').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(file);
            }
        });
        
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
        
        // Edit certificate file preview
        $('#editCertFile').on('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#editCertPreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>

<?php
// Include the footer
include('footer.php');
?>
</body>
</html>

