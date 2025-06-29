// Document ready function
$(document).ready(function() {
    // Show/hide citizen-specific fields based on client type
    $('#clientType').on('change', function() {
        if ($(this).val() === 'citizen') {
            $('.citizen-only').show();
        } else {
            $('.citizen-only').hide();
        }
    });
    
    $('#editClientType').on('change', function() {
        if ($(this).val() === 'citizen') {
            $('.edit-citizen-only').show();
        } else {
            $('.edit-citizen-only').hide();
        }
    });
    
    // Link view to edit button
    $('#viewToEditBtn').on('click', function() {
        const clientId = $('#viewClientModal').data('client-id');
        $('#viewClientModal').modal('hide');
        editClient(clientId);
    });
});

 // Handle view client modal
    $('#viewClientModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const clientId = button.data('client-id');
        const clientName = button.data('client-name');
        const clientType = button.data('client-type');
        const clientTypeLabel = button.data('client-type-label');
        const clientUniqueId = button.data('client-unique-id');
        const clientRegion = button.data('client-region');
        const clientContact = button.data('client-contact');
        const clientEmail = button.data('client-email');
        const clientAddress = button.data('client-address');
        
        // Fill in basic client info
        $('#viewClientName').text(clientName);
        $('#viewClientType').text(clientTypeLabel);
        $('#viewClientUniqueId').text(clientUniqueId);
        $('#viewClientRegion').text(clientRegion);
        $('#viewClientContact').text(clientContact);
        $('#viewClientEmail').text(clientEmail);
        $('#viewClientAddress').text(clientAddress);
        
        
      
        
    });

// Function to view client details
function viewClient(clientId) {
    // Show loading state
    $('#viewClientName').text('Loading...');
    $('#viewClientType').text('Loading...');
    $('#viewClientReferenceId').text('Loading...');
    $('#viewClientSex').text('Loading...');
    $('#viewClientAge').text('Loading...');
    $('#viewClientRegion').text('Loading...');
    $('#viewClientContact').text('Loading...');
    $('#viewClientEmail').text('Loading...');
    $('#viewClientType').text('Loading...');
    
    // Clear feedback sections
    $('#viewClientFeedback').html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading feedback data...</p></div>');
    
    // Store client ID in modal for reference
    $('#viewClientModal').data('client-id', clientId);
    
    // Fetch client data
    $.ajax({
        url: 'view_client.php',
        type: 'GET',
        data: { id: clientId },
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                alert('Error: ' + response.error);
                return;
            }
            
            // Fill in basic client info
            $('#viewClientName').text(response.client.client_name);
            $('#viewClientType').text(getClientTypeLabel(response.client.client_type));
            $('#viewClientReferenceId').text(response.client.reference_id);
            $('#viewClientSex').text(response.client.sex || 'N/A');
            $('#viewClientAge').text(response.client.age || 'N/A');
            $('#viewClientRegion').text(response.client.region);
            $('#viewClientContact').text(response.client.contact);
            $('#viewClientEmail').text(response.client.email);

            
            // Show/hide citizen-specific fields
            if (response.client.client_type === 'citizen') {
                $('.citizen-only').show();
            } else {
                $('.citizen-only').hide();
            }
            
            // Build feedback HTML
            let feedbackHtml = buildFeedbackHtml(response.feedback);
            $('#viewClientFeedback').html(feedbackHtml);
            
            // Set completion date
            if (response.client.completion_date) {
                $('#viewClientFeedbackDate').text('Feedback submitted on ' + formatDate(response.client.completion_date));
            } else {
                $('#viewClientFeedbackDate').text('No feedback submitted yet');
            }
            
            // Show the modal
            $('#viewClientModal').modal('show');
        },
        error: function(xhr, status, error) {
            alert('Error fetching client data: ' + error);
        }
    });
}

