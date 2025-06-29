<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addClientForm" action="add_client.php" method="post">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="client_name" class="form-label">Client Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="client_name" name="client_name" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="reference_id" class="form-label">Reference Id <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reference_id" name="reference_id" required>
                        </div>
                        <div class="col-md-6">
                            <label for="client_type" class="form-label">Client Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="client_type" name="client_type" required>
                                <option value="">Select Type</option>
                                <option value="Individual">Individual</option>
                                <option value="Business">Business</option>
                                <option value="Government">Government</option>
                                <option value="NGO">NGO</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="contact" class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact" name="contact" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="sex" class="form-label">Sex <span class="text-danger">*</span></label>
                            <select class="form-select" id="sex" name="sex" required>
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="age" class="form-label">Age <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="age" name="age" min="1" max="120" required>
                        </div>
                        <div class="col-md-4">
                            <label for="region" class="form-label">Region <span class="text-danger">*</span></label>
                            <select class="form-select" id="region" name="region" required>
                                <option value="">Select Region</option>
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
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="completion_date" class="form-label">Completion Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="completion_date" name="completion_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="file_id" class="form-label">Sheet <span class="text-danger">*</span></label>
                            <select class="form-select" id="file_id" name="file_id" required>
                                <option value="">Select Sheet</option>
                                <?php foreach ($availableSheets as $sheet): ?>
                                    <option value="<?php echo $sheet['id']; ?>">
                                        <?php echo htmlspecialchars($sheet['file_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cert_type" class="form-label">Certificate Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="cert_type" name="cert_type" required>
                                <option value="">Select Certificate Type</option>
                                <?php
                                // Fetch certificate types
                                $certQuery = "SELECT * FROM certificates where status = 'Unarchived'";
                                $certResult = $conn->query($certQuery);
                                if ($certResult && $certResult->num_rows > 0) {
                                    while ($cert = $certResult->fetch_assoc()) {
                                        echo '<option value="' . $cert['id'] . '">' . htmlspecialchars($cert['name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CARP <span class="text-danger">*</span></label>
                            <div class="d-flex mt-2">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="carp" id="carp_yes" value="yes" required>
                                    <label class="form-check-label" for="carp_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="carp" id="carp_no" value="no" required>
                                    <label class="form-check-label" for="carp_no">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <label for="staff_name_input">Conducted by:</label>
                                    <input type="text" id="staff_name_input" name="staff" value="" placeholder="Enter staff name" class="form-control"><br>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addClientForm" class="btn btn-primary">Save Client</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle add client form submission
    document.addEventListener('DOMContentLoaded', function() {
        const addClientForm = document.getElementById('addClientForm');
        if (addClientForm) {
            addClientForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form data
                const formData = new FormData(addClientForm);
                
                // Send AJAX request to save client data
                fetch('add_client.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert("Client added successfully!");
                        
                        // Close the modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addClientModal'));
                        modal.hide();
                        
                        // Reset the form
                        addClientForm.reset();
                        
                        // Refresh the client list if needed
                        if (typeof loadClients === 'function') {
                            loadClients();
                        } else {
                            // Reload the page if loadClients function is not available
                            window.location.reload();
                        }
                    } else {
                        // Show error message
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An error occurred while saving the client data.");
                });
            });
        }
    });
</script>
