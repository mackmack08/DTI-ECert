
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
            background-color: #f8f9fa;
        }
        .certificate-card {
            background-color: #dfe3f4;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            min-height: 150px;
        }
        .certificate-card img {
            width: 120px;
            height: auto;
            border-radius: 5px;
        }
        .add-certificate {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 120px;
            height: 100px;
            background-color: #f0f0f0;
            border: 2px dashed #ccc;
            border-radius: 5px;
            font-size: 24px;
            font-weight: bold;
            color: #666;
            cursor: pointer;
        }
        .btn-dark {
            background-color: #0d1b57;
        }
        /* Add some spacing between buttons */
        .certificate-card .btn {
            margin-right: 5px;
        }
        
        /* Modal styles */
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        /* Custom large modal */
        .modal-xl-custom {
            max-width: 90%;
            width: 1200px;
        }
        .modal-content {
            height: 90vh;
            max-height: 800px;
        }
        .modal-body {
            overflow-y: auto;
        }
        .certificate-image-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 20px;
        }
        .certificate-image {
            width: 100%;
            max-width: 600px;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .form-container {
            padding: 4px;
            border: 2px solid rgba(0,0,0,0.1);
            border-radius: 8px;
            background-color: #ffffff;
            height: auto;
            margin: 10px 0;
        }
        .form-scroll {
            max-height: 100%;
            overflow-y: auto;
        }
        /* Boxed form styles */
        .form-row {
            display: flex;
            margin-bottom: 15px;
            gap: 5px;
        }
        .form-label-box {
            flex: 0 0 30%;
            background-color: 2px solid black;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            font-weight: 300;
        }
        .form-value-box {
            flex: 0 0 50%;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px 15px;
            background-color: #fff;
            min-height: 42px;
            display: flex;
            align-items: center;
        }
        @media (max-width: 767px) {
            .form-row {
                flex-direction: column;
            }
            .form-label-box,
            .form-value-box {
                flex: 0 0 100%;
            }
            .modal-content {
                height: auto;
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
        <div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <select class="form-select w-auto">
                    <option selected>Certificate Type</option>
                    <option>Business Permit</option>
                    <option>Tax Clearance</option>
                </select>
                <div class="input-group w-25">
                    <input type="text" class="form-control" placeholder="Search Certificate Name">
                    <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="row g-3">
                <!-- Add Certificate Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="certificate-card">
                        <div class="add-certificate">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <h5>Certificate Name</h5>
                            <button class="btn btn-dark btn-sm" id="addCertificateBtn">
                                <i class="fas fa-plus"></i> Add Certificate
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Certificate Cards -->
                <div class="col-md-6 col-lg-4">
                    <div class="certificate-card">
                        <img src="img/cert2.png" alt="Certificate">
                        <div>
                            <h5>Business Permit</h5>
                            <button class="btn btn-dark btn-sm view-certificate-btn" data-cert-name="Business Permit" data-cert-date="2023-05-15" data-cert-id="1">
                                <i class="fas fa-eye"></i> View Certificate
                            </button>
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="certificate-card">
                        <img src="img/cert2.png" alt="Certificate">
                        <div>
                            <h5>Tax Clearance</h5>
                            <button class="btn btn-dark btn-sm view-certificate-btn" data-cert-name="Tax Clearance" data-cert-date="2023-06-20" data-cert-id="2">
                                <i class="fas fa-eye"></i> View Certificate
                            </button>
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- View Certificate Modal -->
<div class="modal fade" id="viewCertificateModal" tabindex="-1" aria-labelledby="viewCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl-custom" style="max-width: 1200px; width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCertificateModalLabel">Certificate Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row h-100">
                    <!-- Left side - Certificate Info -->
                    <div class="col-md-5">
                        <div class="form-container">
                            <div class="form-scroll">
                                <div id="certificateInfoDisplay">
                                    <div class="form-row">
                                        <div class="form-label-box">
                                            <label>Certificate Name</label>
                                        </div>
                                        <div class="form-value-box" id="viewCertName">
                                            Business Permit
                                        </div>
                                    </div>
                                   
                                    <div class="form-row">
                                        <div class="form-label-box">
                                            <label>Date Uploaded</label>
                                        </div>
                                        <div class="form-value-box" id="viewUploadDate">
                                            2023-05-15
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <!-- Right side - Certificate Image -->
                    <div class="col-md-7">
                        <div class="certificate-image-container">
                            <img src="img/cert2.png"
                                 alt="Certificate Preview"
                                 class="certificate-image" id="viewCertificateImage">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Back</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // View Certificate button click event
        $(document).on('click', '.view-certificate-btn', function() {
            // Get certificate data from data attributes
            var certName = $(this).data('cert-name');
            var certDate = $(this).data('cert-date');
            
            // Set certificate data in the view modal
            $('#viewCertName').text(certName);
            $('#viewUploadDate').text(certDate);
            
            // Show the view certificate modal
            $('#viewCertificateModal').modal('show');
        });
        
        // Add Certificate button click event
        $('#addCertificateBtn').click(function() {
            alert('Add Certificate functionality would go here!');
            // You can implement a similar modal for adding certificates
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <?php
    // Add page-specific scripts if needed
    $additionalScripts = '
        <!-- Any additional scripts specific to this page -->
        <script>
            // Client management specific JavaScript
        </script>
    ';

    // Include the footer
    include('footer.php');
    ?>
</body>
</html>
