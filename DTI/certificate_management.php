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
    
    .certificate-card {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        min-height: 160px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 5px solid #0d1b57;
        margin-bottom: 20px;
    }
    
    .certificate-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .certificate-card img {
        width: 130px;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .certificate-card:hover img {
        transform: scale(1.05);
    }
    
    .certificate-info {
        flex-grow: 1;
        padding: 0 20px;
    }
    
    .certificate-title {
        font-weight: 600;
        color: #0d1b57;
        margin-bottom: 8px;
        font-size: 18px;
    }
    
    .certificate-date {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .certificate-description {
        color: #495057;
        font-size: 14px;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .add-certificate-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 2px dashed #0d1b57;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 160px;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 20px;
    }
    
    .add-certificate-card:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
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
    
    .btn-primary-custom {
        background-color: #0d1b57;
        border-color: #0d1b57;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
        background-color: #162a78;
        border-color: #162a78;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 27, 87, 0.3);
    }
    
    .btn-warning-custom {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    
    .btn-warning-custom:hover {
        background-color: #e0a800;
        border-color: #d39e00;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }
    
    .btn-danger-custom {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    
    .btn-danger-custom:hover {
        background-color: #c82333;
        border-color: #bd2130;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }
    
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
    
    .filter-select {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .filter-select:focus {
        border-color: #0d1b57;
        box-shadow: 0 0 0 0.25rem rgba(13, 27, 87, 0.25);
    }
    
    /* Modal styles */
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
    
    .certificate-details-container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 25px;
        height: 100%;
        display: flex;
        flex-direction: column;
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
    
    .certificate-image-wrapper {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 25px;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .certificate-image {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 1px solid #dee2e6;
    }
    
    .certificate-actions {
        margin-top: 20px;
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    
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
    
    .edit-mode-container {
        display: none;
    }
    
    .view-mode-container {
        display: block;
    }
    
    @media (max-width: 767px) {
        .certificate-card {
            flex-direction: column;
            text-align: center;
            padding: 15px;
        }
        
        .certificate-card img {
            margin-bottom: 15px;
        }
        
        .certificate-info {
            padding: 0;
            margin-bottom: 15px;
        }
        
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
        <!-- Search and Filter Section -->
        <div class="search-container">
            <div class="row align-items-center">
                <div class="col-md-4 mb-3 mb-md-0">
                    <select class="form-select filter-select">
                        <option selected>All Certificate Types</option>
                        <option>Business Permit</option>
                        <option>Tax Clearance</option>
                        <option>DTI Registration</option>
                        <option>BIR Registration</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control search-input border-start-0" placeholder="Search certificates by name, date, or type...">
                    </div>
                </div>
                <div class="col-md-2 text-md-end">
                    <button class="btn btn-primary-custom w-100">
                        <i class="fas fa-filter me-2"></i> Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Certificates Grid -->
        <div class="row">
            <!-- Add Certificate Card -->
            <div class="col-md-6 col-lg-4">
                <div class="add-certificate-card" id="addCertificateBtn">
                    <div class="add-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="add-text">Add New Certificate</div>
                </div>
            </div>
            <!-- Certificate Cards -->
            <div class="col-md-6 col-lg-4">
                <div class="certificate-card">
                    <img src="img/cert2.png" alt="Business Permit">
                    <div class="certificate-info">
                        <h5 class="certificate-title">Business Permit</h5>
                        <div class="certificate-date">
                            <i class="far fa-calendar-alt me-1"></i> Uploaded: May 15, 2023
                        </div>
                        <div class="certificate-description">
                            Official document that authorizes a business to operate within a specific jurisdiction.
                        </div>
                        <div class="d-flex">
                            <button class="btn btn-primary-custom btn-sm me-2 view-certificate-btn" 
                                    data-cert-name="Business Permit" 
                                    data-cert-date="2023-05-15" 
                                    data-cert-id="1"
                                    data-cert-desc="Official document that authorizes a business to operate within a specific jurisdiction.">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                            <button class="btn btn-warning-custom btn-sm me-2 edit-certificate-btn"
                                    data-cert-name="Business Permit" 
                                    data-cert-date="2023-05-15" 
                                    data-cert-id="1"
                                    data-cert-desc="Official document that authorizes a business to operate within a specific jurisdiction.">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            <button class="btn btn-danger-custom btn-sm delete-certificate-btn"
                                    data-cert-id="1"
                                    data-cert-name="Business Permit">
                                <i class="fas fa-trash-alt me-1"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="certificate-card">
                    <img src="img/cert2.png" alt="Tax Clearance">
                    <div class="certificate-info">
                        <h5 class="certificate-title">Tax Clearance</h5>
                        <div class="certificate-date">
                            <i class="far fa-calendar-alt me-1"></i> Uploaded: June 20, 2023
                        </div>
                        <div class="certificate-description">
                            Document certifying that a business has paid all required taxes and has no outstanding tax liabilities.
                        </div>
                        <div class="d-flex">
                            <button class="btn btn-primary-custom btn-sm me-2 view-certificate-btn" 
                                    data-cert-name="Tax Clearance" 
                                    data-cert-date="2023-06-20" 
                                    data-cert-id="2"
                                    data-cert-desc="Document certifying that a business has paid all required taxes and has no outstanding tax liabilities.">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                            <button class="btn btn-warning-custom btn-sm me-2 edit-certificate-btn"
                                    data-cert-name="Tax Clearance" 
                                    data-cert-date="2023-06-20" 
                                    data-cert-id="2"
                                    data-cert-desc="Document certifying that a business has paid all required taxes and has no outstanding tax liabilities.">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            <button class="btn btn-danger-custom btn-sm delete-certificate-btn"
                                    data-cert-id="2"
                                    data-cert-name="Tax Clearance">
                                <i class="fas fa-trash-alt me-1"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="certificate-card">
                    <img src="img/cert2.png" alt="DTI Registration">
                    <div class="certificate-info">
                        <h5 class="certificate-title">DTI Registration</h5>
                        <div class="certificate-date">
                            <i class="far fa-calendar-alt me-1"></i> Uploaded: July 10, 2023
                        </div>
                        <div class="certificate-description">
                            Certificate of business name registration issued by the Department of Trade and Industry.
                        </div>
                        <div class="d-flex">
                            <button class="btn btn-primary-custom btn-sm me-2 view-certificate-btn" 
                                    data-cert-name="DTI Registration" 
                                    data-cert-date="2023-07-10" 
                                    data-cert-id="3"
                                    data-cert-desc="Certificate of business name registration issued by the Department of Trade and Industry.">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                            <button class="btn btn-warning-custom btn-sm me-2 edit-certificate-btn"
                                    data-cert-name="DTI Registration" 
                                    data-cert-date="2023-07-10" 
                                    data-cert-id="3"
                                    data-cert-desc="Certificate of business name registration issued by the Department of Trade and Industry.">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            <button class="btn btn-danger-custom btn-sm delete-certificate-btn"
                                    data-cert-id="3"
                                    data-cert-name="DTI Registration">
                                <i class="fas fa-trash-alt me-1"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Certificate Modal -->
<div class="modal fade" id="viewCertificateModal" tabindex="-1" aria-labelledby="viewCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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
                            
                            <div class="view-mode-container" id="viewModeContainer">
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
                                    <div class="certificate-detail-label">Issuing Authority:</div>
                                    <div class="certificate-detail-value">Local Government Unit</div>
                                </div>
                                
                                <div class="certificate-detail-row">
                                    <div class="certificate-detail-label">Valid Until:</div>
                                    <div class="certificate-detail-value">December 31, 2023</div>
                                </div>
                                
                                <div class="certificate-actions">
                                    <button class="btn btn-warning-custom" id="switchToEditMode">
                                        <i class="fas fa-edit me-2"></i> Edit Certificate
                                    </button>
                                    <button class="btn btn-danger-custom" id="deleteCertBtn">
                                        <i class="fas fa-trash-alt me-2"></i> Delete
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Edit Mode Container (Initially Hidden) -->
                            <div class="edit-mode-container" id="editModeContainer">
                                <form id="editCertificateForm">
                                    <input type="hidden" id="editCertId" value="">
                                    
                                    <div class="form-group">
                                        <label for="editCertName" class="form-label">Certificate Name</label>
                                        <input type="text" class="form-control" id="editCertName" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="editCertType" class="form-label">Certificate Type</label>
                                        <select class="form-select" id="editCertType" required>
                                            <option value="Business Document">Business Document</option>
                                            <option value="Tax Document">Tax Document</option>
                                            <option value="Registration Document">Registration Document</option>
                                            <option value="License">License</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="editCertDesc" class="form-label">Description</label>
                                        <textarea class="form-control" id="editCertDesc" rows="3" required></textarea>
                                        <div class="form-text">Provide a brief description of what this certificate is for.</div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="editIssuingAuthority" class="form-label">Issuing Authority</label>
                                        <input type="text" class="form-control" id="editIssuingAuthority" value="Local Government Unit">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="editValidUntil" class="form-label">Valid Until</label>
                                        <input type="date" class="form-control" id="editValidUntil" value="2023-12-31">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="editCertImage" class="form-label">Replace Certificate Image</label>
                                        <input type="file" class="form-control" id="editCertImage">
                                        <div class="form-text">Leave empty to keep the current image.</div>
                                    </div>
                                    
                                    <div class="certificate-actions">
                                        <button type="submit" class="btn btn-primary-custom">
                                            <i class="fas fa-save me-2"></i> Save Changes
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="cancelEdit">
                                            <i class="fas fa-times me-2"></i> Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right side - Certificate Image -->
                    <div class="col-md-7">
                        <div class="certificate-image-wrapper">
                            <img src="img/SampleCertificate.png" alt="Certificate Preview" class="certificate-image" id="viewCertificateImage">
                            <div class="mt-3 text-center">
                                <a href="#" class="btn btn-primary-custom" id="downloadCertBtn">
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
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="addCertName" class="form-label">Certificate Name</label>
                                <input type="text" class="form-control" id="addCertName" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="addCertType" class="form-label">Certificate Type</label>
                                <select class="form-select" id="addCertType" required>
                                    <option value="" selected disabled>Select certificate type</option>
                                    <option value="Business Document">Business Document</option>
                                    <option value="Tax Document">Tax Document</option>
                                    <option value="Registration Document">Registration Document</option>
                                    <option value="License">License</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="addCertDesc" class="form-label">Description</label>
                                <textarea class="form-control" id="addCertDesc" rows="3" required></textarea>
                                <div class="form-text">Provide a brief description of what this certificate is for.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="addIssuingAuthority" class="form-label">Issuing Authority</label>
                                <input type="text" class="form-control" id="addIssuingAuthority">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="addValidUntil" class="form-label">Valid Until
                                <label for="addValidUntil" class="form-label">Valid Until</label>
                                <input type="date" class="form-control" id="addValidUntil">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="addCertImage" class="form-label">Certificate Image</label>
                                <input type="file" class="form-control" id="addCertImage" required>
                                <div class="form-text">Upload a clear image of the certificate (JPG, PNG, PDF).</div>
                            </div>
                            
                            <div class="mt-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="addCertVerified">
                                    <label class="form-check-label" for="addCertVerified">
                                        Mark as verified certificate
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary-custom" id="submitAddCertificate">
                    <i class="fas fa-plus me-2"></i> Add Certificate
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCertificateModal" tabindex="-1" aria-labelledby="deleteCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCertificateModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the certificate "<span id="deleteCertName"></span>"?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> This action cannot be undone.</p>
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

<script>
    $(document).ready(function() {
        // View Certificate button click event
        $(document).on('click', '.view-certificate-btn', function() {
            // Get certificate data from data attributes
            var certId = $(this).data('cert-id');
            var certName = $(this).data('cert-name');
            var certDate = $(this).data('cert-date');
            var certDesc = $(this).data('cert-desc');
            
            // Set certificate data in the view modal
            $('#viewCertName').text(certName);
            $('#viewUploadDate').text('Uploaded on ' + certDate);
            $('#viewCertDesc').text(certDesc);
            
            // Reset to view mode
            $('#viewModeContainer').show();
            $('#editModeContainer').hide();
            
            // Show the view certificate modal
            $('#viewCertificateModal').modal('show');
        });
        
        // Edit Certificate button click event
        $(document).on('click', '.edit-certificate-btn', function() {
            // Get certificate data from data attributes
            var certId = $(this).data('cert-id');
            var certName = $(this).data('cert-name');
            var certDate = $(this).data('cert-date');
            var certDesc = $(this).data('cert-desc');
            
            // Set certificate data in the view modal
            $('#viewCertName').text(certName);
            $('#viewUploadDate').text('Uploaded on ' + certDate);
            $('#viewCertDesc').text(certDesc);
            
            // Set data in edit form
            $('#editCertId').val(certId);
            $('#editCertName').val(certName);
            $('#editCertDesc').val(certDesc);
            
            // Switch to edit mode
            $('#viewModeContainer').hide();
            $('#editModeContainer').show();
            
            // Show the view certificate modal
            $('#viewCertificateModal').modal('show');
        });
        
        // Switch to Edit Mode button click event
        $('#switchToEditMode').click(function() {
            // Get current values from view mode
            var certName = $('#viewCertName').text();
            var certDesc = $('#viewCertDesc').text();
            var certId = $('.view-certificate-btn').data('cert-id');
            
            // Set values in edit form
            $('#editCertId').val(certId);
            $('#editCertName').val(certName);
            $('#editCertDesc').val(certDesc);
            
            // Switch to edit mode
            $('#viewModeContainer').hide();
            $('#editModeContainer').show();
        });
        
        // Cancel Edit button click event
        $('#cancelEdit').click(function() {
            // Switch back to view mode
            $('#editModeContainer').hide();
            $('#viewModeContainer').show();
        });
        
        // Edit Certificate Form Submit
        $('#editCertificateForm').submit(function(e) {
            e.preventDefault();
            
            // Get form data
            var certId = $('#editCertId').val();
            var certName = $('#editCertName').val();
            var certDesc = $('#editCertDesc').val();
            var certType = $('#editCertType').val();
            var issuingAuthority = $('#editIssuingAuthority').val();
            var validUntil = $('#editValidUntil').val();
            
            // Here you would typically send an AJAX request to update the certificate
            // For demonstration, we'll just update the UI
            
            // Update the certificate card
            $('.view-certificate-btn[data-cert-id="' + certId + '"]').data('cert-name', certName);
            $('.view-certificate-btn[data-cert-id="' + certId + '"]').data('cert-desc', certDesc);
            
            // Update the card title
            $('.view-certificate-btn[data-cert-id="' + certId + '"]').closest('.certificate-card').find('.certificate-title').text(certName);
            
            // Update the card description
            $('.view-certificate-btn[data-cert-id="' + certId + '"]').closest('.certificate-card').find('.certificate-description').text(certDesc);
            
            // Update the view mode data
            $('#viewCertName').text(certName);
            $('#viewCertType').text(certType);
            $('#viewCertDesc').text(certDesc);
            
            // Show success message
            alert('Certificate updated successfully!');
            
            // Switch back to view mode
            $('#editModeContainer').hide();
            $('#viewModeContainer').show();
        });
        
        // Add Certificate button click event
        $('#addCertificateBtn').click(function() {
            // Reset the add certificate form
            $('#addCertificateForm')[0].reset();
            
            // Show the add certificate modal
            $('#addCertificateModal').modal('show');
        });
        
        // Add Certificate Form Submit
        $('#submitAddCertificate').click(function() {
            // Check if form is valid
            if (!$('#addCertificateForm')[0].checkValidity()) {
                $('#addCertificateForm')[0].reportValidity();
                return;
            }
            
            // Get form data
            var certName = $('#addCertName').val();
            var certType = $('#addCertType').val();
            var certDesc = $('#addCertDesc').val();
            var issuingAuthority = $('#addIssuingAuthority').val();
            var validUntil = $('#addValidUntil').val();
            var isVerified = $('#addCertVerified').is(':checked');
            
            // Here you would typically send an AJAX request to add the certificate
            // For demonstration, we'll just update the UI
            
            // Create a new certificate card
            var newCertId = Math.floor(Math.random() * 1000); // Generate a random ID for demo
            var today = new Date();
            var formattedDate = today.getFullYear() + '-' + 
                                ('0' + (today.getMonth() + 1)).slice(-2) + '-' + 
                                ('0' + today.getDate()).slice(-2);
            
            var newCertCard = `
                <div class="col-md-6 col-lg-4">
                    <div class="certificate-card">
                        <img src="img/cert2.png" alt="${certName}">
                        <div class="certificate-info">
                            <h5 class="certificate-title">${certName}</h5>
                            <div class="certificate-date">
                                <i class="far fa-calendar-alt me-1"></i> Uploaded: ${formattedDate}
                            </div>
                            <div class="certificate-description">
                                ${certDesc}
                            </div>
                            <div class="d-flex">
                                <button class="btn btn-primary-custom btn-sm me-2 view-certificate-btn" 
                                        data-cert-name="${certName}" 
                                        data-cert-date="${formattedDate}" 
                                        data-cert-id="${newCertId}"
                                        data-cert-desc="${certDesc}">
                                    <i class="fas fa-eye me-1"></i> View
                                </button>
                                <button class="btn btn-warning-custom btn-sm me-2 edit-certificate-btn"
                                        data-cert-name="${certName}" 
                                        data-cert-date="${formattedDate}" 
                                        data-cert-id="${newCertId}"
                                        data-cert-desc="${certDesc}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <button class="btn btn-danger-custom btn-sm delete-certificate-btn"
                                        data-cert-id="${newCertId}"
                                        data-cert-name="${certName}">
                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add the new certificate card to the grid
            $('#addCertificateBtn').closest('.col-md-6').after(newCertCard);
            
            // Show success message
            alert('Certificate added successfully!');
            
            // Close the modal
            $('#addCertificateModal').modal('hide');
        });
        
        // Delete Certificate button click event
        $(document).on('click', '.delete-certificate-btn', function() {
            var certId = $(this).data('cert-id');
            var certName = $(this).data('cert-name');
            
            // Set the certificate name and ID in the delete confirmation modal
            $('#deleteCertName').text(certName);
            $('#deleteCertId').val(certId);
            
            // Show the delete confirmation modal
            $('#deleteCertificateModal').modal('show');
        });
        
        // Delete button in view modal
        $('#deleteCertBtn').click(function() {
            var certName = $('#viewCertName').text();
            var certId = $('.view-certificate-btn').data('cert-id');
            
            // Set the certificate name and ID in the delete confirmation modal
            $('#deleteCertName').text(certName);
            $('#deleteCertId').val(certId);
            
            // Close the view modal and show the delete confirmation modal
            $('#viewCertificateModal').modal('hide');
            $('#deleteCertificateModal').modal('show');
        });
        
        // Confirm Delete button click event
        $('#confirmDeleteBtn').click(function() {
            var certId = $('#deleteCertId').val();
            
            // Here you would typically send an AJAX request to delete the certificate
            // For demonstration, we'll just remove the card from the UI
            
            // Remove the certificate card
            $('.delete-certificate-btn[data-cert-id="' + certId + '"]').closest('.col-md-6').remove();
            
            // Show success message
            alert('Certificate deleted successfully!');
            
            // Close the delete confirmation modal
            $('#deleteCertificateModal').modal('hide');
        });
        
        // Download Certificate button click event
        $('#downloadCertBtn').click(function(e) {
            e.preventDefault();
            alert('Download functionality would be implemented here.');
        });
        
        // Print Certificate button click event
        $('#printCertBtn').click(function(e) {
            e.preventDefault();
            alert('Print functionality would be implemented here.');
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
// Include the footer
include('footer.php');
?>
</body>
</html>
