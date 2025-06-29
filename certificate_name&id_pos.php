    <?php
    session_start();
    include('dbcon.php');

    // Set page-specific variables
    $pageTitle = "DTI Certificate Text Settings";
    $currentPage = "Certificate Text Settings";

    // Get certificate ID from URL parameter
    $cert_id = isset($_GET['cert_id']) ? intval($_GET['cert_id']) : 0;

    // Fetch certificate information
    if ($cert_id) {
        $certQuery = "SELECT * FROM certificates WHERE id = ?";
        $stmt = $conn->prepare($certQuery);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $cert_id);
        $stmt->execute();
        $certResult = $stmt->get_result();
        $certificate = $certResult->fetch_assoc();
        
        if (!$certificate) {
            $errorMessage = "Certificate not found.";
            $cert_id = 0;
        }
    }

    // Default settings
    $defaultSettings = [
        'ref_id_x' => 20, 'ref_id_y' => 12, 'ref_id_size' => 13, 'ref_id_color' => '0,0,0',
        'client_name_x' => 'center', 'client_name_y' => 'middle', 'client_name_size' => 25, 'client_name_color' => '38,61,128',
        'background_image' => '' // Default background image (empty)
    ];

    // Load settings from database if available
    $settings = $defaultSettings;
    if ($cert_id && !empty($certificate['positioning_data'])) {
        $savedSettings = json_decode($certificate['positioning_data'], true);
        if ($savedSettings) {
            $settings = array_merge($defaultSettings, $savedSettings);
        }
    }

    // Make sure this line is correctly preserving the background image path
    if ($cert_id && !empty($certificate['file_path']) && file_exists($certificate['file_path'])) {
        $settings['background_image'] = $certificate['file_path'];
        // Add debugging
        error_log("Loading certificate image: " . $certificate['file_path'] . " (exists: " . (file_exists($certificate['file_path']) ? 'yes' : 'no') . ")");
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $cert_id) {
        if (isset($_POST['reset_defaults'])) {
            // Reset to defaults with prepared statement
            $updateQuery = "UPDATE certificates SET positioning_data = NULL WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("i", $cert_id);
            $stmt->execute();
            $settings = $defaultSettings;
            $successMessage = "Settings reset to defaults successfully!";
            
            // Check if we should redirect after reset
            if (isset($_POST['redirect_after_save'])) {
                header("Location: certificate_management.php");
                exit;
            }
        } else {
            // Process form data
            $newSettings = [
                'ref_id_x' => isset($_POST['ref_id_x']) ? floatval($_POST['ref_id_x']) : $defaultSettings['ref_id_x'],
                'ref_id_y' => isset($_POST['ref_id_y']) ? floatval($_POST['ref_id_y']) : $defaultSettings['ref_id_y'],
                'ref_id_size' => isset($_POST['ref_id_size']) ? intval($_POST['ref_id_size']) : $defaultSettings['ref_id_size'],
                'ref_id_color' => isset($_POST['ref_id_color']) ? $_POST['ref_id_color'] : $defaultSettings['ref_id_color'],
                'client_name_size' => isset($_POST['client_name_size']) ? intval($_POST['client_name_size']) : $defaultSettings['client_name_size'],
                'client_name_color' => isset($_POST['client_name_color']) ? $_POST['client_name_color'] : $defaultSettings['client_name_color'],
                'client_name_x' => isset($_POST['center_x']) && $_POST['center_x'] === 'on' ? 'center' : (isset($_POST['client_name_x']) ? floatval($_POST['client_name_x']) : $defaultSettings['client_name_x']),
                // Always use the Y position value from the form, no centering logic
                'client_name_y' => isset($_POST['client_name_y']) ? floatval($_POST['client_name_y']) : $defaultSettings['client_name_y'],
            ];
            
            // Save settings to database with prepared statement
            $positioningData = json_encode($newSettings);
            $updateQuery = "UPDATE certificates SET positioning_data = ? WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            
            if ($stmt) {
                $stmt->bind_param("si", $positioningData, $cert_id);
                if ($stmt->execute()) {
                    $settings = $newSettings;
                    $successMessage = "Certificate settings saved successfully!";
                } else {
                    $errorMessage = "Error saving settings: " . $stmt->error;
                }
            } else {
                $errorMessage = "Error preparing statement: " . $conn->error;
            }
        }
    }

    // Get all certificates for dropdown
    $allCertsQuery = "SELECT id, name FROM certificates ORDER BY name";
    $allCertsResult = mysqli_query($conn, $allCertsQuery);
    $allCertificates = [];
    while ($row = mysqli_fetch_assoc($allCertsResult)) {
        $allCertificates[] = $row;
    }

    // Include the header
    include('header.php');
    // Include the sidebar
    include('sidebar.php');
    ?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Certificate Settings</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style2.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&family=Montserrat:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            
            
            /* Canvas and container styles from certificate generator */
        
            .canvas-container {
                overflow: auto;
                margin-bottom: 25px;
                position: relative;
                background-color: #f8fafc;
                border-radius: 8px;
                border: 1px solid #e1e5eb;
                /* Add these lines for better canvas display */
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 600px; /* Increase minimum height */
            }
            
            .canvas-container::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(0, 86, 179, 0.05) 0%, rgba(220, 53, 69, 0.05) 100%);
                border-radius: 8px;
                pointer-events: none;
            }

            canvas {
                max-width: 100%;
                height: auto;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                background-color: white;
                cursor: move;
                transition: all 0.3s ease;
                border: 1px solid #e1e5eb;
            }

            .drag-instructions {
                background-color: #f0f7ff;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 0.95rem;
                border-left: 4px solid #0056b3;
                display: flex;
                align-items: center;
            }

            .drag-instructions i {
                font-size: 1.5rem;
                color: #0056b3;
                margin-right: 12px;
            }

            .element-selector {
                margin-bottom: 20px;
                background-color: #f8fafc;
                padding: 15px;
                border-radius: 8px;
                border: 1px solid #e1e5eb;
            }

            .element-selector label {
                margin-bottom: 10px;
                color: #0056b3;
                font-weight: 600;
            }
            

            .button-group {
                display: flex;
                gap: 15px;
                justify-content: center;
            }

            #element-position-info {
                background-color: #f8fafc;
                border: 1px solid #e1e5eb;
                font-size: 0.9rem;
                padding: 10px;
                border-radius: 6px;
            }
            
            .center-line-horizontal, .center-line-vertical {
                position: absolute;
                background-color: rgba(255, 0, 0, 0.3);
                z-index: 5;
            }
            
            .center-line-horizontal {
                left: 0; right: 0; top: 50%; height: 1px;
                border-top: 1px dashed rgba(255, 0, 0, 0.5);
            }
            
            .center-line-vertical {
                top: 0; bottom: 0; left: 50%; width: 1px;
                border-left: 1px dashed rgba(255, 0, 0, 0.5);
            }
            
            .file-input {
                margin-bottom: 22px;
            }
            
            .file-input-container {
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 30px 20px;
                border: 2px dashed #ccd0d5;
                border-radius: var(--border-radius);
                background-color: #f8fafc;
                transition: var(--transition);
                cursor: pointer;
                margin-top: 8px;
            }
            
            .file-input-container:hover {
                border-color: var(--primary-light);
                background-color: #f0f7ff;
            }
            
            .file-input-container i {
                font-size: 2.5rem;
                color: var(--primary-light);
                margin-bottom: 15px;
            }
            
            .file-input-container p {
                margin: 0;
                color: #64748b;
                text-align: center;
            }
            
            .file-input-container strong {
                color: var(--primary-color);
            }
            
            .file-input input[type="file"] {
                position: absolute;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                opacity: 0;
                cursor: pointer;
            }
            
            .file-name {
                margin-top: 10px;
                font-size: 0.9rem;
                color: var(--primary-color);
                text-align: center;
                display: none;
            }
            
            .main-content .form-control,
            .main-content .form-select {
                border: 2px solid #e9ecef !important;
                border-radius: 8px !important;
                padding: 12px 15px !important;
                font-size: 1rem !important;
                transition: all 0.3s ease !important;
                background-color: #ffffff !important;
                color: #495057 !important;
            }

            .main-content .form-control:focus,
            .main-content .form-select:focus {
                border-color: #007bff !important;
                outline: none !important;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15) !important;
                background-color: #ffffff !important;
            }
            
            .input-group-text {
                padding: 0;
                overflow: hidden;
            }
            
            .settings-card {
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                margin-bottom: 20px;
                transition: all 0.3s ease;
            }
            
            .settings-card:hover {
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            }
            
            .settings-card-header {
                background-color: #f8f9fa;
                padding: 15px 20px;
                border-bottom: 1px solid #e9ecef;
                border-radius: 10px 10px 0 0;
            }

            .card-header {
                background: #01043A; !important;
                color: white !important;
                padding: 20px 30px !important;
                border-bottom: none !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                flex-wrap: wrap !important;
            }
            
            .settings-card-header h5 {
                margin: 0;
                color: #495057;
                font-weight: 600;
            }
            
            .settings-card-body {
                padding: 20px;
            }

            .back-to-certs {
                background-color: rgba(255, 255, 255, 0.2) !important;
                color: white !important;
                border: 2px solid rgba(255, 255, 255, 0.3) !important;
                padding: 10px 20px !important;
                border-radius: 25px !important;
                text-decoration: none !important;
                font-weight: 500 !important;
                font-size: 0.9rem !important;
                transition: all 0.3s ease !important;
                display: flex !important;
                align-items: center !important;
                gap: 8px !important;
            }

            .back-to-certs:hover {
                background-color: rgba(255, 255, 255, 0.3) !important;
                border-color: rgba(255, 255, 255, 0.5) !important;
                color: white !important;
                text-decoration: none !important;
                transform: translateY(-1px) !important;
            }

            .back-to-certs i {
                font-size: 0.8rem !important;
            }
            
            .size-control {
                display: flex;
                align-items: center;
                margin-top: 10px;
            }

            #certificateCanvas {
                max-width: 100%;
                height: auto;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }

            
            .size-control button {
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 1px solid #ced4da;
                background-color: #f8f9fa;
                color: #495057;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            .size-control button:hover {
                background-color: #e9ecef;
            }
            
            .size-control button:first-child {
                border-radius: 4px 0 0 4px;
            }
            
            .size-control button:last-child {
                border-radius: 0 4px 4px 0;
            }
            
            .size-control input {
                width: 60px;
                text-align: center;
                border-left: 0;
                border-right: 0;
                border-radius: 0;
            }
            
            .position-control {
                display: flex;
                align-items: center;
                margin-top: 10px;
            }
            
            .position-control .position-btn-group {
                display: grid;
                grid-template-columns: repeat(3, 32px);
                grid-template-rows: repeat(3, 32px);
                gap: 2px;
                margin-right: 15px;
            }
            
            .position-control .position-btn {
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 1px solid #ced4da;
                background-color: #f8f9fa;
                color: #495057;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            .position-control .position-btn:hover {
                background-color: #e9ecef;
            }
            
            .position-control .position-btn.center {
                background-color: #e9ecef;
            }
            
            .position-info {
                margin-left: 15px;
                font-size: 14px;
                color: #6c757d;
            }
            /* Success Notification Overlay */
            .notification-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.7);
                display: none;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                backdrop-filter: blur(5px);
            }

            .notification-content {
                background: linear-gradient(135deg, #01043A 0%, #0038A8 100%);
                color: white;
                padding: 40px;
                border-radius: 15px;
                text-align: center;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                max-width: 500px;
                width: 90%;
                animation: slideInScale 0.5s ease-out;
                border: 3px solid #28a745;
            }

            .success-icon {
                font-size: 4rem;
                color: #28a745;
                margin-bottom: 20px;
                animation: bounceIn 0.8s ease-out;
            }

            .notification-content h3 {
                margin: 0 0 15px 0;
                font-size: 1.5rem;
                font-weight: 700;
                letter-spacing: 1px;
            }

            .notification-content p {
                margin: 0;
                font-size: 1rem;
                opacity: 0.9;
                line-height: 1.5;
            }

            @keyframes slideInScale {
                0% {
                    transform: scale(0.7) translateY(-50px);
                    opacity: 0;
                }
                100% {
                    transform: scale(1) translateY(0);
                    opacity: 1;
                }
            }

            @keyframes bounceIn {
                0% {
                    transform: scale(0);
                }
                50% {
                    transform: scale(1.2);
                }
                100% {
                    transform: scale(1);
                }
            }

            /* Progress bar for countdown */
            .notification-progress {
                width: 100%;
                height: 4px;
                background-color: rgba(255, 255, 255, 0.3);
                border-radius: 2px;
                margin-top: 20px;
                overflow: hidden;
            }

            .notification-progress-bar {
                height: 100%;
                background-color: #28a745;
                width: 100%;
                animation: progressCountdown 3s linear;
            }

            @keyframes progressCountdown {
                0% {
                    width: 100%;
                }
                100% {
                    width: 0%;
                }
            }

            .main-content .btn-primary {
                background: #01043A !important;
                color: white !important;
            }

            .main-content .btn-primary:hover {
                background:  #0056b3 !important;
                transform: translateY(-2px) !important;
            }

        </style>
    </head>
    <body>
        <div class="main-content" style="margin-top: 120px;">
            <div class="container mt-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Certificate Text Settings</h4>
                                <a href="certificate_management.php" class="back-to-certs">
                                    <i class="fas fa-arrow-left"></i> Back to Certificate Management
                                </a>
                            </div>
                            <div class="page-header">
            
                            <div class="card-body">
                                <?php if (isset($successMessage)): ?>
                                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                <?php endif; ?>
                                                            <?php if (isset($errorMessage)): ?>
                                    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                                <?php endif; ?>
                            
                                
                                <form method="post" action="" id="settingsForm" enctype="multipart/form-data">

                                    <div class="row">
                                        <div class="col-md-5">
                                            <!-- Reference ID Settings -->
                                            <div class="settings-card mb-4">
                                                <div class="settings-card-header">
                                                    <h5><i class="fas fa-hashtag me-2"></i>Reference ID Settings</h5>
                                                </div>
                                                <div class="settings-card-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label for="ref_id_x" class="form-label">X Position (mm)</label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.1" class="form-control" id="ref_id_x" name="ref_id_x"
                                                                    value="<?php echo $settings['ref_id_x']; ?>" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                <button type="button" class="btn btn-outline-secondary position-adjust" data-field="ref_id_x" data-amount="-1" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-chevron-left"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-secondary position-adjust" data-field="ref_id_x" data-amount="1" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-chevron-right"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="ref_id_y" class="form-label">Y Position (mm)</label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.1" class="form-control" id="ref_id_y" name="ref_id_y"
                                                                    value="<?php echo $settings['ref_id_y']; ?>" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                <button type="button" class="btn btn-outline-secondary position-adjust" data-field="ref_id_y" data-amount="-1" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-chevron-up"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-secondary position-adjust" data-field="ref_id_y" data-amount="1" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-chevron-down"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="ref_id_size" class="form-label">Font Size (pt)</label>
                                                            <div class="input-group">
                                                                <button type="button" class="btn btn-outline-secondary size-adjust" data-field="ref_id_size" data-amount="-1" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                                <input type="number" class="form-control" id="ref_id_size" name="ref_id_size"
                                                                    value="<?php echo $settings['ref_id_size']; ?>" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                <button type="button" class="btn btn-outline-secondary size-adjust" data-field="ref_id_size" data-amount="1" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="ref_id_color" class="form-label">Color</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" id="ref_id_color" name="ref_id_color"
                                                                    value="<?php echo $settings['ref_id_color']; ?>" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                <span class="input-group-text p-0">
                                                                    <input type="color" class="form-control form-control-color" id="ref_id_color_picker"
                                                                        value="#<?php echo implode('', array_map(function($val) { return str_pad(dechex(intval($val)), 2, '0', STR_PAD_LEFT); }, explode(',', $settings['ref_id_color']))); ?>"
                                                                        <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <div class="form-text text-muted">
                                                            <i class="fas fa-info-circle me-1"></i> Reference ID appears in the top corner of the certificate
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Client Name Settings -->
                                            <div class="settings-card mb-4">
                                                <div class="settings-card-header">
                                                    <h5><i class="fas fa-user me-2"></i>Client Name Settings</h5>
                                                </div>
                                                <div class="settings-card-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label for="client_name_x" class="form-label">X Position (mm)</label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.1" class="form-control" id="client_name_x" name="client_name_x"
                                                                    value="<?php echo $settings['client_name_x'] === 'center' ? 148.5 : $settings['client_name_x']; ?>"
                                                                    <?php echo ($settings['client_name_x'] === 'center' || !$cert_id) ? 'disabled' : ''; ?>>
                                                                <button type="button" class="btn btn-outline-secondary position-adjust" data-field="client_name_x" data-amount="-1" <?php echo ($settings['client_name_x'] === 'center' || !$cert_id) ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-chevron-left"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-secondary position-adjust" data-field="client_name_x" data-amount="1" <?php echo ($settings['client_name_x'] === 'center' || !$cert_id) ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-chevron-right"></i>
                                                                </button>
                                                            </div>
                                                            <div class="form-check mt-2" style="visibility: hidden;">
                                                                <input class="form-check-input" type="checkbox" id="center_x" name="center_x"
                                                                    <?php echo $settings['client_name_x'] === 'center' ? 'checked' : ''; ?>
                                                                    <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                <label class="form-check-label" for="center_x">Center horizontally</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="client_name_y" class="form-label">Y Position (mm)</label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.1" class="form-control" id="client_name_y" name="client_name_y"
                                                                    value="<?php echo $settings['client_name_y'] === 'middle' ? 105 : $settings['client_name_y']; ?>"
                                                                    <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                <button type="button" class="btn btn-outline-secondary position-adjust" data-field="client_name_y" data-amount="-1" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-chevron-up"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-secondary position-adjust" data-field="client_name_y" data-amount="1" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-chevron-down"></i>
                                                                </button>
                                                            </div>
                                                            <!-- Removed the "Center vertically" checkbox completely -->
                                                        </div>
                                                    </div>
                                                    <!-- Rest of the settings remain the same -->
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="client_name_size" class="form-label">Font Size (pt)</label>
                                                            <div class="input-group">
                                                                <button type="button" class="btn btn-outline-secondary size-adjust" data-field="client_name_size" data-amount="-1" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                                <input type="number" class="form-control" id="client_name_size" name="client_name_size"
                                                                    value="<?php echo $settings['client_name_size']; ?>" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                <button type="button" class="btn btn-outline-secondary size-adjust" data-field="client_name_size" data-amount="1" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="client_name_color" class="form-label">Color</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" id="client_name_color" name="client_name_color"
                                                                    value="<?php echo $settings['client_name_color']; ?>" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                <span class="input-group-text p-0">
                                                                    <input type="color" class="form-control form-control-color" id="client_name_color_picker"
                                                                        value="#<?php echo implode('', array_map(function($val) { return str_pad(dechex(intval($val)), 2, '0', STR_PAD_LEFT); }, explode(',', $settings['client_name_color']))); ?>"
                                                                        <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <div class="form-text text-muted">
                                                            <i class="fas fa-info-circle me-1"></i> Client name appears prominently on the certificate
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class="mt-4 d-flex justify-content-between">
                                                <button type="submit" name="redirect_after_save" value="1" class="btn btn-primary" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                    <i class="fas fa-save me-1"></i> Save Setting
                                                </button>
                                                
                                                
                                                                                    
                                                <button type="submit" name="reset_defaults" class="btn btn-outline-secondary" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                    <i class="fas fa-undo me-1"></i> Reset to Defaults
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-7">
                                            <div class="card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Certificate Preview</h5>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="guidelines-toggle" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                        <label class="form-check-label" for="guidelines-toggle">Show Guidelines</label>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="text-center">
                                                        <div class="drag-instructions">
                                                            <i class="fas fa-hand-pointer"></i>
                                                            <div>
                                                                <strong>Click & Drag:</strong> Click directly on any text element to select and drag it to position.
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="canvas-container">
                                                            <canvas id="certificateCanvas"></canvas>
                                                        </div>
                                                        
                                                        <div id="element-position-info" class=" p-2 rounded">
                                                            <?php if ($cert_id): ?>
                                                                Click and drag to position elements on the certificate
                                                            <?php else: ?>
                                                                Please select a certificate to configure
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <div class="button-group mt-3">
                                                            <button type="button" class="btn btn-outline-primary" id="reset-positions" <?php echo (!$cert_id) ? 'disabled' : ''; ?>>
                                                                <i class="fas fa-undo"></i> Reset Positions
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-4">
                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Instructions</h6>
                                                                <ul class="mb-0">
                                                                    <li>Click directly on any text element to select it</li>
                                                                    <li>Drag selected elements to position them precisely</li>
                                                                    <li>Use arrow keys to fine-tune position (hold Shift for larger steps)</li>
                                                                    <li>Use the font size controls to adjust text size</li>
                                                                    <li>Toggle guidelines to see center markers</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Success Notification Overlay -->
            <div class="notification-overlay" id="successNotification">
                <div class="notification-content">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 id="notificationTitle">SETTINGS SAVED SUCCESSFULLY!</h3>
                    <p id="notificationMessage">Your certificate settings have been saved. Redirecting to certificate management...</p>
                    <div class="notification-progress">
                        <div class="notification-progress-bar"></div>
                    </div>
                </div>
            </div>

        </div>
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
        
        <script>
$(document).ready(function() {
    // Canvas setup
    const canvas = document.getElementById("certificateCanvas");
    const ctx = canvas.getContext("2d");
    let img = new Image();
    let hasTemplate = <?php echo (!empty($settings['background_image']) && file_exists($settings['background_image'])) ? 'true' : 'false'; ?>;
    
    // Constants for A4 landscape dimensions in mm
    const A4_WIDTH = 297;
    const A4_HEIGHT = 210; // Corrected A4 height
    const CANVAS_WIDTH = 842; // A4 width in pixels at 72dpi
    const CANVAS_HEIGHT = 595; // A4 height in pixels at 72dpi
    
    // Scaling factors
    const IMAGE_SCALE_FACTOR = 1.0;
    const scaleX = (CANVAS_WIDTH * IMAGE_SCALE_FACTOR) / A4_WIDTH;
    const scaleY = (CANVAS_HEIGHT * IMAGE_SCALE_FACTOR) / A4_HEIGHT;
    const offsetX = (CANVAS_WIDTH - (CANVAS_WIDTH * IMAGE_SCALE_FACTOR)) / 2;
    const offsetY = (CANVAS_HEIGHT - (CANVAS_HEIGHT * IMAGE_SCALE_FACTOR)) / 2;
    
    // Elements configuration
    let elements = {
        ref_id: {
            x: <?php echo $settings['ref_id_x']; ?>,
            y: <?php echo $settings['ref_id_y']; ?>,
            text: "DTI-2023-001",
            fontSize: <?php echo $settings['ref_id_size']; ?>,
            color: "rgb(<?php echo $settings['ref_id_color']; ?>)"
        },
        client_name: {
            x: <?php echo $settings['client_name_x'] === 'center' ? 'null' : $settings['client_name_x']; ?>,
            y: <?php echo $settings['client_name_y'] === 'middle' ? 105 : $settings['client_name_y']; ?>,
            text: "NAME OF CLIENT",
            fontSize: <?php echo $settings['client_name_size']; ?>,
            color: "rgb(<?php echo $settings['client_name_color']; ?>)",
            centerX: <?php echo $settings['client_name_x'] === 'center' ? 'true' : 'false'; ?>,
            centerY: false
        }
    };
    
    // Dragging state
    let isDragging = false;
    let selectedElement = "ref_id";
    let startX, startY;
    
    // History for undo/redo
    let history = [];
    let historyIndex = -1;
    const maxHistory = 50;
    
    // Utility functions
    function mmToPixels(mm, axis) {
        if (axis === 'x') {
            return (mm * scaleX) + offsetX;
        } else {
            return (mm * scaleY) + offsetY;
        }
    }
    
    function pixelsToMM(pixels, axis) {
        if (axis === 'x') {
            return (pixels - offsetX) / scaleX;
        } else {
            return (pixels - offsetY) / scaleY;
        }
    }
    
    function roundToDecimal(value, decimals = 1) {
        return Math.round(value * Math.pow(10, decimals)) / Math.pow(10, decimals);
    }
    
    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }
    
    function rgbToHex(rgb) {
        const match = rgb.match(/rgb\((\d+),\s*(\d+),\s*(\d+)\)/);
        if (match) {
            const r = parseInt(match[1]);
            const g = parseInt(match[2]);
            const b = parseInt(match[3]);
            return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        }
        return "#000000";
    }
    
    // History management
    function saveState() {
        if (historyIndex < history.length - 1) {
            history = history.slice(0, historyIndex + 1);
        }
        
        history.push({
            ref_id: { ...elements.ref_id },
            client_name: { ...elements.client_name }
        });
        
        if (history.length > maxHistory) {
            history.shift();
        } else {
            historyIndex++;
        }
        
        updateUndoRedoButtons();
    }
    
    function undo() {
        if (historyIndex > 0) {
            historyIndex--;
            const state = history[historyIndex];
            elements.ref_id = { ...state.ref_id };
            elements.client_name = { ...state.client_name };
            
            updateFormFromElements();
            drawCanvas();
            updateUndoRedoButtons();
        }
    }
    
    function redo() {
        if (historyIndex < history.length - 1) {
            historyIndex++;
            const state = history[historyIndex];
            elements.ref_id = { ...state.ref_id };
            elements.client_name = { ...state.client_name };
            
            updateFormFromElements();
            drawCanvas();
            updateUndoRedoButtons();
        }
    }
    
    function updateUndoRedoButtons() {
        $("#undo-btn").prop('disabled', historyIndex <= 0);
        $("#redo-btn").prop('disabled', historyIndex >= history.length - 1);
    }
    
    function updateFormFromElements() {
        $("#ref_id_x").val(elements.ref_id.x.toFixed(1));
        $("#ref_id_y").val(elements.ref_id.y.toFixed(1));
        $("#ref_id_size").val(elements.ref_id.fontSize);
        $("#ref_id_color").val(elements.ref_id.color.replace('rgb(', '').replace(')', ''));
        $("#ref_id_color_picker").val(rgbToHex(elements.ref_id.color));
        
        if (elements.client_name.centerX) {
            $("#client_name_x").val('center').prop('disabled', true);
        } else {
            $("#client_name_x").val(elements.client_name.x.toFixed(1)).prop('disabled', false);
        }
        
        $("#client_name_y").val(elements.client_name.y.toFixed(1));
        $("#client_name_size").val(elements.client_name.fontSize);
        $("#client_name_color").val(elements.client_name.color.replace('rgb(', '').replace(')', ''));
        $("#client_name_color_picker").val(rgbToHex(elements.client_name.color));
        $("#center_x").prop('checked', elements.client_name.centerX);
    }
    
    // Image loading
    if (hasTemplate) {
        console.log("Loading template:", "<?php echo $settings['background_image']; ?>");
        img.src = "<?php echo $settings['background_image']; ?>";
        
        img.onerror = function() {
            console.error("Failed to load image:", img.src);
            canvas.width = CANVAS_WIDTH;
            canvas.height = CANVAS_HEIGHT;
            hasTemplate = false;
            drawCanvas();
            showNotification("Failed to load certificate template", "error");
        };
        
        img.onload = function() {
            console.log("Image loaded successfully:", img.width, "x", img.height);
            canvas.width = CANVAS_WIDTH;
            canvas.height = CANVAS_HEIGHT;
            drawCanvas();
        };
    } else {
        canvas.width = CANVAS_WIDTH;
        canvas.height = CANVAS_HEIGHT;
        drawCanvas();
    }
    
    // Canvas drawing function
    function drawCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Draw background
        if (hasTemplate && img.complete) {
            const scaledWidth = canvas.width * IMAGE_SCALE_FACTOR;
            const scaledHeight = canvas.height * IMAGE_SCALE_FACTOR;
            const imgOffsetX = (canvas.width - scaledWidth) / 2;
            const imgOffsetY = (canvas.height - scaledHeight) / 2;
            ctx.drawImage(img, imgOffsetX, imgOffsetY, scaledWidth, scaledHeight);
        } else {
            ctx.fillStyle = "white";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Draw border
            ctx.strokeStyle = "#ddd";
            ctx.lineWidth = 1;
            ctx.strokeRect(0, 0, canvas.width, canvas.height);
        }
        
        // Draw guidelines if enabled
        if ($("#guidelines-toggle").is(':checked')) {
            drawGuidelines();
        }
        
        // Draw text elements
        drawTextElements();
        
        // Update position info
        updatePositionInfo();
    }
    
    function drawGuidelines() {
        ctx.save();
        ctx.setLineDash([5, 5]);
        ctx.strokeStyle = "rgba(255, 0, 0, 0.5)";
        ctx.lineWidth = 1;
        
        // Center lines
        ctx.beginPath();
        ctx.moveTo(0, canvas.height / 2);
        ctx.lineTo(canvas.width, canvas.height / 2);
        ctx.moveTo(canvas.width / 2, 0);
        ctx.lineTo(canvas.width / 2, canvas.height);
        ctx.stroke();
        
        // Grid lines (every 10mm)
        ctx.strokeStyle = "rgba(0, 0, 255, 0.2)";
        for (let i = 10; i < A4_WIDTH; i += 10) {
            const x = mmToPixels(i, 'x');
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, canvas.height);
            ctx.stroke();
        }
        
        for (let i = 10; i < A4_HEIGHT; i += 10) {
            const y = mmToPixels(i, 'y');
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(canvas.width, y);
            ctx.stroke();
        }
        
        ctx.restore();
    }
    
    function drawTextElements() {
        // Draw reference ID
        const refIdX = mmToPixels(elements.ref_id.x, 'x');
        const refIdY = mmToPixels(elements.ref_id.y, 'y');
        const refIdFontSize = elements.ref_id.fontSize * IMAGE_SCALE_FACTOR;
        
        ctx.font = `${refIdFontSize}px 'Courier New', monospace`;
        ctx.fillStyle = elements.ref_id.color;
        ctx.textAlign = "left";
        ctx.textBaseline = "top";
        ctx.fillText(elements.ref_id.text, refIdX, refIdY);
        
        // Draw client name
        let clientNameX, clientNameY;
        
        if (elements.client_name.centerX) {
            clientNameX = canvas.width / 2;
            ctx.textAlign = "center";
        } else {
            clientNameX = mmToPixels(elements.client_name.x, 'x');
            ctx.textAlign = "left";
        }
        
        clientNameY = mmToPixels(elements.client_name.y, 'y');
        
        const clientNameFontSize = elements.client_name.fontSize * IMAGE_SCALE_FACTOR;
        ctx.font = `bold ${clientNameFontSize}px 'Times New Roman', serif`;
        ctx.fillStyle = elements.client_name.color;
        ctx.textBaseline = "top";
        ctx.fillText(elements.client_name.text, clientNameX, clientNameY);
        
        // Draw underline for client name
        const textWidth = ctx.measureText(elements.client_name.text).width;
        const lineY = clientNameY + clientNameFontSize + 2;
        
        ctx.beginPath();
        if (ctx.textAlign === "center") {
            ctx.moveTo(clientNameX - textWidth / 2, lineY);
            ctx.lineTo(clientNameX + textWidth / 2, lineY);
        } else {
            ctx.moveTo(clientNameX, lineY);
            ctx.lineTo(clientNameX + textWidth, lineY);
        }
        ctx.strokeStyle = elements.client_name.color;
        ctx.lineWidth = 2;
        ctx.stroke();
        
        // Draw selection highlights
        drawSelectionHighlight();
    }
    
    function drawSelectionHighlight() {
        if (!$("#guidelines-toggle").is(':checked')) return;
        
        ctx.save();
        ctx.setLineDash([3, 3]);
        ctx.strokeStyle = "rgba(0, 123, 255, 0.8)";
        ctx.lineWidth = 2;
        ctx.fillStyle = "rgba(0, 123, 255, 0.1)";
        
        if (selectedElement === "ref_id") {
            const x = mmToPixels(elements.ref_id.x, 'x');
            const y = mmToPixels(elements.ref_id.y, 'y');
            const fontSize = elements.ref_id.fontSize * IMAGE_SCALE_FACTOR;
            
            ctx.font = `${fontSize}px 'Courier New', monospace`;
            const width = ctx.measureText(elements.ref_id.text).width;
            const height = fontSize;
            
            ctx.fillRect(x - 5, y - 5, width + 10, height + 10);
            ctx.strokeRect(x - 5, y - 5, width + 10, height + 10);
        } else if (selectedElement === "client_name") {
            let x, y;
            
            if (elements.client_name.centerX) {
                x = canvas.width / 2;
                ctx.textAlign = "center";
            } else {
                x = mmToPixels(elements.client_name.x, 'x');
                ctx.textAlign = "left";
            }
            
            y = mmToPixels(elements.client_name.y, 'y');
            const fontSize = elements.client_name.fontSize * IMAGE_SCALE_FACTOR;
            
            ctx.font = `bold ${fontSize}px 'Times New Roman', serif`;
            const width = ctx.measureText(elements.client_name.text).width;
            const height = fontSize;
            
            if (ctx.textAlign === "center") {
                x = x - width / 2;
            }
            
            ctx.fillRect(x - 5, y - 5, width + 10, height + 15);
            ctx.strokeRect(x - 5, y - 5, width + 10, height + 15);
        }
        
        ctx.restore();
    }
    
        // Hit detection
    function isPointInElement(mouseX, mouseY, element) {
        const hitPadding = 10;
        let elementX, elementY, elementWidth, elementHeight;
        
        // Create temporary context for measurements
        const tempCanvas = document.createElement('canvas');
        const tempCtx = tempCanvas.getContext('2d');
        
        if (element === "ref_id") {
            elementX = mmToPixels(elements.ref_id.x, 'x');
            elementY = mmToPixels(elements.ref_id.y, 'y');
            const fontSize = elements.ref_id.fontSize * IMAGE_SCALE_FACTOR;
            
            tempCtx.font = `${fontSize}px 'Courier New', monospace`;
            elementWidth = tempCtx.measureText(elements.ref_id.text).width;
            elementHeight = fontSize;
            
        } else if (element === "client_name") {
            if (elements.client_name.centerX) {
                elementX = canvas.width / 2;
            } else {
                elementX = mmToPixels(elements.client_name.x, 'x');
            }
            
            elementY = mmToPixels(elements.client_name.y, 'y');
            const fontSize = elements.client_name.fontSize * IMAGE_SCALE_FACTOR;
            
            tempCtx.font = `bold ${fontSize}px 'Times New Roman', serif`;
            elementWidth = tempCtx.measureText(elements.client_name.text).width;
            elementHeight = fontSize;
            
            if (elements.client_name.centerX) {
                elementX = elementX - elementWidth / 2;
            }
        }
        
        return mouseX >= elementX - hitPadding && 
               mouseX <= elementX + elementWidth + hitPadding &&
               mouseY >= elementY - hitPadding && 
               mouseY <= elementY + elementHeight + hitPadding;
    }
    
    function getMousePosition(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleFactorX = canvas.width / rect.width;
        const scaleFactorY = canvas.height / rect.height;
        
        return {
            x: (e.clientX - rect.left) * scaleFactorX,
            y: (e.clientY - rect.top) * scaleFactorY
        };
    }
    
    // Mouse event handlers
    canvas.addEventListener("mousedown", function(e) {
        if (!hasTemplate) return;
        
        const mousePos = getMousePosition(e);
        let clickedElement = null;
        
        // Check which element was clicked
        if (isPointInElement(mousePos.x, mousePos.y, "ref_id")) {
            clickedElement = "ref_id";
        } else if (isPointInElement(mousePos.x, mousePos.y, "client_name")) {
            clickedElement = "client_name";
        }
        
        if (clickedElement) {
            selectedElement = clickedElement;
            isDragging = true;
            canvas.style.cursor = 'grabbing';
            
            startX = mousePos.x;
            startY = mousePos.y;
            
            // Update UI to show selected element
            updateElementSelection();
            drawCanvas();
        }
    });
    
    canvas.addEventListener("mousemove", function(e) {
        if (!hasTemplate) return;
        
        const mousePos = getMousePosition(e);
        
        if (isDragging) {
            // Calculate movement delta
            const dx = mousePos.x - startX;
            const dy = mousePos.y - startY;
            
            // Convert to mm
            const dxMM = pixelsToMM(dx, 'x') - pixelsToMM(0, 'x');
            const dyMM = pixelsToMM(dy, 'y') - pixelsToMM(0, 'y');
            
            if (selectedElement === "ref_id") {
                elements.ref_id.x = roundToDecimal(elements.ref_id.x + dxMM);
                elements.ref_id.y = roundToDecimal(elements.ref_id.y + dyMM);
                
                // Constrain to canvas bounds
                elements.ref_id.x = Math.max(0, Math.min(A4_WIDTH - 10, elements.ref_id.x));
                elements.ref_id.y = Math.max(0, Math.min(A4_HEIGHT - 10, elements.ref_id.y));
                
            } else if (selectedElement === "client_name") {
                if (!elements.client_name.centerX) {
                    elements.client_name.x = roundToDecimal(elements.client_name.x + dxMM);
                    elements.client_name.x = Math.max(0, Math.min(A4_WIDTH - 10, elements.client_name.x));
                }
                
                elements.client_name.y = roundToDecimal(elements.client_name.y + dyMM);
                elements.client_name.y = Math.max(0, Math.min(A4_HEIGHT - 10, elements.client_name.y));
            }
            
            startX = mousePos.x;
            startY = mousePos.y;
            
            updateFormFromElements();
            drawCanvas();
            return;
        }
        
        // Update cursor based on hover
        let overElement = false;
        if (isPointInElement(mousePos.x, mousePos.y, "ref_id") || 
            isPointInElement(mousePos.x, mousePos.y, "client_name")) {
            overElement = true;
        }
        
        canvas.style.cursor = overElement ? 'pointer' : 'default';
    });
    
    canvas.addEventListener("mouseup", function() {
        if (isDragging) {
            isDragging = false;
            canvas.style.cursor = 'default';
            saveState(); // Save state after drag operation
        }
    });
    
    canvas.addEventListener("mouseleave", function() {
        isDragging = false;
        canvas.style.cursor = 'default';
    });
    
    // Element selection UI
    function updateElementSelection() {
        $(".element-selector").removeClass('active');
        if (selectedElement === "ref_id") {
            $("#select-ref-id").addClass('active');
        } else if (selectedElement === "client_name") {
            $("#select-client-name").addClass('active');
        }
    }
    
    // Element selector buttons
    $("#select-ref-id").on('click', function() {
        selectedElement = "ref_id";
        updateElementSelection();
        drawCanvas();
    });
    
    $("#select-client-name").on('click', function() {
        selectedElement = "client_name";
        updateElementSelection();
        drawCanvas();
    });
    
    // Position info update
    function updatePositionInfo() {
        if (selectedElement === "ref_id") {
            $("#element-position-info").html(`
                <strong>Reference ID:</strong> X: ${elements.ref_id.x.toFixed(1)}mm, 
                Y: ${elements.ref_id.y.toFixed(1)}mm, Size: ${elements.ref_id.fontSize}pt
            `);
        } else if (selectedElement === "client_name") {
            const xPos = elements.client_name.centerX ? "Centered" : `${elements.client_name.x.toFixed(1)}mm`;
            $("#element-position-info").html(`
                <strong>Client Name:</strong> X: ${xPos}, 
                Y: ${elements.client_name.y.toFixed(1)}mm, Size: ${elements.client_name.fontSize}pt
            `);
        }
    }
    
    // Form input handlers
    $("#ref_id_x, #ref_id_y, #ref_id_size").on('input', function() {
        const id = $(this).attr('id');
        const value = parseFloat($(this).val());
        
        if (id === "ref_id_x") {
            elements.ref_id.x = value;
        } else if (id === "ref_id_y") {
            elements.ref_id.y = value;
        } else if (id === "ref_id_size") {
            elements.ref_id.fontSize = Math.max(8, Math.min(72, value));
        }
        
        drawCanvas();
        debouncedSaveState();
    });
    
    $("#client_name_x, #client_name_y, #client_name_size").on('input', function() {
        const id = $(this).attr('id');
        const value = parseFloat($(this).val());
        
        if (id === "client_name_x" && !elements.client_name.centerX) {
            elements.client_name.x = value;
        } else if (id === "client_name_y") {
            elements.client_name.y = value;
        } else if (id === "client_name_size") {
            elements.client_name.fontSize = Math.max(10, Math.min(72, value));
        }
        
        drawCanvas();
        debouncedSaveState();
    });
    
    // Color picker handlers
    $("#ref_id_color_picker").on('input', function() {
        const hex = $(this).val();
        const rgb = hexToRgb(hex);
        const rgbString = `${rgb.r},${rgb.g},${rgb.b}`;
        
        $("#ref_id_color").val(rgbString);
        elements.ref_id.color = `rgb(${rgbString})`;
        drawCanvas();
        debouncedSaveState();
    });
    
    $("#client_name_color_picker").on('input', function() {
        const hex = $(this).val();
        const rgb = hexToRgb(hex);
        const rgbString = `${rgb.r},${rgb.g},${rgb.b}`;
        
        $("#client_name_color").val(rgbString);
        elements.client_name.color = `rgb(${rgbString})`;
        drawCanvas();
        debouncedSaveState();
    });
    
    // Center X checkbox handler
    $("#center_x").on('change', function() {
        elements.client_name.centerX = $(this).is(':checked');
        
        if (elements.client_name.centerX) {
            $("#client_name_x").prop('disabled', true);
            $(".position-adjust[data-field='client_name_x']").prop('disabled', true);
            elements.client_name.x = null;
        } else {
            $("#client_name_x").prop('disabled', false);
            $(".position-adjust[data-field='client_name_x']").prop('disabled', false);
            elements.client_name.x = A4_WIDTH / 2;
            $("#client_name_x").val(elements.client_name.x.toFixed(1));
        }
        
        drawCanvas();
        saveState();
    });
    
    // Position adjustment buttons
    $(".position-adjust").on('click', function() {
        const field = $(this).data('field');
        const amount = parseFloat($(this).data('amount'));
        
        if (field === "ref_id_x") {
            elements.ref_id.x = roundToDecimal(elements.ref_id.x + amount);
            elements.ref_id.x = Math.max(0, Math.min(A4_WIDTH - 10, elements.ref_id.x));
            $("#ref_id_x").val(elements.ref_id.x.toFixed(1));
        } else if (field === "ref_id_y") {
            elements.ref_id.y = roundToDecimal(elements.ref_id.y + amount);
            elements.ref_id.y = Math.max(0, Math.min(A4_HEIGHT - 10, elements.ref_id.y));
            $("#ref_id_y").val(elements.ref_id.y.toFixed(1));
        } else if (field === "client_name_x" && !elements.client_name.centerX) {
            elements.client_name.x = roundToDecimal(elements.client_name.x + amount);
            elements.client_name.x = Math.max(0, Math.min(A4_WIDTH - 10, elements.client_name.x));
            $("#client_name_x").val(elements.client_name.x.toFixed(1));
        } else if (field === "client_name_y") {
            elements.client_name.y = roundToDecimal(elements.client_name.y + amount);
            elements.client_name.y = Math.max(0, Math.min(A4_HEIGHT - 10, elements.client_name.y));
            $("#client_name_y").val(elements.client_name.y.toFixed(1));
        }
        
        drawCanvas();
        saveState();
    });
    
    // Size adjustment buttons
    $(".size-adjust").on('click', function() {
        const field = $(this).data('field');
        const amount = parseInt($(this).data('amount'));
        
        if (field === "ref_id_size") {
            elements.ref_id.fontSize = Math.max(8, Math.min(72, elements.ref_id.fontSize + amount));
            $("#ref_id_size").val(elements.ref_id.fontSize);
        } else if (field === "client_name_size") {
            elements.client_name.fontSize = Math.max(10, Math.min(72, elements.client_name.fontSize + amount));
            $("#client_name_size").val(elements.client_name.fontSize);
        }
        
        drawCanvas();
        saveState();
    });
    
    // Guidelines toggle
    $("#guidelines-toggle").on('change', function() {
        drawCanvas();
    });
    
    // Reset positions
    $("#reset-positions").on('click', function() {
        elements.ref_id.x = <?php echo $defaultSettings['ref_id_x']; ?>;
        elements.ref_id.y = <?php echo $defaultSettings['ref_id_y']; ?>;
        elements.ref_id.fontSize = <?php echo $defaultSettings['ref_id_size']; ?>;
        elements.ref_id.color = "rgb(<?php echo $defaultSettings['ref_id_color']; ?>)";
        
        elements.client_name.centerX = <?php echo $defaultSettings['client_name_x'] === 'center' ? 'true' : 'false'; ?>;
        elements.client_name.x = <?php echo $defaultSettings['client_name_x'] === 'center' ? 'null' : $defaultSettings['client_name_x']; ?>;
        elements.client_name.y = <?php echo $defaultSettings['client_name_y'] === 'middle' ? 105 : $defaultSettings['client_name_y']; ?>;
        elements.client_name.fontSize = <?php echo $defaultSettings['client_name_size']; ?>;
        elements.client_name.color = "rgb(<?php echo $defaultSettings['client_name_color']; ?>)";
        
        updateFormFromElements();
        drawCanvas();
        saveState();
        
        showNotification("Positions reset to defaults", "success");
    });
    
    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (!hasTemplate || $('input:focus, textarea:focus').length > 0) return;
        
        const step = e.shiftKey ? 5 : 1;
        let redraw = false;
        
                // Element switching
        if (e.key === "1") {
            selectedElement = "ref_id";
            updateElementSelection();
            redraw = true;
        } else if (e.key === "2") {
            selectedElement = "client_name";
            updateElementSelection();
            redraw = true;
        }
        
        // Movement controls
        if (selectedElement === "ref_id") {
            if (e.key === "ArrowLeft") {
                elements.ref_id.x = roundToDecimal(elements.ref_id.x - step * 0.1);
                elements.ref_id.x = Math.max(0, elements.ref_id.x);
                $("#ref_id_x").val(elements.ref_id.x.toFixed(1));
                redraw = true;
            } else if (e.key === "ArrowRight") {
                elements.ref_id.x = roundToDecimal(elements.ref_id.x + step * 0.1);
                elements.ref_id.x = Math.min(A4_WIDTH - 10, elements.ref_id.x);
                $("#ref_id_x").val(elements.ref_id.x.toFixed(1));
                redraw = true;
            } else if (e.key === "ArrowUp") {
                elements.ref_id.y = roundToDecimal(elements.ref_id.y - step * 0.1);
                elements.ref_id.y = Math.max(0, elements.ref_id.y);
                $("#ref_id_y").val(elements.ref_id.y.toFixed(1));
                redraw = true;
            } else if (e.key === "ArrowDown") {
                elements.ref_id.y = roundToDecimal(elements.ref_id.y + step * 0.1);
                elements.ref_id.y = Math.min(A4_HEIGHT - 10, elements.ref_id.y);
                $("#ref_id_y").val(elements.ref_id.y.toFixed(1));
                redraw = true;
            } else if (e.key === "+" || e.key === "=") {
                elements.ref_id.fontSize = Math.min(72, elements.ref_id.fontSize + 1);
                $("#ref_id_size").val(elements.ref_id.fontSize);
                redraw = true;
            } else if (e.key === "-" && elements.ref_id.fontSize > 8) {
                elements.ref_id.fontSize = Math.max(8, elements.ref_id.fontSize - 1);
                $("#ref_id_size").val(elements.ref_id.fontSize);
                redraw = true;
            }
        } else if (selectedElement === "client_name") {
            if (e.key === "ArrowLeft" && !elements.client_name.centerX) {
                elements.client_name.x = roundToDecimal(elements.client_name.x - step * 0.1);
                elements.client_name.x = Math.max(0, elements.client_name.x);
                $("#client_name_x").val(elements.client_name.x.toFixed(1));
                redraw = true;
            } else if (e.key === "ArrowRight" && !elements.client_name.centerX) {
                elements.client_name.x = roundToDecimal(elements.client_name.x + step * 0.1);
                elements.client_name.x = Math.min(A4_WIDTH - 10, elements.client_name.x);
                $("#client_name_x").val(elements.client_name.x.toFixed(1));
                redraw = true;
            } else if (e.key === "ArrowUp") {
                elements.client_name.y = roundToDecimal(elements.client_name.y - step * 0.1);
                elements.client_name.y = Math.max(0, elements.client_name.y);
                $("#client_name_y").val(elements.client_name.y.toFixed(1));
                redraw = true;
            } else if (e.key === "ArrowDown") {
                elements.client_name.y = roundToDecimal(elements.client_name.y + step * 0.1);
                elements.client_name.y = Math.min(A4_HEIGHT - 10, elements.client_name.y);
                $("#client_name_y").val(elements.client_name.y.toFixed(1));
                redraw = true;
            } else if (e.key === "+" || e.key === "=") {
                elements.client_name.fontSize = Math.min(72, elements.client_name.fontSize + 1);
                $("#client_name_size").val(elements.client_name.fontSize);
                redraw = true;
            } else if (e.key === "-" && elements.client_name.fontSize > 10) {
                elements.client_name.fontSize = Math.max(10, elements.client_name.fontSize - 1);
                $("#client_name_size").val(elements.client_name.fontSize);
                redraw = true;
            }
        }
        
        // Undo/Redo
        if (e.ctrlKey || e.metaKey) {
            if (e.key === "z" && !e.shiftKey) {
                e.preventDefault();
                undo();
                return;
            } else if ((e.key === "z" && e.shiftKey) || e.key === "y") {
                e.preventDefault();
                redo();
                return;
            }
        }
        
        if (redraw) {
            e.preventDefault();
            drawCanvas();
            debouncedSaveState();
        }
    });
    
    // Debounced save state for input changes
    let saveStateTimeout;
    function debouncedSaveState() {
        clearTimeout(saveStateTimeout);
        saveStateTimeout = setTimeout(saveState, 500);
    }
    
    // Notification system
    function showNotification(message, type = "info", duration = 3000) {
        const alertClass = type === "success" ? "alert-success" : 
                          type === "error" ? "alert-danger" : 
                          type === "warning" ? "alert-warning" : "alert-info";
        
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show notification-alert" role="alert">
                <i class="bi bi-${type === "success" ? "check-circle" : 
                                  type === "error" ? "exclamation-triangle" : 
                                  type === "warning" ? "exclamation-triangle" : "info-circle"}-fill me-2"></i>
                <strong>${message}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        
        // Remove existing notifications
        $('.notification-alert').remove();
        
        // Add new notification
        notification.prependTo('.container').hide().fadeIn(300);
        
        // Auto-hide after duration
        if (duration > 0) {
            setTimeout(() => {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, duration);
        }
    }
    
    // Save and return function
    function SaveAndReturn() {
        if (!hasTemplate) {
            showNotification("Please upload a certificate template first", "error");
            return;
        }
        
        const certificateName = document.getElementById("certificateName").value.trim();
        if (!certificateName) {
            showNotification("Please enter a certificate name", "error");
            return;
        }
        
        const certificateFileDescription = document.getElementById("certificateFileDescription").value;
        const certificateContentDescription = document.getElementById("descriptionInput").value;
        const imageData = canvas.toDataURL('image/png');
        
        showNotification("Processing... Saving certificate...", "info", 0);
        
        const data = {
            name: certificateName,
            file_description: certificateFileDescription,
            content_description: certificateContentDescription,
            image_data: imageData
        };
        
        fetch('save_certificate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            $('.notification-alert').remove();
            if (data.success) {
                showNotification(data.message, "success");
                setTimeout(() => {
                    window.location.href = 'certificate_management.php?cert_id=' + data.certificate_id;
                }, 1500);
            } else {
                showNotification(data.message, "error");
            }
        })
        .catch(error => {
            $('.notification-alert').remove();
            console.error('Error:', error);
            showNotification("Failed to save certificate. Please try again.", "error");
        });
    }
    
    // Form submission handlers
    // Enhanced form submission with proper success notification
