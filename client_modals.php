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
                            </div>
                            
                            <div class="client-detail-row">
                                <div class="client-detail-label">Reference ID:</div>
                                <div class="client-detail-value" id="viewClientReferenceId">Loading...</div>
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
                                <div class="client-detail-label">Region:</div>
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
                                <div class="client-detail-label">Type:</div>
                                <div class="client-detail-value" id="viewClientType">Loading...</div>
                            </div>
                            
                            <!-- Certificate View Button - Only shown if certificate exists -->
                            <div class="client-detail-row" id="certificateSection" style="display: none;">
                                <div class="client-detail-label">Certificate:</div>
                                <div class="client-detail-value">
                                    <button id="viewCertificateBtn" class="btn btn-info-custom btn-sm" onclick="viewCertificateFromModal()">
                                        <i class="fas fa-certificate"></i> View Certificate
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" id="viewClientCertPath" value="">
                        </div>
                    </div>
                    
                    <!-- Right side - Feedback and Additional Info -->
                    <div class="col-md-6">
                        <div class="client-details-container">
                            <div class="client-details-header">
                                <h4 class="client-details-title">Client Feedback</h4>
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
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> This action cannot be undone. All client data will be permanently removed.</p>
                <form id="deleteClientForm" method="POST" action="delete_client.php">
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

<!-- Edit Client Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editClientModalLabel">Edit Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editClientForm" method="POST" action="edit_client.php">
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
                                <select class="form-select" id="editClientType" name="editClientType">
                                    <option value="citizen">Citizen</option>
                                    <option value="business">Business</option>
                                    <option value="government">Government</option>
                                </select>
                            </div>
                            
                            <div class="row edit-citizen-only" style="display: none;">
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
                                <select class="form-select" id="editClientRegion" name="editClientRegion">
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

<!-- Certificate Modal -->
<div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