// Function to edit client
function editClient(clientId) {
    // Fetch client data
    $.ajax({
        url: 'view_client.php',
        type: 'GET',
        data: { id: clientId },
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                alert('Error: ' + response.error);
                return;
            }
            
            // Fill in form fields
            $('#editClientId').val(response.client.id);
            $('#editClientName').val(response.client.client_name);
            $('#editClientType').val(response.client.client_type);
            $('#editClientSex').val(response.client.sex);
            $('#editClientAge').val(response.client.age);
            $('#editClientRegion').val(response.client.region);
            $('#editClientContact').val(response.client.contact);
            $('#editClientEmail').val(response.client.email);
            
            // Show/hide citizen-specific fields
            if (response.client.client_type === 'citizen') {
                $('.edit-citizen-only').show();
            } else {
                $('.edit-citizen-only').hide();
            }
            
            // Show the modal
            $('#editClientModal').modal('show');
        },
        error: function(xhr, status, error) {
            alert('Error fetching client data: ' + error);
        }
    });
}

// Function to delete client
function deleteClient(clientId, clientName) {
    $('#deleteClientId').val(clientId);
    $('#deleteClientName').text(clientName);
    $('#deleteClientModal').modal('show');
}

// Helper function to build feedback HTML
function buildFeedbackHtml(feedback) {
    // Check if any feedback data exists
    let hasFeedback = false;
    for (const category in feedback) {
        if (typeof feedback[category] === 'object') {
            for (const item in feedback[category]) {
                if (feedback[category][item] && typeof feedback[category][item] === 'string' && feedback[category][item].trim() !== '') {
                    hasFeedback = true;
                    break;
                }
                if (typeof feedback[category][item] === 'object') {
                    for (const subItem in feedback[category][item]) {
                        if (feedback[category][item][subItem] && feedback[category][item][subItem].trim() !== '') {
                            hasFeedback = true;
                            break;
                        }
                    }
                }
            }
            if (hasFeedback) break;
        }
    }
    
    if (!hasFeedback) {
        return '<div class="alert alert-info">No feedback data available for this client.</div>';
    }
    
    let html = '<div class="feedback-sections">';
    
    // Service Rating & Objectives
    if (feedback.service_rating_objectives) {
        html += '<div class="feedback-section">';
        html += '<h5 class="feedback-section-title">Service Rating & Objectives</h5>';
        
        if (feedback.service_rating_objectives.objectives_achieved) {
            html += '<div class="feedback-item"><span class="feedback-label">Objectives Achieved:</span> <span class="feedback-value">' + feedback.service_rating_objectives.objectives_achieved + '</span></div>';
        }
        
        if (feedback.service_rating_objectives.info_received) {
            html += '<div class="feedback-item"><span class="feedback-label">Information Received:</span> <span class="feedback-value">' + feedback.service_rating_objectives.info_received + '</span></div>';
        }
        
        if (feedback.service_rating_objectives.relevance_value) {
            html += '<div class="feedback-item"><span class="feedback-label">Relevance & Value:</span> <span class="feedback-value">' + feedback.service_rating_objectives.relevance_value + '</span></div>';
        }
        
        if (feedback.service_rating_objectives.duration_sufficient) {
            html += '<div class="feedback-item"><span class="feedback-label">Duration Sufficient:</span> <span class="feedback-value">' + feedback.service_rating_objectives.duration_sufficient + '</span></div>';
        }
        
        html += '</div>';
    }
    
    // Service Access & Functionality
    if (feedback.service_access_functionality) {
        html += '<div class="feedback-section">';
        html += '<h5 class="feedback-section-title">Service Access & Functionality</h5>';
        
        if (feedback.service_access_functionality.sign_up_access) {
            html += '<div class="feedback-item"><span class="feedback-label">Sign-up & Access:</span> <span class="feedback-value">' + feedback.service_access_functionality.sign_up_access + '</span></div>';
        }
        
        if (feedback.service_access_functionality.audio_video_sync) {
            html += '<div class="feedback-item"><span class="feedback-label">Audio/Video Sync:</span> <span class="feedback-value">' + feedback.service_access_functionality.audio_video_sync + '</span></div>';
        }
        
        html += '</div>';
    }
    
    // Resource Speaker
    if (feedback.resource_speaker) {
        html += '<div class="feedback-section">';
        html += '<h5 class="feedback-section-title">Resource Speaker</h5>';
        
        if (feedback.resource_speaker.quality) {
            html += '<h6 class="feedback-subsection-title">Quality</h6>';
            
            if (feedback.resource_speaker.quality.knowledge) {
                html += '<div class="feedback-item"><span class="feedback-label">Knowledge:</span> <span class="feedback-value">' + feedback.resource_speaker.quality.knowledge + '</span></div>';
            }
            
            if (feedback.resource_speaker.quality.clarity) {
                html += '<div class="feedback-item"><span class="feedback-label">Clarity:</span> <span class="feedback-value">' + feedback.resource_speaker.quality.clarity + '</span></div>';
            }
            
            if (feedback.resource_speaker.quality.engagement) {
                html += '<div class="feedback-item"><span class="feedback-label">Engagement:</span> <span class="feedback-value">' + feedback.resource_speaker.quality.engagement + '</span></div>';
            }
            
            if (feedback.resource_speaker.quality.visual_relevance) {
                html += '<div class="feedback-item"><span class="feedback-label">Visual Relevance:</span> <span class="feedback-value">' + feedback.resource_speaker.quality.visual_relevance + '</span></div>';
            }
        }
        
        if (feedback.resource_speaker.interaction) {
            html += '<h6 class="feedback-subsection-title">Interaction</h6>';
            
            if (feedback.resource_speaker.interaction.answer_questions) {
                html += '<div class="feedback-item"><span class="feedback-label">Answer Questions:</span> <span class="feedback-value">' + feedback.resource_speaker.interaction.answer_questions + '</span></div>';
            }
            
            if (feedback.resource_speaker.interaction.chat_responsiveness) {
                html += '<div class="feedback-item"><span class="feedback-label">Chat Responsiveness:</span> <span class="feedback-value">' + feedback.resource_speaker.interaction.chat_responsiveness + '</span></div>';
            }
        }
        
        html += '</div>';
    }
    
    // Moderator
    if (feedback.moderator) {
        html += '<div class="feedback-section">';
        html += '<h5 class="feedback-section-title">Moderator</h5>';
        
        if (feedback.moderator.manage_discussion) {
            html += '<div class="feedback-item"><span class="feedback-label">Manage Discussion:</span> <span class="feedback-value">' + feedback.moderator.manage_discussion + '</span></div>';
        }
        
        if (feedback.moderator.monitor_raises_questions) {
            html += '<div class="feedback-item"><span class="feedback-label">Monitor & Raise Questions:</span> <span class="feedback-value">' + feedback.moderator.monitor_raises_questions + '</span></div>';
        }
        
        if (feedback.moderator.manage_program) {
            html += '<div class="feedback-item"><span class="feedback-label">Manage Program:</span> <span class="feedback-value">' + feedback.moderator.manage_program + '</span></div>';
        }
        
        html += '</div>';
    }
    
    // Host/Secretariat
    if (feedback.host_secretariat) {
        html += '<div class="feedback-section">';
        html += '<h5 class="feedback-section-title">Host/Secretariat</h5>';
        
        if (feedback.host_secretariat.technical_assistance) {
            html += '<div class="feedback-item"><span class="feedback-label">Technical Assistance:</span> <span class="feedback-value">' + feedback.host_secretariat.technical_assistance + '</span></div>';
        }
        
        if (feedback.host_secretariat.admittance_management) {
            html += '<div class="feedback-item"><span class="feedback-label">Admittance Management:</span> <span class="feedback-value">' + feedback.host_secretariat.admittance_management + '</span></div>';
        }
        
        html += '</div>';
    }
    
    // Overall Satisfaction
    if (feedback.overall) {
        html += '<div class="feedback-section">';
        html += '<h5 class="feedback-section-title">Overall Satisfaction</h5>';
        
        if (feedback.overall.satisfaction_rating) {
            html += '<div class="feedback-item"><span class="feedback-label">Rating:</span> <span class="feedback-value">' + feedback.overall.satisfaction_rating + '</span></div>';
        }
        
        if (feedback.overall.dissatisfied_reasons) {
            html += '<div class="feedback-item"><span class="feedback-label">Dissatisfied Reasons:</span> <span class="feedback-value">' + feedback.overall.dissatisfied_reasons + '</span></div>';
        }
        
        if (feedback.overall.improvement_suggestions) {
            html += '<div class="feedback-item"><span class="feedback-label">Improvement Suggestions:</span> <span class="feedback-value">' + feedback.overall.improvement_suggestions + '</span></div>';
        }
        
        html += '</div>';
    }
    
    html += '</div>'; // Close feedback-sections
    
    return html;
}