$("#settingsForm").on('submit', function(e) {
    e.preventDefault();
    
    const isRedirectRequest = $(document.activeElement).attr('name') === 'redirect_after_save';
    const formData = $(this).serialize();
    
    const submitBtn = $("button[name='redirect_after_save']");
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i><strong>SAVING...</strong>').prop('disabled', true);
    
    $.ajax({
        type: "POST",
        url: window.location.href,
        data: formData,
        success: function(response) {
            submitBtn.html(originalText).prop('disabled', false);
            
            if (isRedirectRequest) {
                // Show success notification with redirect
                showSuccessNotification(
                    'SETTINGS SAVED SUCCESSFULLY!',
                    'Your certificate settings have been saved. Redirecting to certificate management...',
                    true // redirect flag
                );
                return;
            }
            
            // Handle success response for non-redirect saves
            showSuccessNotification(
                'SETTINGS SAVED!',
                'Your certificate settings have been saved successfully.',
                false // no redirect
            );
        },
        error: function(xhr, status, error) {
            submitBtn.html(originalText).prop('disabled', false);
            
            let errorMessage = 'Unknown error occurred';
            if (xhr.status === 404) {
                errorMessage = 'File not found (404). Please check the file path.';
            } else if (xhr.status === 500) {
                errorMessage = 'Server error (500). Please check server logs.';
            } else if (xhr.status === 0) {
                errorMessage = 'Network error. Please check your connection.';
            } else if (error) {
                errorMessage = error;
            }
            
            showErrorNotification('ERROR SAVING SETTINGS', errorMessage);
        }
    });
});

