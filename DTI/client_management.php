<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="img/logowhite.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table-container {
            margin: 20px;
        }
        /* Custom styles for DataTables controls */
        .dataTables_wrapper .dataTables_filter {
            display: flex;
            align-items: center;
            float: right;
        }
        .add-client-btn {
            margin-right: 15px;
        }
        /* Make the search input wider */
        .dataTables_filter input {
            width: 200px !important;
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
        .client-image-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 20px;
        }
        .client-image {
            width: 100%;
            max-width: 400px;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .certificate-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            width: 100%;
            max-width: 400px;
        }
        .certificate-buttons .btn {
            flex: 1;
        }
        .form-container {
            padding: 15px;
            border: 2px solid rgba(0,0,0,0.1);
            border-radius: 8px;
            background-color: #ffffff;
            height: calc(90% - 30px);
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
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            font-weight: 300;
        }
        .form-input-box {
            flex: 0 0 50%;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            background-color: #fff;
        }
        .form-input-box input,
        .form-input-box select,
        .form-input-box textarea {
            width: 100%;
            border: none;
            background: transparent;
            outline: none;
            padding: 0;
        }
        .form-input-box input:focus,
        .form-input-box select:focus,
        .form-input-box textarea:focus {
            box-shadow: none;
        }
        /* View Info specific styles */
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
        /* Email modal styles */
        #emailModal .modal-dialog {
            max-width: 500px;
        }
        @media (max-width: 767px) {
            .form-row {
                flex-direction: column;
            }
            .form-label-box,
            .form-input-box,
            .form-value-box {
                flex: 0 0 100%;
            }
            .modal-content {
                height: auto;
            }
            .certificate-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="table-responsive">
            <table id="clientTable" class="table table-striped table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Name of the Client</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Client Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>John Doe</td>
                        <td>Male</td>
                        <td>30</td>
                        <td>Premium</td>
                        <td>
                            <button class="btn btn-success btn-sm view-info-btn" data-id="1">View Info</button>
                            <button class="btn btn-danger btn-sm">Remove Client</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Jane Smith</td>
                        <td>Female</td>
                        <td>28</td>
                        <td>Regular</td>
                        <td>
                            <button class="btn btn-success btn-sm view-info-btn" data-id="2">View Info</button>
                            <button class="btn btn-danger btn-sm">Remove Client</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Client Modal -->
    <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row h-100">
                        <!-- Left side - Form -->
                        <div class="col-md-7">
                            <div class="form-container">
                                <div class="form-scroll">
                                    <form id="addClientForm">
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label for="clientName">Client Name</label>
                                            </div>
                                            <div class="form-input-box">
                                                <input type="text" id="clientName" required>
                                            </div>
                                        </div>
                                       
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label for="clientGender">Gender</label>
                                            </div>
                                            <div class="form-input-box">
                                                <select id="clientGender" required>
                                                    <option value="" selected disabled>Select Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                       
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label for="clientAge">Age</label>
                                            </div>
                                            <div class="form-input-box">
                                                <input type="number" id="clientAge" min="1" max="120" required>
                                            </div>
                                        </div>
                                       
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label for="clientType">Client Type</label>
                                            </div>
                                            <div class="form-input-box">
                                                <select id="clientType" required>
                                                    <option value="" selected disabled>Select Client Type</option>
                                                    <option value="Regular">Regular</option>
                                                    <option value="Premium">Premium</option>
                                                    <option value="VIP">VIP</option>
                                                </select>
                                            </div>
                                        </div>
                                       
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label for="businessName">Business Name</label>
                                            </div>
                                            <div class="form-input-box">
                                                <input type="text" id="businessName">
                                            </div>
                                        </div>
                                       
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label for="certificateType">Type of Certificate</label>
                                            </div>
                                            <div class="form-input-box">
                                                <select id="certificateType" required>
                                                    <option value="" selected disabled>Select Certificate Type</option>
                                                    <option value="Certificate2">Certificate 2</option>
                                                    <option value="Certificate1">Certificate 1</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label for="clientEmail">Email Address</label>
                                            </div>
                                            <div class="form-input-box">
                                                <input type="email" id="clientEmail" placeholder="For certificate delivery">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                       
                        <!-- Right side - Image -->
                        <div class="col-md-5">
                            <div class="client-image-container">
                                <img src="img/cert2.png"
                                     alt="Certificate Preview"
                                     class="client-image">
                                     
                                <!-- Certificate action buttons -->
                                <div class="certificate-buttons">
                                    <button type="button" class="btn btn-primary" id="downloadCertBtn">
                                        <i class="fas fa-download"></i> Download Certificate
                                    </button>
                                    <button type="button" class="btn btn-success" id="emailCertBtn">
                                        <i class="fas fa-envelope"></i> Send to Email
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-primary btn-lg" id="saveClientBtn">Add Client</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- View Client Info Modal -->
    <div class="modal fade" id="viewClientModal" tabindex="-1" aria-labelledby="viewClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewClientModalLabel">Client Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row h-100">
                        <!-- Left side - Client Info -->
                        <div class="col-md-7">
                            <div class="form-container">
                                <div class="form-scroll">
                                    <div id="clientInfoDisplay">
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label>Client Name</label>
                                            </div>
                                            <div class="form-value-box" id="viewClientName">
                                                John Doe
                                            </div>
                                        </div>
                                       
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label>Gender</label>
                                            </div>
                                            <div class="form-value-box" id="viewClientGender">
                                                Male
                                            </div>
                                        </div>
                                       
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label>Age</label>
                                            </div>
                                            <div class="form-value-box" id="viewClientAge">
                                                30
                                            </div>
                                        </div>
                                       
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label>Client Type</label>
                                            </div>
                                            <div class="form-value-box" id="viewClientType">
                                                Premium
                                            </div>
                                        </div>
                                       
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label>Business Name</label>
                                            </div>
                                            <div class="form-value-box" id="viewBusinessName">
                                                Acme Corporation
                                            </div>
                                        </div>
                                       
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label>Certificate Type</label>
                                            </div>
                                            <div class="form-value-box" id="viewCertificateType">
                                                Certificate 2
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label>Email Address</label>
                                            </div>
                                            <div class="form-value-box" id="viewClientEmail">
                                                john.doe@example.com
                                            </div>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-label-box">
                                                <label>Registration Date</label>
                                            </div>
                                            <div class="form-value-box" id="viewRegistrationDate">
                                                2023-05-15
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                        <!-- Right side - Certificate Image -->
                        <div class="col-md-5">
                            <div class="client-image-container">
                                <img src="img/cert2.png"
                                     alt="Certificate Preview"
                                     class="client-image" id="viewCertificateImage">
                                     
                                <!-- Certificate action buttons -->
                                <div class="certificate-buttons">
                                    <button type="button" class="btn btn-primary" id="viewDownloadCertBtn">
                                        <i class="fas fa-download"></i> Download Certificate
                                    </button>
                                    <button type="button" class="btn btn-success" id="viewEmailCertBtn">
                                        <i class="fas fa-envelope"></i> Send to Email
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-warning btn-lg" id="editClientBtn">Edit Client</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Email Certificate Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailModalLabel">Send Certificate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="emailForm">
                        <div class="mb-3">
                            <label for="recipientEmail" class="form-label">Recipient Email</label>
                            <input type="email" class="form-control" id="recipientEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailSubject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="emailSubject" value="Your Certificate" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="emailMessage" rows="4">Please find your certificate attached to this email.</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendEmailBtn">Send</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#clientTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "language": {
                    "search": "Search Client's Name:"
                }

            });
           
            // Add the "Add Client" button next to the search bar
            $('.dataTables_filter').prepend('<button class="btn btn-dark add-client-btn" id="addClientBtn"><i class="fas fa-plus"></i> Add Client</button>');
           
            // Add Client button click event
            $(document).on('click', '#addClientBtn', function() {
                $('#addClientModal').modal('show');
            });
            
            // Download Certificate button click event
            $('#downloadCertBtn').click(function() {
                // Get the certificate image
                var certificateImg = $('.client-image').attr('src');
                
                // Create a temporary link element
                var downloadLink = document.createElement('a');
                downloadLink.href = certificateImg;
                downloadLink.download = 'certificate.png';
                
                // Append to body, click and remove
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
                
                alert('Certificate download started!');
            });
            
            // Email Certificate button click event
            $('#emailCertBtn').click(function() {
                // Get email from the form if available
                var clientEmail = $('#clientEmail').val();
                if (clientEmail) {
                    $('#recipientEmail').val(clientEmail);
                }
                
                // Show email modal
                $('#emailModal').modal('show');
            });

                            // View Info button click event
                $(document).on('click', '.view-info-btn', function() {
                    // Get the client ID from the data attribute
                    var clientId = $(this).data('id');
                    
                    // In a real application, you would fetch client data based on the ID
                    // For this example, we'll just use the data we have
                    
                    // Set client data in the view modal based on which client was clicked
                    if (clientId == 1) {
                        $('#viewClientName').text('John Doe');
                        $('#viewClientGender').text('Male');
                        $('#viewClientAge').text('30');
                        $('#viewClientType').text('Premium');
                        $('#viewBusinessName').text('Acme Corporation');
                        $('#viewCertificateType').text('Certificate 2');
                        $('#viewClientEmail').text('john.doe@example.com');
                        $('#viewRegistrationDate').text('2023-05-15');
                    } else if (clientId == 2) {
                        $('#viewClientName').text('Jane Smith');
                        $('#viewClientGender').text('Female');
                        $('#viewClientAge').text('28');
                        $('#viewClientType').text('Regular');
                        $('#viewBusinessName').text('Smith Enterprises');
                        $('#viewCertificateType').text('Certificate 1');
                        $('#viewClientEmail').text('jane.smith@example.com');
                        $('#viewRegistrationDate').text('2023-06-20');
                    }
                    
                    // Show the view client modal
                    $('#viewClientModal').modal('show');
                });

                // View Download Certificate button click event
                $('#viewDownloadCertBtn').click(function() {
                    // Get the certificate image
                    var certificateImg = $('#viewCertificateImage').attr('src');
                    
                    // Create a temporary link element
                    var downloadLink = document.createElement('a');
                    downloadLink.href = certificateImg;
                    downloadLink.download = 'certificate.png';
                    
                    // Append to body, click and remove
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                    
                    alert('Certificate download started!');
                });

                // View Email Certificate button click event
                $('#viewEmailCertBtn').click(function() {
                    // Get email from the client info
                    var clientEmail = $('#viewClientEmail').text().trim();
                    if (clientEmail) {
                        $('#recipientEmail').val(clientEmail);
                    }
                    
                    // Show email modal
                    $('#emailModal').modal('show');
                });
                            
            // Send Email button click event
            $('#sendEmailBtn').click(function() {
                // Validate email form
                if (!$('#emailForm')[0].checkValidity()) {
                    $('#emailForm')[0].reportValidity();
                    return;
                }
                
                // In a real application, you would send the email via AJAX to your server
                // For this example, we'll just show a success message
                
                // Close the email modal
                $('#emailModal').modal('hide');
                
                // Show success message
                alert('Certificate sent to ' + $('#recipientEmail').val() + ' successfully!');
            });
           
            // Save Client button click event
            $('#saveClientBtn').click(function() {
                // Validate form
                if (!$('#addClientForm')[0].checkValidity()) {
                    $('#addClientForm')[0].reportValidity();
                    return;
                }
               
                // Get form values
                var name = $('#clientName').val();
                var gender = $('#clientGender').val();
                var age = $('#clientAge').val();
                var type = $('#clientType').val();
               
                // Add new row to table
                var newRow = table.row.add([
                    table.data().count() + 1,
                    name,
                    gender,
                    age,
                    type,
                   '<button class="btn btn-success btn-sm view-info-btn" data-id="' + (table.data().count() + 1) + '">View Info</button> ' +
                    '<button class="btn btn-danger btn-sm">Remove Client</button>'
                ]).draw().node();
               
                // Highlight the new row briefly
                $(newRow).addClass('highlight');
                setTimeout(function() {
                    $(newRow).removeClass('highlight');
                }, 1000);
               
                // Reset form and close modal
                $('#addClientForm')[0].reset();
                $('#addClientModal').modal('hide');
               
                // Show success message
                alert('Client added successfully!');
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
