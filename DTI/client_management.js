
// Handle client type selection to show/hide citizen-specific fields
document.addEventListener('DOMContentLoaded', function() {
    // For Add Client form
    const clientTypeSelect = document.getElementById('clientType');
    if (clientTypeSelect) {
        clientTypeSelect.addEventListener('change', function() {
            const citizenFields = document.querySelectorAll('#addClientModal .citizen-only');
            citizenFields.forEach(field => {
                if (this.value === 'citizen') {
                    field.style.display = 'flex';
                    // Make fields required
                    field.querySelectorAll('select, input').forEach(input => {
                        input.required = true;
                    });
                } else {
                    field.style.display = 'none';
                    // Make fields not required
                    field.querySelectorAll('select, input').forEach(input => {
                        input.required = false;
                    });
                }
            });
        });
    }
    
    // For Edit Client form
    const editClientTypeSelect = document.getElementById('editClientType');
    if (editClientTypeSelect) {
        editClientTypeSelect.addEventListener('change', function() {
            const citizenFields = document.querySelectorAll('#editClientModal .citizen-only');
            citizenFields.forEach(field => {
                if (this.value === 'citizen') {
                    field.style.display = 'flex';
                    // Make fields required
                    field.querySelectorAll('select, input').forEach(input => {
                        input.required = true;
                    });
                } else {
                    field.style.display = 'none';
                    // Make fields not required
                    field.querySelectorAll('select, input').forEach(input => {
                        input.required = false;
                    });
                }
            });
        });
    }
    
    // View Client Modal
    const viewClientModal = document.getElementById('viewClientModal');
    if (viewClientModal) {
        viewClientModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const clientId = button.getAttribute('data-client-id');
            const clientName = button.getAttribute('data-client-name');
            const clientType = button.getAttribute('data-client-type');
            const clientTypeLabel = button.getAttribute('data-client-type-label');
            const clientUniqueId = button.getAttribute('data-client-unique-id');
            const clientSex = button.getAttribute('data-client-sex');
            const clientAge = button.getAttribute('data-client-age');
            const clientRegion = button.getAttribute('data-client-region');
            const clientContact = button.getAttribute('data-client-contact');
            const clientEmail = button.getAttribute('data-client-email');
            const clientAddress = button.getAttribute('data-client-address');
            
            // Set the client details in the modal
            document.getElementById('viewClientName').textContent = clientName;
            document.getElementById('viewClientType').textContent = clientTypeLabel;
            document.getElementById('viewClientUniqueId').textContent = clientUniqueId;
            document.getElementById('viewClientSex').textContent = clientSex || 'N/A';
            document.getElementById('viewClientAge').textContent = clientAge || 'N/A';
            document.getElementById('viewClientRegion').textContent = clientRegion;
            document.getElementById('viewClientContact').textContent = clientContact;
            document.getElementById('viewClientEmail').textContent = clientEmail;
            document.getElementById('viewClientAddress').textContent = clientAddress || 'N/A';
            
            // Show/hide citizen-specific fields
            const citizenFields = document.querySelectorAll('#viewClientModal .citizen-only');
            citizenFields.forEach(field => {
                field.style.display = clientType === 'citizen' ? 'flex' : 'none';
            });
            
            // Set up the edit button to pass the client ID
            document.getElementById('viewToEditBtn').setAttribute('data-client-id', clientId);
            document.getElementById('viewToEditBtn').setAttribute('data-bs-target', '#editClientModal');
            
            // Load certificates via AJAX
            loadClientCertificates(clientId);
            
            // Load feedback via AJAX
            loadClientFeedback(clientId);
        });
    }
    
    // Function to load client certificates
    function loadClientCertificates(clientId) {
        // Placeholder for AJAX call
        // In a real implementation, you would make an AJAX call to fetch certificates
        setTimeout(() => {
            const certificatesContainer = document.getElementById('viewClientCertificates');
            
            // Sample data - replace with actual AJAX response
            const certificates = [
                { id: 1, name: 'Business Permit', date: '2023-01-15' },
                { id: 2, name: 'Tax Clearance', date: '2023-02-20' },
                { id: 3, name: 'DTI Registration', date: '2023-03-10' }
            ];
            
            if (certificates.length > 0) {
                let html = '';
                certificates.forEach(cert => {
                    html += `
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-certificate me-2 text-primary"></i>
                            ${cert.name}
                            <small class="text-muted d-block">Issued: ${cert.date}</small>
                        </div>
                        <button class="btn btn-sm btn-primary-custom" onclick="downloadCertificate(${cert.id})">
                            <i class="fas fa-download me-1"></i> Download
                        </button>
                    </a>`;
                });
                certificatesContainer.innerHTML = html;
            } else {
                certificatesContainer.innerHTML = '<div class="text-center py-3"><p class="text-muted">No certificates found for this client.</p></div>';
            }
        }, 1000); // Simulating network delay
    }
    
    // Function to load client feedback
    function loadClientFeedback(clientId) {
        // Placeholder for AJAX call
        // In a real implementation, you would make an AJAX call to fetch feedback
        setTimeout(() => {
            const feedbackContainer = document.getElementById('viewClientFeedback');
            const feedbackDateElement = document.getElementById('viewClientFeedbackDate');
            
            // Sample data - replace with actual AJAX response
            const feedback = {
                date: 'June 15, 2023',
                items: [
                    { question: '1. SERVICE: Reliability and Outcome', answer: 'Very Satisfied' },
                    { question: '2. SERVICE: Access and Facilities', answer: 'Satisfied' },
                    { question: '3. RESOURCE SPEAKER: Reliability, Communication and Quality', answer: 'Very Satisfied' },
                    { question: '4. RESOURCE SPEAKER: Responsiveness and Integrity', answer: 'Satisfied' },
                    { question: '5. MODERATOR: Reliability and Responsiveness', answer: 'Very Satisfied' },
                    { question: '6. HOST/SECRETARIAT: Reliability and Responsiveness', answer: 'Satisfied' },
                    { question: '7. OVERALL SATISFACTION RATING', answer: 'Very Satisfied' },
                    { 
                        question: '8. Comments/suggestions to help us improve our service/s:',
                        answer: 'The service was excellent overall. The staff were very helpful and knowledgeable. I would recommend improving the waiting area facilities.'
                    }
                ]
            };
            
            if (feedback) {
                feedbackDateElement.textContent = `Last feedback submitted on ${feedback.date}`;
                
                let html = '';
                feedback.items.forEach(item => {
                    html += `
                    <div class="feedback-item mb-3 pb-3" style="border-bottom: 1px solid #e9ecef;">
                        <div class="feedback-question fw-bold mb-1">${item.question}</div>
                        <div class="client-detail-value">${item.answer}</div>
                    </div>`;
                });
                feedbackContainer.innerHTML = html;
            } else {
                feedbackDateElement.textContent = 'No feedback available';
                feedbackContainer.innerHTML = '<div class="text-center py-3"><p class="text-muted">This client has not submitted any feedback yet.</p></div>';
            }
        }, 1000); // Simulating network delay
    }
    
    // Function to download certificate (placeholder)
    function downloadCertificate(certId) {
        alert(`Downloading certificate ID: ${certId}`);
        // In a real implementation, this would trigger a download
    }
    
    // Edit Client Modal
    const editClientModal = document.getElementById('editClientModal');
    if (editClientModal) {
        editClientModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const clientId = button.getAttribute('data-client-id');
            const clientName = button.getAttribute('data-client-name');
            const clientType = button.getAttribute('data-client-type');
            const clientSex = button.getAttribute('data-client-sex');
            const clientAge = button.getAttribute('data-client-age');
            const clientRegion = button.getAttribute('data-client-region');
            const clientContact = button.getAttribute('data-client-contact');
            const clientEmail = button.getAttribute('data-client-email');
            const clientAddress = button.getAttribute('data-client-address');
            
            // Set the client details in the form
            document.getElementById('editClientId').value = clientId;
            document.getElementById('editClientName').value = clientName;
            document.getElementById('editClientType').value = clientType;
            
            if (clientType === 'citizen') {
                document.getElementById('editClientSex').value = clientSex || '';
                document.getElementById('editClientAge').value = clientAge || '';
                
                // Show citizen-specific fields
                document.querySelectorAll('#editClientModal .citizen-only').forEach(field => {
                    field.style.display = 'flex';
                });
            } else {
                // Hide citizen-specific fields
                document.querySelectorAll('#editClientModal .citizen-only').forEach(field => {
                    field.style.display = 'none';
                });
            }
            
            document.getElementById('editClientRegion').value = clientRegion;
            document.getElementById('editClientContact').value = clientContact;
            document.getElementById('editClientEmail').value = clientEmail;
            document.getElementById('editClientAddress').value = clientAddress || '';
        });
    }
    
    // Delete Client Modal
    const deleteClientModal = document.getElementById('deleteClientModal');
    if (deleteClientModal) {
        deleteClientModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const clientId = button.getAttribute('data-client-id');
            const clientName = button.getAttribute('data-client-name');
            
            document.getElementById('deleteClientId').value = clientId;
            document.getElementById('deleteClientName').textContent = clientName;
        });
    }
    
    // Handle the view-to-edit button click
    const viewToEditBtn = document.getElementById('viewToEditBtn');
    if (viewToEditBtn) {
        viewToEditBtn.addEventListener('click', function() {
            const clientId = this.getAttribute('data-client-id');
            
            // Close the view modal
            const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewClientModal'));
            viewModal.hide();
            
            // Find the edit button with matching client ID and trigger it
            setTimeout(() => {
                const editButtons = document.querySelectorAll('.btn-warning-custom[data-client-id="' + clientId + '"]');
                if (editButtons.length > 0) {
                    editButtons[0].click();
                }
            }, 500); // Give time for the view modal to close
        });
    }
    
    // Initially hide citizen-specific fields in add form
    document.querySelectorAll('#addClientModal .citizen-only').forEach(field => {
        field.style.display = 'none';
    });
});