// Handle reset button with success notification
$("button[name='reset_defaults']").on('click', function(e) {
    e.preventDefault();
    
    const resetBtn = $(this);
    const originalText = resetBtn.html();
    resetBtn.html('<i class="fas fa-spinner fa-spin me-2"></i><strong>RESETTING...</strong>').prop('disabled', true);
    
    $.ajax({
        type: "POST",
        url: window.location.href,
        data: $("#settingsForm").serialize() + "&reset_defaults=1",
        success: function(response) {
            resetBtn.html(originalText).prop('disabled', false);
            
            // Reset form fields to defaults
            $("#reset-positions").click();
            
            // Show success notification
            showSuccessNotification(
                'SETTINGS RESET!',
                'All settings have been reset to their default values.',
                false
            );
        },
        error: function(xhr, status, error) {
            resetBtn.html(originalText).prop('disabled', false);
            
            let errorMessage = 'Unknown error occurred';
            if (xhr.status === 404) {
                errorMessage = 'File not found (404). Please check the file path.';
            } else if (xhr.status === 500) {
                errorMessage = 'Server error (500). Please check server logs.';
            } else if (xhr.status === 0) {
                errorMessage = 'Network error. Please check your connection.';
            } else if (error) {
                errorMessage = error;
            }
            
            showErrorNotification('ERROR RESETTING SETTINGS', errorMessage);
        }
    });
});