// Helper function to get client type label
function getClientTypeLabel(type) {
    switch(type) {
        case 'citizen':
            return 'Citizen';
        case 'business':
            return 'Business';
        case 'government':
            return 'Government';
        default:
            return type;
    }
}

// Helper function to format date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Helper function to build feedback HTML
function buildFeedbackHtml(feedback) {
    // Check if any feedback data exists
    let hasFeedback = false;
    for (const category in feedback) {
        if (typeof feedback[category] === 'object') {
            for (const item in feedback[category]) {
                if (feedback[category][item] && typeof feedback[category][item] === 'string' && feedback[category][item].trim() !== '') {
                    hasFeedback = true;
                    break;
                }
                if (typeof feedback[category][item] === 'object') {
                    for (const subItem in feedback[category][item]) {
                        if (feedback[category][item][subItem] && feedback[category][item][subItem].trim() !== '') {
                            hasFeedback = true;
                            break;
                        }
                    }
                }
            }
            if (hasFeedback) break;
        }
    }
    
    if (!hasFeedback) {
        return '<div class="alert alert-info">No feedback data available for this client.</div>';
    }
    
    // Start with a container that uses Bootstrap's row and column system
    let html = '<div class="row feedback-container">';
    
    // Left column
    html += '<div class="col-md-6 feedback-column">';
    
    // Service Rating & Objectives (Left column)
    if (feedback.service_rating_objectives) {
        html += '<div class="feedback-section">';
        html += '<h5 class="feedback-section-title">Service Rating & Objectives</h5>';
        
        if (feedback.service_rating_objectives.objectives_achieved) {
            html += '<div class="feedback-item"><span class="feedback-label">Objectives Achieved:</span> <span class="feedback-value">' + feedback.service_rating_objectives.objectives_achieved + '</span></div>';
        }
        
        if (feedback.service_rating_objectives.info_received) {
            html += '<div class="feedback-item"><span class="feedback-label">Information Received:</span> <span class="feedback-value">' + feedback.service_rating_objectives.info_received + '</span></div>';
        }
        
        if (feedback.service_rating_objectives.relevance_value) {
            html += '<div class="feedback-item"><span class="feedback-label">Relevance & Value:</span> <span class="feedback-value">' + feedback.service_rating_objectives.relevance_value + '</span></div>';
        }
        
        if (feedback.service_rating_objectives.duration_sufficient) {
            html += '<div class="feedback-item"><span class="feedback-label">Duration Sufficient:</span> <span class="feedback-value">' + feedback.service_rating_objectives.duration_sufficient + '</span></div>';
        }
        
        html += '</div>';
    }
    
    // Service Access & Functionality (Left column)
    if (feedback.service_access_functionality) {
        html += '<div class="feedback-section">';
        html += '<h5 class="feedback-section-title">Service Access & Functionality</h5>';
        
        if (feedback.service_access_functionality.sign_up_access) {
            html += '<div class="feedback-item"><span class="feedback-label">Sign-up & Access:</span> <span class="feedback-value">' + feedback.service_access_functionality.sign_up_access + '</span></div>';
        }
        
        if (feedback.service_access_functionality.audio_video_sync) {
            html += '<div class="feedback-item"><span class="feedback-label">Audio/Video Sync:</span> <span class="feedback-value">' + feedback.service_access_functionality.audio_video_sync + '</span></div>';
        }
        
        html += '</div>';
    }
    
    // Resource Speaker (Left column)
    if (feedback.resource_speaker) {
        html += '<div class="feedback-section">';
        html += '<h5 class="feedback-section-title">Resource Speaker</h5>';
        
        if (feedback.resource_speaker.quality) {
            html += '<h6 class="feedback-subsection-title">Quality</h6>';
            
            if (feedback.resource_speaker.quality.knowledge) {
                html += '<div class="feedback-item"><span class="feedback-label">Knowledge:</span> <span class="feedback-value">' + feedback.resource_speaker.quality.knowledge + '</span></div>';
            }
            
            if (feedback.resource_speaker.quality.clarity) {
                html += '<div class="feedback-item"><span class="feedback-label">Clarity:</span> <span class="feedback-value">' + feedback.resource_speaker.quality.clarity + '</span></div>';
            }
            
            if (feedback.resource_speaker.quality.engagement) {
                html += '<div class="feedback-item"><span class="feedback-label">Engagement:</span> <span class="feedback-value">' + feedback.resource_speaker.quality.engagement + '</span></div>';
            }
            
            if (feedback.resource_speaker.quality.visual_relevance) {
                html += '<div class="feedback-item"><span class="feedback-label">Visual Relevance:</span> <span class="feedback-value">' + feedback.resource_speaker.quality.visual_relevance + '</span></div>';
            }
        }
        
        if (feedback.resource_speaker.interaction) {
            html += '<h6 class="feedback-subsection-title">Interaction</h6>';
            
            if (feedback.resource_speaker.interaction.answer_questions) {
                html += '<div class="feedback-item"><span class="feedback-label">Answer Questions:</span> <span class="feedback-value">' + feedback.resource_speaker.interaction.answer_questions + '</span></div>';
            }
            
            if (feedback.resource_speaker.interaction.chat_responsiveness) {
                html += '<div class="feedback-item"><span class="feedback-label">Chat Responsiveness:</span> <span class="feedback-value">' + feedback.resource_speaker.interaction.chat_responsiveness + '</span></div>';
            }
        }
        
        html += '</div>';
    }
    
    // Close left column
    html += '</div>';
    
    // Right column
    html += '<div class="col-md-6 feedback-column">';
    
    // Moderator (Right column)
    if (feedback.moderator) {
        html += '<div class="feedback-section">';
        html += '<h5 class="feedback-section-title">Moderator</h5>';
        
        if (feedback.moderator.manage_discussion) {
            html += '<div class="feedback-item"><span class="feedback-label">Manage Discussion:</span> <span class="feedback-value">' + feedback.moderator.manage_discussion + '</span></div>';
        }
        
        if (feedback.moderator.monitor_raises_questions) {
            html += '<div class="feedback-item"><span class="feedback-label">Monitor & Raise Questions:</span> <span class="feedback-value">' + feedback.moderator.monitor_raises_questions + '</span></div>';
        }
        
        if (feedback.moderator.manage_program) {
            html += '<div class="feedback-item"><span class="feedback-label">Manage Program:</span> <span class="feedback-value">' + feedback.moderator.manage_program + '</span></div>';
        }
        
        html += '</div>';
    }
    
    // Host/Secretariat (Right column)
    if (feedback.host_secretariat) {
        html += '<div class="feedback-section">';
        html += '<h5 class="feedback-section-title">Host/Secretariat</h5>';
        
        if (feedback.host_secretariat.technical_assistance) {
            html += '<div class="feedback-item"><span class="feedback-label">Technical Assistance:</span> <span class="feedback-value">' + feedback.host_secretariat.technical_assistance + '</span></div>';
        }
        
        if (feedback.host_secretariat.admittance_management) {
            html += '<div class="feedback-item"><span class="feedback-label">Admittance Management:</span> <span class="feedback-value">' + feedback.host_secretariat.admittance_management + '</span></div>';
        }
        
        html += '</div>';
    }
    
    // Overall Satisfaction (Right column)
    if (feedback.overall) {
        html += '<div class="feedback-section">';
        html += '<h5 class="feedback-section-title">Overall Satisfaction</h5>';
        
        if (feedback.overall.satisfaction_rating) {
            html += '<div class="feedback-item"><span class="feedback-label">Rating:</span> <span class="feedback-value">' + feedback.overall.satisfaction_rating + '</span></div>';
        }
        
        if (feedback.overall.dissatisfied_reasons) {
            html += '<div class="feedback-item"><span class="feedback-label">Dissatisfied Reasons:</span> <span class="feedback-value">' + feedback.overall.dissatisfied_reasons + '</span></div>';
        }
        
        if (feedback.overall.improvement_suggestions) {
            html += '<div class="feedback-item"><span class="feedback-label">Improvement Suggestions:</span> <span class="feedback-value">' + feedback.overall.improvement_suggestions + '</span></div>';
        }
        
        html += '</div>';
    }
    
    // Close right column
    html += '</div>';
    
    // Close row container
    html += '</div>';
    
    return html;  
}
    