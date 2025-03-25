// JavaScript for certificate management
document.addEventListener('DOMContentLoaded', function() {
    // File preview for add certificate
    document.getElementById('certFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewPlaceholder = document.getElementById('previewPlaceholder');
        const certPreview = document.getElementById('certPreview');
        
        if (file) {
            const fileType = file.type;
            if (fileType.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    certPreview.src = e.target.result;
                    certPreview.style.display = 'block';
                    previewPlaceholder.style.display = 'none';
                }
                reader.readAsDataURL(file);
            } else if (fileType === 'application/pdf') {
                // Show PDF icon for PDF files
                previewPlaceholder.innerHTML = '<i class="fas fa-file-pdf fa-4x text-danger mb-2"></i><p>PDF Document</p>';
                previewPlaceholder.style.display = 'block';
                certPreview.style.display = 'none';
            }
        }
    });
    
    // View certificate modal
    const viewCertificateModal = document.getElementById('viewCertificateModal');
    if (viewCertificateModal) {
        viewCertificateModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const certId = button.getAttribute('data-cert-id');
            const certName = button.getAttribute('data-cert-name');
            const certDate = button.getAttribute('data-cert-date');
            const certType = button.getAttribute('data-cert-type');
            const certDesc = button.getAttribute('data-cert-desc');
            const certFile = button.getAttribute('data-cert-file');
            
            // Update modal content
            document.getElementById('viewCertName').textContent = certName;
            document.getElementById('viewUploadDate').textContent = 'Uploaded on ' + certDate;
            document.getElementById('viewCertType').textContent = certType;
            document.getElementById('viewCertDesc').textContent = certDesc;
            
            // Set download link
            const downloadBtn = document.getElementById('downloadCertBtn');
            downloadBtn.href = certFile;
            downloadBtn.setAttribute('download', certName);
            
            // Set print link
            document.getElementById('printCertBtn').onclick = function() {
                window.open(certFile, '_blank').print();
                return false;
            };
            
            // Display image or PDF viewer
            const viewCertificateImage = document.getElementById('viewCertificateImage');
            const fileExt = certFile.split('.').pop().toLowerCase();
            
            if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
                viewCertificateImage.src = certFile;
                viewCertificateImage.style.display = 'block';
            } else if (fileExt === 'pdf') {
                // Create PDF embed if it's a PDF
                viewCertificateImage.style.display = 'none';
                const pdfContainer = viewCertificateImage.parentElement;
                
                // Remove existing PDF viewer if any
                const existingEmbed = pdfContainer.querySelector('embed');
                if (existingEmbed) {
                    pdfContainer.removeChild(existingEmbed);
                }
                
                // Create new PDF viewer
                const pdfEmbed = document.createElement('embed');
                pdfEmbed.src = certFile;
                pdfEmbed.type = 'application/pdf';
                pdfEmbed.style.width = '100%';
                pdfEmbed.style.height = '400px';
                pdfEmbed.className = 'mb-3';
                
                // Insert before the buttons
                pdfContainer.insertBefore(pdfEmbed, pdfContainer.querySelector('.mt-3'));
            }
            
            // Set up edit button
            document.querySelector('.edit-from-view').onclick = function() {
                // Hide this modal and show edit modal
                const viewModal = bootstrap.Modal.getInstance(viewCertificateModal);
                viewModal.hide();
                
                // Trigger edit modal with same data
                setTimeout(() => {
                    const editButton = document.querySelector(`button[data-bs-target="#editCertificateModal"][data-cert-id="${certId}"]`);
                    if (editButton) {
                        editButton.click();
                    }
                }, 500);
            };
        });
    }
    
    // Edit certificate modal
    const editCertificateModal = document.getElementById('editCertificateModal');
    if (editCertificateModal) {
        editCertificateModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const certId = button.getAttribute('data-cert-id');
            const certName = button.getAttribute('data-cert-name');
            const certDesc = button.getAttribute('data-cert-desc');
            const certFile = button.getAttribute('data-cert-file');
            
            // Update form fields
            document.getElementById('editCertId').value = certId;
            document.getElementById('editCertName').value = certName;
            document.getElementById('editCertDesc').value = certDesc;
            
            // Show current certificate preview
            const editCertPreview = document.getElementById('editCertPreview');
            const fileExt = certFile.split('.').pop().toLowerCase();
            
            if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
                editCertPreview.src = certFile;
                editCertPreview.style.display = 'block';
            } else if (fileExt === 'pdf') {
                // For PDF files, show a PDF icon instead
                editCertPreview.style.display = 'none';
                const previewContainer = editCertPreview.parentElement;
                previewContainer.innerHTML = `
                    <div class="text-center">
                        <i class="fas fa-file-pdf fa-4x text-danger mb-2"></i>
                        <p>PDF Document</p>
                        <a href="${certFile}" target="_blank" class="btn btn-sm btn-primary-custom">
                            <i class="fas fa-eye me-1"></i> View PDF
                        </a>
                    </div>
                `;
            }
        });
    }
    
    // Delete certificate modal
    const deleteCertificateModal = document.getElementById('deleteCertificateModal');
    if (deleteCertificateModal) {
        deleteCertificateModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const certId = button.getAttribute('data-cert-id');
            const certName = button.getAttribute('data-cert-name');
            
            // Update modal content
            document.getElementById('deleteCertId').value = certId;
            document.getElementById('deleteCertName').textContent = certName;
        });
    }
    
    // Search functionality
    const searchInput = document.getElementById('searchCertificate');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const certificateItems = document.querySelectorAll('.certificate-item');
            
            certificateItems.forEach(item => {
                const certificateName = item.querySelector('.card-title').textContent.toLowerCase();
                const certificateDesc = item.querySelector('.card-text').textContent.toLowerCase();
                
                if (certificateName.includes(searchTerm) || certificateDesc.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // File preview for edit certificate
    const editCertFile = document.getElementById('editCertFile');
    if (editCertFile) {
        editCertFile.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileType = file.type;
                const previewContainer = document.getElementById('editCertPreview').parentElement;
                
                if (fileType.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.innerHTML = `<img src="${e.target.result}" alt="New Certificate" class="img-fluid mx-auto" style="max-height: 180px;">`;
                    }
                    reader.readAsDataURL(file);
                } else if (fileType === 'application/pdf') {
                    previewContainer.innerHTML = `
                        <div class="text-center">
                            <i class="fas fa-file-pdf fa-4x text-danger mb-2"></i>
                            <p>New PDF Document</p>
                        </div>
                    `;
                }
            }
        });
    }
});