// Function to show success notification
function showSuccessNotification(title, message, shouldRedirect = false) {
    $("#notificationTitle").text(title);
    $("#notificationMessage").text(message);
    
    // Show the notification
    $("#successNotification").css('display', 'flex').hide().fadeIn(300);
    
    if (shouldRedirect) {
        // Redirect after 3 seconds
        setTimeout(function() {
            $("#successNotification").fadeOut(300, function() {
                window.location.href = 'certificate_management.php';
            });
        }, 3000);
    } else {
        // Auto-hide after 3 seconds for non-redirect notifications
        setTimeout(function() {
            $("#successNotification").fadeOut(300);
        }, 3000);
    }
}

// Function to show error notification
function showErrorNotification(title, message) {
    // Create error notification if it doesn't exist
    if ($("#errorNotification").length === 0) {
        const errorNotificationHtml = `
            <div class="notification-overlay" id="errorNotification">
                <div class="notification-content" style="border-color: #dc3545; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                    <div class="success-icon" style="color: #fff;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h3 id="errorNotificationTitle">ERROR!</h3>
                    <p id="errorNotificationMessage">An error occurred.</p>
                    <button class="btn btn-light mt-3" onclick="$('#errorNotification').fadeOut(300);">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
            </div>
        `;
        $('body').append(errorNotificationHtml);
    }
    
    $("#errorNotificationTitle").text(title);
    $("#errorNotificationMessage").text(message);
    
    // Show the error notification
    $("#errorNotification").css('display', 'flex').hide().fadeIn(300);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $("#errorNotification").fadeOut(300);
    }, 5000);
}

