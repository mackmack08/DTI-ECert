<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="img/logowhite.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    body {
        background-color: #f5f5f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* Table Styles */
    .client-table {
        background-color: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    .client-table .table {
        margin-bottom: 0;
    }
    
    .client-table th {
        background-color: #f0f2f5;
        color: #0d1b57;
        font-weight: 600;
        border: none;
        padding: 15px;
        text-align: center;
    }
    
    .client-table td {
        vertical-align: middle;
        padding: 15px;
        border-color: #e9ecef;
        text-align: center;
    }
    
    .client-table tbody tr {
        transition: background-color 0.3s ease;
    }
    
    .client-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .client-table .client-name {
    margin-left: 23px;    
    font-weight: 600;
    color: #0d1b57;
    text-align: left;
    }
    
    .client-table .client-email {
    margin-left: 23px;
    color: #6c757d;
    font-size: 14px;
    text-align: left;
    }
    
    .client-type-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .client-type-citizen {
        background-color: #e3f2fd;
        color: #0d47a1;
    }
    
    .client-type-business {
        background-color: #e8f5e9;
        color: #1b5e20;
    }
    
    .client-type-government {
        background-color: #fff3e0;
        color: #e65100;
    }
    
    /* Add Client Button */
    .add-client-btn {
        background-color: #0d1b57;
        color: white;
        border-radius: 50px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 10px rgba(13, 27, 87, 0.3);
    
    }
    
    .add-client-btn:hover {
        background-color: #162a78;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(13, 27, 87, 0.4);
        color: white;
    }
    
    .add-client-btn i {
        margin-right: 8px;
    }
    
    /* Button Styles */
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
        color: white;
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
        width: 50px;
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
    
    /* Client Details Styles */
    .client-details-container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 25px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .client-details-header {
        border-bottom: 2px solid #0d1b57;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    
    .client-details-title {
        color: #0d1b57;
        font-weight: 600;
        font-size: 20px;
        margin-bottom: 5px;
    }
    
    .client-details-subtitle {
        color: #6c757d;
        font-size: 14px;
    }
    
    .client-detail-row {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 15px;
    }
    
    .client-detail-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .client-detail-label {
        flex: 0 0 40%;
        color: #495057;
        font-weight: 500;
    }
    
    .client-detail-value {
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
    
    /* Table Responsive Styles */
    @media (max-width: 992px) {
        .client-table .action-column {
            min-width: 200px;
        }
    }
    
    @media (max-width: 767px) {
        .client-table {
            overflow-x: auto;
        }
        
        .client-detail-row {
            flex-direction: column;
        }
        
        .client-detail-label,
        .client-detail-value {
            flex: 0 0 100%;
        }
        
        .client-detail-label {
            margin-bottom: 5px;
        }
    }
    </style>
</head>
<body>

<?php
// Set page-specific variables
$pageTitle = "DTI Client Management";
$currentPage = "Client Management";

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
        <!-- Search and Add Client Section -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6 text-md-start">
                <a href="#" class="btn add-client-btn" data-bs-toggle="modal" data-bs-target="#addClientModal">
                    <i class="fas fa-plus"></i> Add New Client
                </a>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control search-input border-start-0" placeholder="Search clients by name, email, or contact...">
                </div>
            </div>
        </div>
        <!-- Clients Table -->
        <div class="client-table">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 30%">Client Name</th>
                        <th style="width: 15%">Type</th>
                        <th style="width: 15%">Region</th>
                        <th style="width: 20%">Contact</th>
                        <th style="width: 20%" class="action-column">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="client-name">Juan Dela Cruz</div>
                            <div class="client-email">juan@example.com</div>
                        </td>
                        <td>
                            <span class="client-type-badge client-type-citizen">Citizen</span>
                        </td>
                        <td>NCR</td>
                        <td>09123456789</td>
                        <td>
                            <button class="btn btn-primary-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#viewClientModal" data-client-id="1">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-warning-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#editClientModal" data-client-id="1">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger-custom btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#deleteClientModal" data-client-id="1" data-client-name="Juan Dela Cruz">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="client-name">Manila Trading Co.</div>
                            <div class="client-email">info@manilatrading.com</div>
                        </td>
                        <td>
                            <span class="client-type-badge client-type-business">Business</span>
                            </td>
                        <td>NCR</td>
                        <td>(02) 8123-4567</td>
                        <td>
                            <button class="btn btn-primary-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#viewClientModal" data-client-id="2">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-warning-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#editClientModal" data-client-id="2">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger-custom btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#deleteClientModal" data-client-id="2" data-client-name="Manila Trading Co.">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="client-name">Department of Agriculture</div>
                            <div class="client-email">info@da.gov.ph</div>
                        </td>
                        <td>
                            <span class="client-type-badge client-type-government">Government</span>
                        </td>
                        <td>Region IV-A</td>
                        <td>(02) 8273-2474</td>
                        <td>
                            <button class="btn btn-primary-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#viewClientModal" data-client-id="3">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-warning-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#editClientModal" data-client-id="3">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger-custom btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#deleteClientModal" data-client-id="3" data-client-name="Department of Agriculture">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="client-name">Maria Reyes</div>
                            <div class="client-email">maria@example.com</div>
                        </td>
                        <td>
                            <span class="client-type-badge client-type-citizen">Citizen</span>
                        </td>
                        <td>Region III</td>
                        <td>09187654321</td>
                        <td>
                            <button class="btn btn-primary-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#viewClientModal" data-client-id="4">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-warning-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#editClientModal" data-client-id="4">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger-custom btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#deleteClientModal" data-client-id="4" data-client-name="Maria Reyes">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="client-name">Cebu Enterprises Inc.</div>
                            <div class="client-email">contact@cebuenterprises.com</div>
                        </td>
                        <td>
                            <span class="client-type-badge client-type-business">Business</span>
                        </td>
                        <td>Region VII</td>
                        <td>(032) 123-4567</td>
                        <td>
                            <button class="btn btn-primary-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#viewClientModal" data-client-id="5">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-warning-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#editClientModal" data-client-id="5">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger-custom btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#deleteClientModal" data-client-id="5" data-client-name="Cebu Enterprises Inc.">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="client-name">Pedro Santos</div>
                            <div class="client-email">pedro@example.com</div>
                        </td>
                        <td>
                            <span class="client-type-badge client-type-citizen">Citizen</span>
                        </td>
                        <td>Region I</td>
                        <td>09198765432</td>
                        <td>
                            <button class="btn btn-primary-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#viewClientModal" data-client-id="6">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-warning-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#editClientModal" data-client-id="6">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger-custom btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#deleteClientModal" data-client-id="6" data-client-name="Pedro Santos">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="client-name">Davao City LGU</div>
                            <div class="client-email">info@davaocity.gov.ph</div>
                        </td>
                        <td>
                            <span class="client-type-badge client-type-government">Government</span>
                        </td>
                        <td>Region XI</td>
                        <td>(082) 123-4567</td>
                        <td>
                            <button class="btn btn-primary-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#viewClientModal" data-client-id="7">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-warning-custom btn-sm btn-action me-1" data-bs-toggle="modal" data-bs-target="#editClientModal" data-client-id="7">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger-custom btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#deleteClientModal" data-client-id="7" data-client-name="Davao City LGU">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Client pagination">
                    <ul class="pagination justify-content-center">
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
                                <h4 class="client-details-title">Juan Dela Cruz</h4>
                                <p class="client-details-subtitle">
                                    <span class="badge bg-primary">Citizen</span>
                                </p>
                            </div>
                            
                            <div class="client-detail-row">
                                <div class="client-detail-label">Sex:</div>
                                <div class="client-detail-value">Male</div>
                            </div>
                            
                            <div class="client-detail-row">
                                <div class="client-detail-label">Age:</div>
                                <div class="client-detail-value">35</div>
                            </div>
                            
                            <div class="client-detail-row">
                                <div class="client-detail-label">Region of Residence:</div>
                                <div class="client-detail-value">NCR</div>
                            </div>
                            
                            <div class="client-detail-row">
                                <div class="client-detail-label">Contact Number:</div>
                                <div class="client-detail-value">09123456789</div>
                            </div>
                            
                            <div class="client-detail-row">
                                <div class="client-detail-label">Email Address:</div>
                                <div class="client-detail-value">juan@example.com</div>
                            </div>
                            
                            <div class="mt-4">
                                <h5 class="mb-3">Client Certificates</h5>
                                <div class="list-group">
                                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-certificate me-2 text-primary"></i>
                                            Business Permit
                                        </div>
                                        <button class="btn btn-sm btn-primary-custom">
                                            <i class="fas fa-download me-1"></i> Download
                                        </button>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-certificate me-2 text-primary"></i>
                                            Tax Clearance
                                        </div>
                                        <button class="btn btn-sm btn-primary-custom">
                                            <i class="fas fa-download me-1"></i> Download
                                        </button>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-certificate me-2 text-primary"></i>
                                            DTI Registration
                                        </div>
                                        <button class="btn btn-sm btn-primary-custom">
                                            <i class="fas fa-download me-1"></i> Download
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right side - Feedback and Additional Info -->
                    <div class="col-md-6">
                        <div class="client-details-container">
                            <div class="client-details-header">
                                <h4 class="client-details-title">Client Feedback</h4>
                                <p class="client-details-subtitle">Last feedback submitted on June 15, 2023</p>
                            </div>
                            
                            <div class="feedback-item">
                                <div class="feedback-question">1. SERVICE: Reliability and Outcome</div>
                                <div class="client-detail-value">Very Satisfied</div>
                            </div>
                            
                            <div class="feedback-item">
                                <div class="feedback-question">2. SERVICE: Access and Facilities</div>
                                <div class="client-detail-value">Satisfied</div>
                            </div>
                            
                            <div class="feedback-item">
                                <div class="feedback-question">3. RESOURCE SPEAKER: Reliability, Communication and Quality</div>
                                <div class="client-detail-value">Very Satisfied</div>
                            </div>
                            
                            <div class="feedback-item">
                                <div class="feedback-question">4. RESOURCE SPEAKER: Responsiveness and Integrity</div>
                                <div class="client-detail-value">Satisfied</div>
                            </div>
                            
                            <div class="feedback-item">
                                <div class="feedback-question">5. MODERATOR: Reliability and Responsiveness</div>
                                <div class="client-detail-value">Very Satisfied</div>
                            </div>
                            
                            <div class="feedback-item">
                                <div class="feedback-question">6. HOST/SECRETARIAT: Reliability and Responsiveness</div>
                                <div class="client-detail-value">Satisfied</div>
                            </div>
                            <div class="feedback-item">
                                <div class="feedback-question">7. OVERALL SATISFACTION RATING</div>
                                <div class="client-detail-value">Very Satisfied</div>
                            </div>
                            
                            <div class="feedback-item">
                                <div class="feedback-question">8. Comments/suggestions to help us improve our service/s:</div>
                                <div class="client-detail-value">
                                    The service was excellent overall. The staff were very helpful and knowledgeable.
                                    The service was excellent overall. The staff were very helpful and knowledgeable. 
                                    I would recommend improving the waiting area facilities.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning-custom" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editClientModal">
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
                <form id="addClientForm">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Basic Information</h5>
                            
                            <div class="form-group mb-3">
                                <label for="clientName" class="form-label">Full Name / Organization Name</label>
                                <input type="text" class="form-control" id="clientName" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="clientType" class="form-label">Client Type</label>
                                <select class="form-select" id="clientType" required>
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
                                        <select class="form-select" id="clientSex">
                                            <option value="" selected disabled>Select sex</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="clientAge" class="form-label">Age</label>
                                        <input type="number" class="form-control" id="clientAge" min="1" max="120">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="clientRegion" class="form-label">Region of Residence</label>
                                <select class="form-select" id="clientRegion" required>
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
                                <input type="text" class="form-control" id="clientContact" required>
                                <div class="form-text">Mobile number or landline</div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="clientEmail" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="clientEmail" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="clientAddress" class="form-label">Complete Address</label>
                                <textarea class="form-control" id="clientAddress" rows="3"></textarea>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="clientCertificate" class="form-label">Upload Certificate (Optional)</label>
                                <input type="file" class="form-control" id="clientCertificate">
                                <div class="form-text">You can upload client certificates later</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addClientForm" class="btn btn-primary-custom">
                    <i class="fas fa-plus me-2"></i> Add Client
                </button>
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
                <form id="editClientForm">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Basic Information</h5>
                            
                            <div class="form-group mb-3">
                                <label for="editClientName" class="form-label">Full Name / Organization Name</label>
                                <input type="text" class="form-control" id="editClientName" value="Juan Dela Cruz" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="editClientType" class="form-label">Client Type</label>
                                <select class="form-select" id="editClientType" required>
                                    <option value="citizen" selected>Citizen</option>
                                    <option value="business">Business</option>
                                    <option value="government">Government</option>
                                </select>
                            </div>
                            
                            <div class="row citizen-only">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="editClientSex" class="form-label">Sex</label>
                                        <select class="form-select" id="editClientSex">
                                            <option value="male" selected>Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="editClientAge" class="form-label">Age</label>
                                        <input type="number" class="form-control" id="editClientAge" value="35" min="1" max="120">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="editClientRegion" class="form-label">Region of Residence</label>
                                <select class="form-select" id="editClientRegion" required>
                                    <option value="NCR" selected>NCR</option>
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
                                <input type="text" class="form-control" id="editClientContact" value="09123456789" required>
                                <div class="form-text">Mobile number or landline</div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="editClientEmail" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="editClientEmail" value="juan@example.com" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="editClientAddress" class="form-label">Complete Address</label>
                                <textarea class="form-control" id="editClientAddress" rows="3">123 Main St., Quezon City</textarea>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="editClientCertificate" class="form-label">Upload New Certificate (Optional)</label>
                                <input type="file" class="form-control" id="editClientCertificate">
                                <div class="form-text">Leave empty to keep existing certificates</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editClientForm" class="btn btn-primary-custom">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
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
                <p>Are you sure you want to delete the client "<span id="deleteClientName">Juan Dela Cruz</span>"?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> This action cannot be undone. All client data and certificates will be permanently removed.</p>
                <input type="hidden" id="deleteClientId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt me-2"></i> Delete Client
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
// Include the footer
include('footer.php');
?>
</body>
</html>
                      