// Close notification when clicking outside
$(".notification-overlay").on('click', function(e) {
    if (e.target === this) {
        $(this).fadeOut(300);
    }
});

// Close notification with Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        $(".notification-overlay:visible").fadeOut(300);
    }
});

    
    // Reset defaults button
    $("button[name='reset_defaults']").on('click', function(e) {
        e.preventDefault();
        
        $.ajax({
            type: "POST",
            url: "certificate_name&id_pos.php?cert_id=<?php echo $cert_id; ?>",
            data: $("#settingsForm").serialize() + "&reset_defaults=1",
            success: function(response) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = response;
                const successMessage = $(tempDiv).find('.alert-success').text();
                
                if (successMessage) {
                    showNotification(successMessage.trim(), "success");
                    $("#reset-positions").click();
                }
            },
            error: function(xhr, status, error) {
                showNotification("Error resetting defaults: " + error, "error");
            }
        });
    });
    
    // Undo/Redo button handlers
    $("#undo-btn").on('click', undo);
    $("#redo-btn").on('click', redo);
    
    // Add tooltips
    $(".position-adjust").each(function() {
        const direction = $(this).find('i').hasClass('fa-chevron-left') ? 'left' :
                         $(this).find('i').hasClass('fa-chevron-right') ? 'right' :
                         $(this).find('i').hasClass('fa-chevron-up') ? 'up' : 'down';
        $(this).attr('title', `Move ${direction} (${$(this).data('amount')}mm)`);
    });
    
    $(".size-adjust").each(function() {
        const action = $(this).find('i').hasClass('fa-minus') ? 'Decrease' : 'Increase';
        $(this).attr('title', `${action} font size (${$(this).data('amount')}pt)`);
    });
    
    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        $('[title]').tooltip();
    }
    
    // Initialize canvas and save initial state
    if (hasTemplate) {
        drawCanvas();
    }
    
    // Save initial state to history
    saveState();
    updateElementSelection();
    
    // Add window resize handler
    $(window).on('resize', function() {
        drawCanvas();
    });
    
    // Prevent context menu on canvas
    canvas.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });
    
    // Add help text
    function showHelp() {
        const helpText = `
            <strong>Keyboard Shortcuts:</strong><br>
            • Arrow keys: Move selected element (Hold Shift for faster movement)<br>
            • +/- keys: Increase/decrease font size<br>
            • 1/2 keys: Switch between Reference ID and Client Name<br>
            • Ctrl+Z: Undo<br>
            • Ctrl+Y or Ctrl+Shift+Z: Redo<br><br>
            <strong>Mouse Controls:</strong><br>
            • Click and drag elements to move them<br>
            • Use the guidelines toggle for precise positioning
        `;
        
        showNotification(helpText, "info", 8000);
    }
    
    // Add help button handler if it exists
    $("#help-btn").on('click', showHelp);
    
    // Expose functions globally if needed
    window.SaveAndReturn = SaveAndReturn;
    window.showNotification = showNotification;
});

// Function to play success sound
function playSuccessSound() {
    // Create audio context for success sound
    if (typeof(AudioContext) !== "undefined" || typeof(webkitAudioContext) !== "undefined") {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        // Create success sound
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.frequency.setValueAtTime(1000, audioContext.currentTime + 0.1);
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.3);
    }
}

// Call sound in success notification function
function showSuccessNotification(title, message, shouldRedirect = false) {
    $("#notificationTitle").text(title);
    $("#notificationMessage").text(message);
    
    // Play success sound
    playSuccessSound();
    
    // Show the notification
    $("#successNotification").css('display', 'flex').hide().fadeIn(300);
    
    if (shouldRedirect) {
        setTimeout(function() {
            $("#successNotification").fadeOut(300, function() {
                window.location.href = 'certificate_management.php';
            });
        }, 3000);
    } else {
        setTimeout(function() {
            $("#successNotification").fadeOut(300);
        }, 3000);
    }
}

</script>

<?php
// Include the footer
include('footer.php');
?>
</body>
</html>



                    


