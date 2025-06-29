<?php
// Set the page title and current page for breadcrumb
$pageTitle = "Certificate Generator";
$currentPage = "Add New Certificate";

// Include header and sidebar
include('header.php');
include('sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/OIP.png" type="image/x-icon">
    <title>Certificate Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&family=Montserrat:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Certificate generator specific styles */
        .certificate-generator {
            --primary-color: #01043A;
            --primary-light: #0038A8;
            --secondary-color: #7209b7;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4caf50;
            --border-radius: 8px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
            
            height: 100vh;
            overflow: hidden;
        }
        
        .certificate-generator * {
            box-sizing: border-box;
        }
        
        .certificate-generator body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f0f2f5;
            color: #212529;
            line-height: 1.6;
            overflow: hidden;
        }
        
        .certificate-generator .container {
            max-width: 1500px;
            margin: 0 auto;
            padding: 10px;
            height: calc(100vh - 30px);
            overflow: hidden;
        }
        
        .certificate-generator .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .certificate-generator .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            flex-shrink: 0;
        }
        
        .certificate-generator .card-header h4 {
            color: var(--primary-color);
            font-weight: 700;
            letter-spacing: -0.5px;
            margin: 0;
        }
        
        .certificate-generator .card-body {
            padding: 1.5rem;
            flex: 1;
            overflow: hidden;
        }
        
        .certificate-generator .app-container {
            display: flex;
            flex-direction: row;
            gap: 30px;
            height: 100%;
            overflow: hidden;
        }
        
        .certificate-generator .input-section {
            flex: 0 0 400px;
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .certificate-generator .preview-section {
            flex: 1;
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .certificate-generator .section-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: var(--primary-color);
            position: relative;
            padding-bottom: 10px;
            font-weight: 600;
            flex-shrink: 0;
        }
        
        .certificate-generator .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            border-radius: 3px;
        }
        
        .certificate-generator .form-group {
            margin-bottom: 18px;
        }
        
        .certificate-generator label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #212529;
            font-size: 13px;
        }
        
        .certificate-generator input[type="text"],
        .certificate-generator input[type="number"],
        .certificate-generator input[type="date"],
        .certificate-generator input[type="color"],
        .certificate-generator input[type="range"],
        .certificate-generator textarea,
        .certificate-generator select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e1e5eb;
            border-radius: var(--border-radius);
            font-family: 'Poppins', Arial, sans-serif;
            transition: var(--transition);
            font-size: 0.9rem;
            background-color: #fcfcfc;
        }
        
        .certificate-generator input[type="text"]:focus,
        .certificate-generator input[type="number"]:focus,
        .certificate-generator input[type="date"]:focus,
        .certificate-generator input[type="color"]:focus,
        .certificate-generator textarea:focus,
        .certificate-generator select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(1, 4, 58, 0.15);
            background-color: white;
        }

        /* NEW: Text Formatting Panel Styles */
        .certificate-generator .text-formatting-panel {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8fafc;
            border-radius: var(--border-radius);
            border: 1px solid #e1e5eb;
        }

        .certificate-generator .text-formatting-panel h4 {
            margin-bottom: 15px;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .certificate-generator .formatting-toolbar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
            margin-bottom: 15px;
        }

        .certificate-generator .toolbar-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .certificate-generator .toolbar-group label {
            font-size: 11px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 3px;
        }

        .certificate-generator .format-btn {
            background: white;
            border: 1px solid #ddd;
            padding: 6px 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
        }

        .certificate-generator .format-btn:hover {
            background: #f0f0f0;
            border-color: var(--primary-color);
        }

        .certificate-generator .format-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .certificate-generator .toolbar-group:nth-child(3) {
            display: flex;
            flex-direction: row;
            gap: 3px;
            align-items: end;
        }

        .certificate-generator .toolbar-group:nth-child(4) {
            display: flex;
            flex-direction: row;
            gap: 5px;
            align-items: end;
        }

        .certificate-generator .toolbar-group:nth-child(4) input[type="color"] {
            width: 40px;
            height: 32px;
            padding: 2px;
            border-radius: 4px;
        }

        /* Margin Controls */
        .certificate-generator .margin-controls {
            margin-bottom: 15px;
        }

        .certificate-generator .margin-control {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .certificate-generator .margin-control label {
            font-size: 11px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .certificate-generator .margin-slider-container {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .certificate-generator .margin-slider {
            flex: 1;
            height: 6px;
            background: #ddd;
            border-radius: 3px;
            outline: none;
            -webkit-appearance: none;
        }

        .certificate-generator .margin-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            background: var(--primary-color);
            border-radius: 50%;
            cursor: pointer;
        }

        .certificate-generator .margin-value {
            width: 60px;
            padding: 4px 6px;
            font-size: 12px;
        }

        /* Alignment Controls */
        .certificate-generator .alignment-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .certificate-generator .alignment-group {
            flex: 1;
        }

        .certificate-generator .alignment-group label {
            font-size: 11px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .certificate-generator .alignment-btn {
            background: #01043A; 
            border: 1px solid #ddd;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 11px;
            margin-right: 5px;
        }

        .certificate-generator .alignment-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .certificate-generator .line-spacing-control select {
            padding: 4px 6px;
            font-size: 12px;
        }

        /* Grid Controls */
        .certificate-generator .grid-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .certificate-generator .grid-toggle {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .certificate-generator .grid-toggle input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .certificate-generator .grid-toggle label {
            font-size: 11px;
            margin: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .certificate-generator textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .certificate-generator .file-input {
            margin-bottom: 18px;
        }
        
        .certificate-generator .file-input-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px 15px;
            border: 2px dashed #ccd0d5;
            border-radius: var(--border-radius);
            background-color: #f8fafc;
            transition: var(--transition);
            cursor: pointer;
            margin-top: 6px;
        }
        
        .certificate-generator .file-input-container:hover {
            border-color: var(--primary-color);
            background-color: #f0f7ff;
        }
        
        .certificate-generator .file-input-container i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .certificate-generator .file-input-container p {
            margin: 0;
            color: #64748b;
            text-align: center;
            font-size: 12px;
        }
        
        .certificate-generator .file-input-container strong {
            color: var(--primary-color);
        }
        
        .certificate-generator .file-input-container input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }
        
        .certificate-generator .file-name {
            margin-top: 8px;
            font-size: 0.8rem;
            color: var(--primary-color);
            text-align: center;
            display: none;
        }
        
        .certificate-generator .position-controls {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .certificate-generator .button-group {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        
        .certificate-generator .btn {
            padding: 0.4rem 0.8rem;
            font-weight: 500;
            border-radius: var(--border-radius);
            transition: var(--transition);
            display: inline-flex;
            align-items : center;
            justify-content: center;
        }
        
        .certificate-generator .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .certificate-generator .btn-primary:hover,
        .certificate-generator .btn-primary:focus {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transform: translateY(-1px);
        }
        
        .certificate-generator button {
            padding: 12px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 500;
            transition: var(--transition);
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(1, 4, 58, 0.2);
        }
        
        .certificate-generator button:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 56, 168, 0.25);
        }
        
        .certificate-generator button i {
            margin-right: 6px;
        }
        
        .certificate-generator button.download-btn {
            background-color: #28a745;
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.2);
        }
        
        .certificate-generator button.download-btn:hover {
            background-color: #218838;
            box-shadow: 0 6px 15px rgba(40, 167, 69, 0.25);
        }
        
        .certificate-generator button.save-btn {
            width: 100%;
        }
        
        .certificate-generator canvas {
            max-width: 100%;
            max-height: 100%;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: white;
            cursor: move;
            transition: var(--transition);
            border: 1px solid #e1e5eb;
        }
        
        .certificate-generator .canvas-container {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
            border-radius: var(--border-radius);
            overflow: hidden;
            min-height: 400px;
        }
        
        .certificate-generator .canvas-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(1, 4, 58, 0.05) 0%, rgba(0, 56, 168, 0.05) 100%);
            border-radius: var(--border-radius);
            pointer-events: none;
        }

        .certificate-generator #certificateCanvas {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: default;
            object-fit: contain;
            display: block;
        }
        
        .certificate-generator .editable-text {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            font-family: inherit;
            font-size: 0.875rem;
            line-height: 1.5;
            background-color: #fff;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .certificate-generator .editable-text:focus {
            outline: none;
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .certificate-generator .editable-text[contenteditable]:empty::before {
            content: attr(placeholder);
            color: #6c757d;
            font-style: italic;
        }

        .certificate-generator .text-formatting-controls {
            display: flex;
            gap: 0.25rem;
        }

        .certificate-generator .text-formatting-controls .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .certificate-generator .text-formatting-controls .btn.active {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }
        
        .certificate-generator small {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 0.75rem;
        }
        
        .certificate-generator #statusMessage {
            padding: 12px;
            border-radius: var(--border-radius);
            margin-top: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        
        .certificate-generator #statusMessage i {
            margin-right: 8px;
            font-size: 1rem;
        }
        
        /* Custom scrollbar for input section */
        .certificate-generator .input-section::-webkit-scrollbar {
            width: 6px;
        }
        
        .certificate-generator .input-section::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .certificate-generator .input-section::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }
        
        .certificate-generator .input-section::-webkit-scrollbar-thumb:hover {
            background: var(--primary-light);
        }
        
        @media (max-width: 1200px) {
            .certificate-generator .app-container {
                flex-direction: column;
                gap: 20px;
            }
            
            .certificate-generator .input-section {
                flex: none;
                max-height: 300px;
            }
            
            .certificate-generator .preview-section {
                flex: 1;
                min-height: 400px;
            }
        }
        
        @media (max-width: 768px) {
            .certificate-generator .container {
                padding: 10px;
                height: calc(100vh - 90px);
            }
            
            .certificate-generator .card-body {
                padding: 1rem;
            }
            
            .certificate-generator .input-section,
            .certificate-generator .preview-section {
                padding: 15px;
            }
            
            .certificate-generator .formatting-toolbar {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            
            .certificate-generator .input-section {
                max-height: 250px;
            }
        }
    </style>
</head>

<body>
<div class="main-content certificate-generator" style="margin-top: 90px;">
    <div class="container mt-4">
        <div class="row h-100">
            <div class="col-12 h-100">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><b>CERTIFICATE GENERATOR</b></h4>
                        <a href="certificate_management.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Certificate Management
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="app-container">
                            <div class="input-section">
                                <h3 class="section-title">Design Your Certificate</h3>
                                
                                <div class="file-input">
                                    <label for="upload">Certificate Template</label>
                                    <div class="file-input-container">
                                        <i class="fas fa-file-image"></i>
                                        <p><strong>Click to upload</strong> or drag and drop</p>
                                        <p>SVG, PNG, JPG or GIF (max. 5MB)</p>
                                        <input type="file" id="upload" accept="image/*">
                                    </div>
                                    <div class="file-name" id="templateFileName"></div>
                                </div>
                                
                                <div class="form-group certificate-name">
                                    <label for="certificateName">Certificate Name</label>
                                    <input type="text" id="certificateName" placeholder="Enter certificate name" required>
                                    <small>This name will be used to identify the certificate in the system</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="descriptionInput"><i class="fas fa-align-left"></i> Certificate Description</label>
                                    <div id="descriptionInput" 
                                        class="form-control editable-text" 
                                        contenteditable="true" 
                                        placeholder="Enter certificate description..."
                                        style="min-height: 100px; max-height: 200px; overflow-y: auto;">
                                    </div>
                                    <div class="text-formatting-controls mt-2">
                                        <button type="button" id="boldBtn" class="btn btn-sm btn-outline-secondary" title="Bold (Ctrl+B)">
                                            <i class="fas fa-bold"></i>
                                        </button>
                                        <button type="button" id="italicBtn" class="btn btn-sm btn-outline-secondary" title="Italic (Ctrl+I)">
                                            <i class="fas fa-italic"></i>
                                        </button>
                                        <button type="button" id="underlineBtn" class="btn btn-sm btn-outline-secondary" title="Underline (Ctrl+U)">
                                            <i class="fas fa-underline"></i>
                                        </button>
                                        <button type="button" id="clearFormatBtn" class="btn btn-sm btn-outline-danger" title="Clear Formatting">
                                            <i class="fas fa-remove-format"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">Select text and use formatting buttons or keyboard shortcuts (Ctrl+B, Ctrl+I, Ctrl+U)</small>
                                </div>

                                <!-- NEW: Enhanced Text Formatting Panel -->
                                <div class="text-formatting-panel">
                                    <h4><i class="fas fa-palette"></i> Text Formatting</h4>
                                    
                                    <div class="formatting-toolbar">
                                        <div class="toolbar-group">
                                            <label>Font Family</label>
                                            <select id="fontFamily">
                                                <option value="Poppins, Arial, sans-serif">Poppins</option>
                                                <option value="'Playfair Display', serif">Playfair Display</option>
                                                <option value="Montserrat, sans-serif">Montserrat</option>
                                                <option value="Roboto, sans-serif">Roboto</option>
                                                <option value="Arial, sans-serif">Arial</option>
                                                <option value="'Times New Roman', serif">Times New Roman</option>
                                            </select>
                                        </div>
                                        
                                        <div class="toolbar-group">
                                            <label>Font Size</label>
                                            <select id="fontSize">
                                                <option value="16">16px</option>
                                                <option value="18">18px</option>
                                                <option value="20">20px</option>
                                                <option value="24">24px</option>
                                                <option value="28">28px</option>
                                                <option value="32">32px</option>
                                                <option value="36">36px</option>
                                                <option value="40" selected>40px</option>
                                                <option value="48">48px</option>
                                                <option value="56">56px</option>
                                                <option value="64">64px</option>
                                                <option value="72">72px</option>
                                            </select>
                                        </div>
                                        
                                        <div class="toolbar-group">
                                            <label>Text Color</label>
                                            <input type="color" id="textColor" value="#000000">
                                        </div>
                                        
                                        <div class="toolbar-group">
                                            <label>Line Spacing</label>
                                            <select id="lineSpacing">
                                                <option value="1.0">1.0</option>
                                                <option value="1.2">1.2</option>
                                                <option value="1.3" selected>1.3</option>
                                                <option value="1.5">1.5</option>
                                                <option value="1.8">1.8</option>
                                                <option value="2.0">2.0</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Margin Controls -->
                                    <div class="margin-controls">
                                        <div class="margin-control">
                                            <label><i class="fas fa-arrows-alt-h"></i> Left Margin</label>
                                            <div class="margin-slider-container">
                                                <input type="range" id="leftMargin" class="margin-slider" min="0" max="200" value="50">
                                                <input type="number" id="leftMarginValue" class="margin-value" min="0" max="200" value="50">
                                            </div>
                                        </div>
                                        
                                        <div class="margin-control">
                                            <label><i class="fas fa-arrows-alt-h"></i> Right Margin</label>
                                            <div class="margin-slider-container">
                                                <input type="range" id="rightMargin" class="margin-slider" min="0" max="200" value="50">
                                                <input type="number" id="rightMarginValue" class="margin-value" min="0" max="200" value="50">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Alignment Controls -->
                                    <div class="alignment-controls">
                                        <div class="alignment-group">
                                            <label>Quick Align</label>
                                            <button type="button" id="centerHorizontalBtn" class="alignment-btn" title="Center Horizontally">
                                                                                               <i class="fas fa-arrows-alt-h"></i> Center H
                                            </button>
                                            <button type="button" id="centerVerticalBtn" class="alignment-btn" title="Center Vertically">
                                                <i class="fas fa-arrows-alt-v"></i> Center V
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Grid Controls -->
                                    <div class="grid-controls">
                                        <div class="grid-toggle">
                                            <input type="checkbox" id="showGridlines">
                                            <label for="showGridlines"><i class="fas fa-th"></i> Show Grid</label>
                                        </div>
                                        <div class="grid-toggle">
                                            <input type="checkbox" id="showMarginLines" checked>
                                            <label for="showMarginLines"><i class="fas fa-ruler-vertical"></i> Show Margins</label>
                                        </div>
                                        <div class="grid-toggle">
                                            <input type="checkbox" id="showCenterLines">
                                            <label for="showCenterLines"><i class="fas fa-crosshairs"></i> Show Center Lines</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group certificate-file-description">
                                    <label for="certificateFileDescription">Template Description</label>
                                    <textarea id="certificateFileDescription" placeholder="Enter a description for this certificate template" rows="3"></textarea>
                                    <small>Provide details about what this certificate is for</small>
                                </div>

                                <div class="file-input">
                                    <label for="imageUpload">Signature Image</label>
                                    <div class="file-input-container">
                                        <i class="fas fa-signature"></i>
                                        <p><strong>Upload signature</strong> for your certificate</p>
                                        <p>PNG with transparent background recommended</p>
                                        <input type="file" id="imageUpload" accept="image/*">
                                    </div>
                                    <div class="file-name" id="signatureFileName"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Signature Size</label>
                                    <div class="position-controls">
                                        <div>
                                            <label for="imgWidth">Width</label>
                                            <input type="number" id="imgWidth" value="200" min="50" max="1000">
                                        </div>
                                        <div>
                                            <label for="imgHeight">Height</label>
                                            <input type="number" id="imgHeight" value="100" min="50" max="1000">
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden inputs for positioning -->
                                <input type="hidden" id="nameInput" placeholder="Enter recipient's name">
                                <input type="hidden" id="dateInput">
                                <input type="hidden" id="sigX" value="100">
                                <input type="hidden" id="sigY" value="500">
                            </div>

                            <div class="preview-section">
                                <div class="canvas-container">
                                    <canvas id="certificateCanvas"></canvas>
                                </div>

                                <div class="button-group">
                                    <button onclick="saveCertificate()" class="save-btn">
                                        <i class="fas fa-arrow-right"></i> Next: Position Text
                                    </button>
                                </div>
                                <div id="statusMessage" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variables
    let canvas = document.getElementById("certificateCanvas");
    let ctx = canvas.getContext("2d");
    let img = new Image();
    let lowerImage = new Image();
    let hasTemplate = false;

   // Text formatting variables
    let leftMarginSize = 50;
    let rightMarginSize = 50;
    let lineSpacing = 1.3;
    let showGridlines = false;
    let showMarginLines = true;
    let showCenterLines = false;

    // Debouncing variables
    let drawTextTimeout = null;
    const DEBOUNCE_DELAY = 16;

    // Element positions and bounds
    let elements = {
        name: { x: null, y: null, text: "", fontSize: 40, fontFamily: "Poppins, Arial, sans-serif", color: "#000000", bold: false, bounds: null },
        description: { x: null, y: null, text: "", fontSize: 24, fontFamily: "Poppins, Arial, sans-serif", color: "#000000", lines: [], bounds: null, formattedContent: [] },
        date: { x: null, y: null, text: "", fontFamily: "Poppins, Arial, sans-serif", color: "#000000", bounds: null },
        signature: { x: 100, y: 500, width: 200, height: 100, bounds: null }
    };

    // Dragging state
    let isDragging = false;
    let selectedElement = null;
    let dragOffset = { x: 0, y: 0 };
    let hoveredElement = null;

    // Initialize canvas with default size and positions
    function initializeCanvas() {
        if (!canvas.width || canvas.width === 0) {
            canvas.width = 800;
            canvas.height = 600;
        }
        
        // Set default positions if not set
        if (elements.name.x === null) {
            elements.name.x = canvas.width / 2;
            elements.name.y = canvas.height / 2 - 50;
            elements.description.x = canvas.width / 2;
            elements.description.y = canvas.height / 2 + 50;
            elements.date.x = canvas.width - 150;
            elements.date.y = canvas.height - 100;
        }
        
        // Initial draw
        drawText();
    }

    // Improved text wrapping function with proper margin handling and newline support
    function wrapText(text, fontSize, fontFamily, maxWidth) {
        ctx.font = `${fontSize}px ${fontFamily}`;
        
        // Handle text with explicit newlines
        const paragraphs = text.split('\n');
        const lines = [];
        
        // Process each paragraph separately
        paragraphs.forEach(paragraph => {
            if (paragraph.trim() === '') {
                // Add empty line for blank paragraphs (double newlines)
                lines.push('');
                return;
            }
            
            const words = paragraph.split(' ');
            if (words.length === 0) return;
            
            let currentLine = words[0];
            
            for (let i = 1; i < words.length; i++) {
                const word = words[i];
                const testLine = currentLine + " " + word;
                const testWidth = ctx.measureText(testLine).width;
                
                if (testWidth <= maxWidth) {
                    currentLine = testLine;
                } else {
                    lines.push(currentLine);
                    currentLine = word;
                }
            }
            
            lines.push(currentLine);
        });
        
        return lines;
    }

    // Improved function to draw formatted text with proper wrapping and formatting
    function drawFormattedTextWithWrapping(segments, x, y, fontSize, fontFamily, color) {
        if (!segments || segments.length === 0) return { width: 0, height: 0 };

        // Calculate available width based on margins
        const availableWidth = canvas.width - leftMarginSize - rightMarginSize;
        
        // Process segments to handle formatting and newlines
        let formattedLines = [];
        let currentLine = [];
        let currentLineWidth = 0;
        let maxWidth = 0;
        
        // First pass: split segments by newlines and create initial lines
        segments.forEach(segment => {
            // Handle newlines within segments
            const parts = segment.text.split('\n');
            
            parts.forEach((part, index) => {
                // If not the first part, start a new line
                if (index > 0) {
                    if (currentLine.length > 0) {
                        formattedLines.push(currentLine);
                        maxWidth = Math.max(maxWidth, currentLineWidth);
                    }
                    currentLine = [];
                    currentLineWidth = 0;
                }
                
                // Skip empty parts (but still create the line break)
                if (part.trim() === '') return;
                
                // Add the segment with its formatting
                const formattedSegment = {
                    text: part,
                    bold: segment.bold,
                    italic: segment.italic,
                    underline: segment.underline
                };
                
                // Set appropriate font for measurement
                let fontStyle = '';
                if (formattedSegment.bold) fontStyle += 'bold ';
                if (formattedSegment.italic) fontStyle += 'italic ';
                ctx.font = `${fontStyle}${fontSize}px ${fontFamily}`;
                
                const segmentWidth = ctx.measureText(part).width;
                
                // Check if adding this segment would exceed available width
                if (currentLineWidth + segmentWidth > availableWidth && currentLine.length > 0) {
                    // This segment needs to start a new line
                    formattedLines.push(currentLine);
                    maxWidth = Math.max(maxWidth, currentLineWidth);
                    currentLine = [formattedSegment];
                    currentLineWidth = segmentWidth;
                } else {
                    // Add to current line
                    currentLine.push(formattedSegment);
                    currentLineWidth += segmentWidth;
                }
            });
        });
        
        // Add the last line if not empty
        if (currentLine.length > 0) {
            formattedLines.push(currentLine);
            maxWidth = Math.max(maxWidth, currentLineWidth);
        }
        
        // Second pass: word wrapping for each line
        let finalLines = [];
        formattedLines.forEach(line => {
            // Combine all segments in the line to perform word wrapping
            let combinedText = line.map(segment => segment.text).join(' ');
            let wrappedLines = wrapText(combinedText, fontSize, fontFamily, availableWidth);
            
            // Add each wrapped line with the formatting of the original line
            wrappedLines.forEach(wrappedLine => {
                finalLines.push({
                    text: wrappedLine,
                    formatting: line.map(segment => ({
                        bold: segment.bold,
                        italic: segment.italic,
                        underline: segment.underline
                    }))
                });
            });
        });
        
        // Draw each line with its formatting
        const lineHeight = fontSize * lineSpacing;
        finalLines.forEach((line, lineIndex) => {
            const currentY = y + (lineIndex * lineHeight);
            
            // Set text alignment
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            
            // Apply formatting to the whole line
            let hasFormatting = line.formatting && line.formatting.some(format => 
                format.bold || format.italic || format.underline);
            
            if (hasFormatting) {
                // If line has formatting, we need to apply it
                let fontStyle = '';
                if (line.formatting.some(f => f.bold)) fontStyle += 'bold ';
                if (line.formatting.some(f => f.italic)) fontStyle += 'italic ';
                ctx.font = `${fontStyle}${fontSize}px ${fontFamily}`;
                
                // Draw the text
                ctx.fillStyle = color;
                ctx.fillText(line.text, x, currentY);
                
                // Add underline if needed
                if (line.formatting.some(f => f.underline)) {
                    const textWidth = ctx.measureText(line.text).width;
                    const underlineY = currentY + fontSize * 0.15;
                    
                    ctx.beginPath();
                    ctx.moveTo(x - textWidth / 2, underlineY);
                    ctx.lineTo(x + textWidth / 2, underlineY);
                    ctx.strokeStyle = color;
                    ctx.lineWidth = fontSize * 0.05;
                    ctx.stroke();
                }
            } else {
                // Simple rendering for unformatted text
                ctx.font = `${fontSize}px ${fontFamily}`;
                ctx.fillStyle = color;
                ctx.fillText(line.text, x, currentY);
            }
            
            // Update max width
            const lineWidth = ctx.measureText(line.text).width;
            maxWidth = Math.max(maxWidth, lineWidth);
        });

        return {
            width: maxWidth,
            height: lineHeight * finalLines.length
        };
    }

    // Function to draw grid lines with center lines
    function drawGridLines() {
        if (!showGridlines && !showMarginLines && !showCenterLines) return;

        ctx.save();

        // Draw regular grid lines
        if (showGridlines) {
            ctx.strokeStyle = '#e0e0e0';
            ctx.lineWidth = 1;
            ctx.setLineDash([5, 5]);

            // Draw grid lines every 50px
            for (let x = 0; x <= canvas.width; x += 50) {
                ctx.beginPath();
                ctx.moveTo(x, 0);
                ctx.lineTo(x, canvas.height);
                ctx.stroke();
            }
            
            for (let y = 0; y <= canvas.height; y += 50) {
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(canvas.width, y);
                ctx.stroke();
            }
        }

        // Draw margin lines (only in preview, not in saved certificate)
        if (showMarginLines) {
            ctx.strokeStyle = '#ff6b6b';
            ctx.lineWidth = 2;
            ctx.setLineDash([10, 5]);
            
            // Left margin
            ctx.beginPath();
            ctx.moveTo(leftMarginSize, 0);
            ctx.lineTo(leftMarginSize, canvas.height);
            ctx.stroke();
            
            // Right margin
            ctx.beginPath();
            ctx.moveTo(canvas.width - rightMarginSize, 0);
            ctx.lineTo(canvas.width - rightMarginSize, canvas.height);
            ctx.stroke();
        }

        // Draw center lines
        if (showCenterLines) {
            ctx.strokeStyle = '#4cc9f0';
            ctx.lineWidth = 2;
            ctx.setLineDash([15, 10]);
            
            // Horizontal center line
            ctx.beginPath();
            ctx.moveTo(0, canvas.height / 2);
            ctx.lineTo(canvas.width, canvas.height / 2);
            ctx.stroke();
            
            // Vertical center line
            ctx.beginPath();
            ctx.moveTo(canvas.width / 2, 0);
            ctx.lineTo(canvas.width / 2, canvas.height);
            ctx.stroke();
        }

        ctx.restore();
    }

    // Improved text formatting setup with better event handling
    function setupTextFormatting() {
        const descriptionInput = document.getElementById("descriptionInput");
        const boldBtn = document.getElementById("boldBtn");
        const italicBtn = document.getElementById("italicBtn");
        const underlineBtn = document.getElementById("underlineBtn");
        const clearFormatBtn = document.getElementById("clearFormatBtn");

        if (!descriptionInput) return;

        if (boldBtn) {
            boldBtn.addEventListener('click', (e) => {
                e.preventDefault();
                descriptionInput.focus();
                document.execCommand('bold', false, null);
                updateFormatButtons();
                setTimeout(drawText, 10);
            });
        }

        if (italicBtn) {
            italicBtn.addEventListener('click', (e) => {
                e.preventDefault();
                descriptionInput.focus();
                document.execCommand('italic', false, null);
                updateFormatButtons();
                setTimeout(drawText, 10);
            });
        }

        if (underlineBtn) {
            underlineBtn.addEventListener('click', (e) => {
                e.preventDefault();
                descriptionInput.focus();
                document.execCommand('underline', false, null);
                updateFormatButtons();
                setTimeout(drawText, 10);
            });
        }

        if (clearFormatBtn) {
            clearFormatBtn.addEventListener('click', (e) => {
                e.preventDefault();
                descriptionInput.focus();
                document.execCommand('removeFormat', false, null);
                updateFormatButtons();
                setTimeout(drawText, 10);
            });
        }

        descriptionInput.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key.toLowerCase()) {
                    case 'b':
                        e.preventDefault();
                        document.execCommand('bold', false, null);
                        updateFormatButtons();
                        setTimeout(drawText, 10);
                        break;
                    case 'i':
                        e.preventDefault();
                        document.execCommand('italic', false, null);
                        updateFormatButtons();
                        setTimeout(drawText, 10);
                        break;
                    case 'u':
                        e.preventDefault();
                        document.execCommand('underline', false, null);
                        updateFormatButtons();
                        setTimeout(drawText, 10);
                        break;
                }
            }
        });

        descriptionInput.addEventListener('input', () => setTimeout(drawText, 10));
        descriptionInput.addEventListener('keyup', () => {
            updateFormatButtons();
            setTimeout(drawText, 10);
        });
        descriptionInput.addEventListener('paste', () => {
            setTimeout(() => {
                updateFormatButtons();
                drawText();
            }, 50);
        });

        descriptionInput.addEventListener('mouseup', updateFormatButtons);
        descriptionInput.addEventListener('focus', updateFormatButtons);
        
        document.addEventListener('selectionchange', () => {
            if (document.activeElement === descriptionInput) {
                updateFormatButtons();
            }
        });
    }

    function updateFormatButtons() {
        const boldBtn = document.getElementById("boldBtn");
        const italicBtn = document.getElementById("italicBtn");
        const underlineBtn = document.getElementById("underlineBtn");

        if (boldBtn) boldBtn.classList.toggle('active', document.queryCommandState('bold'));
        if (italicBtn) italicBtn.classList.toggle('active', document.queryCommandState('italic'));
        if (underlineBtn) underlineBtn.classList.toggle('active', document.queryCommandState('underline'));
    }

    // Improved function to extract formatted text from contenteditable div
    function extractFormattedText(element) {
        if (!element) return [];
        
        const children = element.childNodes;
        const formattedSegments = [];

        function processNode(node) {
            if (node.nodeType === Node.TEXT_NODE) {
                return [{
                    text: node.textContent,
                    bold: false,
                    italic: false,
                    underline: false
                }];
            } else if (node.nodeType === Node.ELEMENT_NODE) {
                const segments = [];
                const isBold = node.tagName === 'B' || node.tagName === 'STRONG' ||
                              node.style.fontWeight === 'bold' || node.style.fontWeight === '700';
                const isItalic = node.tagName === 'I' || node.tagName === 'EM' ||
                               node.style.fontStyle === 'italic';
                const isUnderline = node.tagName === 'U' || 
                                  (node.style.textDecoration && node.style.textDecoration.includes('underline'));

                for (let child of node.childNodes) {
                    const childSegments = processNode(child);
                    childSegments.forEach(segment => {
                        segments.push({
                            text: segment.text,
                            bold: segment.bold || isBold,
                            italic: segment.italic || isItalic,
                            underline: segment.underline || isUnderline
                        });
                    });
                }

                // Handle line breaks properly
                if (node.tagName === 'BR') {
                    segments.push({ text: '\n', bold: false, italic: false, underline: false });
                } else if (node.tagName === 'DIV' || node.tagName === 'P') {
                    // For block elements, add a newline if it's not the first element
                    if (node.previousElementSibling) {
                        segments.unshift({ text: '\n', bold: false, italic: false, underline: false });
                    }
                }

                return segments;
            }
            return [];
        }

        for (let child of children) {
            formattedSegments.push(...processNode(child));
        }

        return formattedSegments;
    }

    // Debounced version of drawText for input events
    function debouncedDrawText() {
        clearTimeout(drawTextTimeout);
        drawTextTimeout = setTimeout(() => {
            drawText();
        }, DEBOUNCE_DELAY);
    }

    // Immediate version for interactions (no delay)
    function immediateDrawText() {
        clearTimeout(drawTextTimeout);
        drawText();
    }

    // Main drawing function with proper text wrapping and formatting
    function drawText() {
        // Always clear and redraw the canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (hasTemplate) {
            // Draw template if available
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        } else {
            // Set a light background if no template
            ctx.fillStyle = "#f8f9fa";
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Add a border
            ctx.strokeStyle = "#dee2e6";
            ctx.lineWidth = 2;
            ctx.strokeRect(0, 0, canvas.width, canvas.height);
        }

        // Draw grid lines if enabled (only in preview mode)
        drawGridLines();

        // Get values from form
        const nameInput = document.getElementById("nameInput");
        if (nameInput) {
            elements.name.text = nameInput.value;
        }

        // Extract formatted content from contenteditable div
        const descriptionDiv = document.getElementById("descriptionInput");
        if (descriptionDiv) {
            elements.description.formattedContent = extractFormattedText(descriptionDiv);
            elements.description.text = descriptionDiv.textContent || descriptionDiv.innerText || "";
        }

        const dateInput = document.getElementById("dateInput");
        if (dateInput && dateInput.value) {
            elements.date.text = new Date(dateInput.value).toLocaleDateString('en-US', {
                year: 'numeric', month: 'long', day: 'numeric'
            });
        }

        // Update font settings from controls
        const fontSizeInput = document.getElementById("fontSize");
        const fontFamilyInput = document.getElementById("fontFamily");
        const textColorInput = document.getElementById("textColor");
        const lineSpacingInput = document.getElementById("lineSpacing");

        if (fontSizeInput) {
            elements.name.fontSize = parseInt(fontSizeInput.value) || 40;
            elements.description.fontSize = elements.name.fontSize * 0.6;
        }

        if (fontFamilyInput) {
            elements.name.fontFamily = fontFamilyInput.value;
            elements.description.fontFamily = fontFamilyInput.value;
            elements.date.fontFamily = fontFamilyInput.value;
        }

        if (textColorInput) {
            elements.name.color = textColorInput.value;
            elements.description.color = textColorInput.value;
            elements.date.color = textColorInput.value;
        }

        if (lineSpacingInput) {
            lineSpacing = parseFloat(lineSpacingInput.value) || 1.3;
        }

        // Update signature dimensions
        const imgWidthInput = document.getElementById("imgWidth");
        const imgHeightInput = document.getElementById("imgHeight");
        if (imgWidthInput) {
            elements.signature.width = parseInt(imgWidthInput.value) || 200;
        }
        if (imgHeightInput) {
            elements.signature.height = parseInt(imgHeightInput.value) || 100;
        }

        // Draw name text
        if (elements.name.text) {
            // Draw selection highlight if this element is selected or hovered
            if (selectedElement === 'name' || hoveredElement === 'name') {
                ctx.save();
                ctx.globalAlpha = 0.3;
                ctx.fillStyle = selectedElement === 'name' ? '#007bff' : '#28a745';
                const nameBounds = calculateTextBounds(elements.name, elements.name.text,
                    elements.name.x, elements.name.y, elements.name.fontSize, elements.name.fontFamily);
                if (nameBounds) {
                    ctx.fillRect(nameBounds.x - 5, nameBounds.y - 5, nameBounds.width + 10, nameBounds.height + 10);
                }
                ctx.restore();
            }

            // Draw name text
            if (elements.name.bold) {
                ctx.font = `bold ${elements.name.fontSize}px ${elements.name.fontFamily}`;
            } else {
                ctx.font = `${elements.name.fontSize}px ${elements.name.fontFamily}`;
            }
            ctx.fillStyle = elements.name.color;
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            ctx.fillText(elements.name.text, elements.name.x, elements.name.y);

            // Calculate and store bounds
            elements.name.bounds = calculateTextBounds(elements.name, elements.name.text,
                elements.name.x, elements.name.y, elements.name.fontSize, elements.name.fontFamily);
        }

        // Draw formatted description with proper wrapping
        if (elements.description.formattedContent && elements.description.formattedContent.length > 0) {
            // Draw selection highlight if this element is selected or hovered
            if (selectedElement === 'description' || hoveredElement === 'description') {
                ctx.save();
                ctx.globalAlpha = 0.3;
                ctx.fillStyle = selectedElement === 'description' ? '#007bff' : '#28a745';
                const descBounds = calculateFormattedTextBounds(elements.description.formattedContent,
                    elements.description.x, elements.description.y, elements.description.fontSize, elements.description.fontFamily);
                if (descBounds) {
                    ctx.fillRect(descBounds.x - 5, descBounds.y - 5, descBounds.width + 10, descBounds.height + 10);
                }
                ctx.restore();
            }

            // Draw formatted description with wrapping
            const textDimensions = drawFormattedTextWithWrapping(
                elements.description.formattedContent,
                elements.description.x,
                elements.description.y,
                elements.description.fontSize,
                elements.description.fontFamily,
                elements.description.color
            );

            // Calculate and store bounds
            elements.description.bounds = {
                x: elements.description.x - textDimensions.width / 2,
                y: elements.description.y - textDimensions.height / 2,
                width: textDimensions.width,
                height: textDimensions.height
            };
        }

        // Draw date text
        if (elements.date.text) {
            // Draw selection highlight if this element is selected or hovered
            if (selectedElement === 'date' || hoveredElement === 'date') {
                ctx.save();
                ctx.globalAlpha = 0.3;
                ctx.fillStyle = selectedElement === 'date' ? '#007bff' : '#28a745';
                const dateBounds = calculateTextBounds(elements.date, elements.date.text,
                    elements.date.x, elements.date.y, elements.description.fontSize, elements.date.fontFamily);
                if (dateBounds) {
                    ctx.fillRect(dateBounds.x - 5, dateBounds.y - 5, dateBounds.width + 10, dateBounds.height + 10);
                }
                ctx.restore();
            }

            // Draw date text
            ctx.textAlign = "right";
            ctx.textBaseline = "middle";
            ctx.font = `${elements.description.fontSize}px ${elements.date.fontFamily}`;
            ctx.fillStyle = elements.date.color;
            ctx.fillText(elements.date.text, elements.date.x, elements.date.y);

            // Calculate and store bounds (adjust for right alignment)
            ctx.font = `${elements.description.fontSize}px ${elements.date.fontFamily}`;
            const metrics = ctx.measureText(elements.date.text);
            elements.date.bounds = {
                x: elements.date.x - metrics.width,
                y: elements.date.y - elements.description.fontSize / 2,
                width: metrics.width,
                height: elements.description.fontSize
            };
        }

        // Draw signature (if lowerImage exists)
        if (typeof lowerImage !== 'undefined' && lowerImage.complete && lowerImage.src) {
            // Draw selection highlight if this element is selected or hovered
            if (selectedElement === 'signature' || hoveredElement === 'signature') {
                ctx.save();
                ctx.globalAlpha = 0.3;
                ctx.fillStyle = selectedElement === 'signature' ? '#007bff' : '#28a745';
                ctx.fillRect(elements.signature.x - 5, elements.signature.y - 5,
                    elements.signature.width + 10, elements.signature.height + 10);
                ctx.restore();
            }

            // Draw signature
            ctx.drawImage(
                lowerImage,
                elements.signature.x,
                elements.signature.y,
                elements.signature.width,
                elements.signature.height
            );

            // Store bounds
            elements.signature.bounds = {
                x: elements.signature.x,
                y: elements.signature.y,
                width: elements.signature.width,
                height: elements.signature.height
            };
        }
    }

    // Function to calculate text bounds
    function calculateTextBounds(element, text, x, y, fontSize, fontFamily) {
        ctx.font = `${fontSize}px ${fontFamily}`;
        const metrics = ctx.measureText(text);
        const textHeight = fontSize;

        return {
            x: x - metrics.width / 2,
            y: y - textHeight / 2,
            width: metrics.width,
            height: textHeight
        };
    }

        // Improved function to calculate formatted text bounds with proper wrapping
    function calculateFormattedTextBounds(segments, x, y, fontSize, fontFamily) {
        if (!segments || segments.length === 0) return null;

        // Calculate available width based on margins
        const availableWidth = canvas.width - leftMarginSize - rightMarginSize;
        
        // Process segments to handle formatting and newlines
        let formattedLines = [];
        let currentLine = [];
        let currentLineWidth = 0;
        let maxWidth = 0;
        
        // First pass: split segments by newlines and create initial lines
        segments.forEach(segment => {
            // Handle newlines within segments
            const parts = segment.text.split('\n');
            
            parts.forEach((part, index) => {
                // If not the first part, start a new line
                if (index > 0) {
                    if (currentLine.length > 0) {
                        formattedLines.push(currentLine);
                        maxWidth = Math.max(maxWidth, currentLineWidth);
                    }
                    currentLine = [];
                    currentLineWidth = 0;
                }
                
                // Skip empty parts (but still create the line break)
                if (part.trim() === '') return;
                
                // Add the segment with its formatting
                const formattedSegment = {
                    text: part,
                    bold: segment.bold,
                    italic: segment.italic,
                    underline: segment.underline
                };
                
                // Set appropriate font for measurement
                let fontStyle = '';
                if (formattedSegment.bold) fontStyle += 'bold ';
                if (formattedSegment.italic) fontStyle += 'italic ';
                ctx.font = `${fontStyle}${fontSize}px ${fontFamily}`;
                
                const segmentWidth = ctx.measureText(part).width;
                
                // Check if adding this segment would exceed available width
                if (currentLineWidth + segmentWidth > availableWidth && currentLine.length > 0) {
                    // This segment needs to start a new line
                    formattedLines.push(currentLine);
                    maxWidth = Math.max(maxWidth, currentLineWidth);
                    currentLine = [formattedSegment];
                    currentLineWidth = segmentWidth;
                } else {
                    // Add to current line
                    currentLine.push(formattedSegment);
                    currentLineWidth += segmentWidth;
                }
            });
        });
        
        // Add the last line if not empty
        if (currentLine.length > 0) {
            formattedLines.push(currentLine);
            maxWidth = Math.max(maxWidth, currentLineWidth);
        }
        
        // Second pass: word wrapping for each line
        let finalLines = [];
        formattedLines.forEach(line => {
            // Combine all segments in the line to perform word wrapping
            let combinedText = line.map(segment => segment.text).join(' ');
            let wrappedLines = wrapText(combinedText, fontSize, fontFamily, availableWidth);
            
            // Add each wrapped line with the formatting of the original line
            wrappedLines.forEach(wrappedLine => {
                finalLines.push({
                    text: wrappedLine,
                    formatting: line.map(segment => ({
                        bold: segment.bold,
                        italic: segment.italic,
                        underline: segment.underline
                    }))
                });
            });
        });
        
        const lineHeight = fontSize * lineSpacing;
        const totalHeight = lineHeight * finalLines.length;
        
        return {
            x: x - maxWidth / 2,
            y: y - totalHeight / 2,
            width: maxWidth,
            height: totalHeight
        };
    }

    // Function to check if point is inside bounds
    function isPointInBounds(x, y, bounds) {
        if (!bounds) return false;
        return x >= bounds.x && x <= bounds.x + bounds.width &&
               y >= bounds.y && y <= bounds.y + bounds.height;
    }

    // Function to get canvas coordinates from mouse event
    function getCanvasCoordinates(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;

        return {
            x: (e.clientX - rect.left) * scaleX,
            y: (e.clientY - rect.top) * scaleY
        };
    }

    // Function to find element at coordinates
    function getElementAtCoordinates(x, y) {
        // Check signature first (if it exists)
        if (lowerImage.src && elements.signature.bounds &&
            isPointInBounds(x, y, elements.signature.bounds)) {
            return 'signature';
        }

        // Check text elements
        if (elements.name.text && elements.name.bounds &&
            isPointInBounds(x, y, elements.name.bounds)) {
            return 'name';
        }

        if (elements.description.formattedContent && elements.description.bounds &&
            isPointInBounds(x, y, elements.description.bounds)) {
            return 'description';
        }

        if (elements.date.text && elements.date.bounds &&
            isPointInBounds(x, y, elements.date.bounds)) {
            return 'date';
        }

        return null;
    }

    // Setup formatting panel controls with proper centering
    function setupFormattingPanelControls() {
        // Margin controls
        const leftMarginSlider = document.getElementById("leftMargin");
        const leftMarginValue = document.getElementById("leftMarginValue");
        const rightMarginSlider = document.getElementById("rightMargin");
        const rightMarginValue = document.getElementById("rightMarginValue");

        if (leftMarginSlider && leftMarginValue) {
            leftMarginSlider.addEventListener('input', function() {
                leftMarginSize = parseInt(this.value);
                leftMarginValue.value = leftMarginSize;
                immediateDrawText();
            });

            leftMarginValue.addEventListener('input', function() {
                leftMarginSize = parseInt(this.value);
                leftMarginSlider.value = leftMarginSize;
                immediateDrawText();
            });
        }

        if (rightMarginSlider && rightMarginValue) {
            rightMarginSlider.addEventListener('input', function() {
                rightMarginSize = parseInt(this.value);
                rightMarginValue.value = rightMarginSize;
                immediateDrawText();
            });

            rightMarginValue.addEventListener('input', function() {
                rightMarginSize = parseInt(this.value);
                rightMarginSlider.value = rightMarginSize;
                immediateDrawText();
            });
        }

        // Alignment controls
        const centerHorizontalBtn = document.getElementById("centerHorizontalBtn");
        const centerVerticalBtn = document.getElementById("centerVerticalBtn");

        if (centerHorizontalBtn) {
            centerHorizontalBtn.addEventListener('click', function() {
                if (selectedElement && elements[selectedElement]) {
                    elements[selectedElement].x = canvas.width / 2;
                    immediateDrawText();
                } else {
                    // If no element is selected, center all text elements horizontally
                    elements.name.x = canvas.width / 2;
                    elements.description.x = canvas.width / 2;
                    immediateDrawText();
                }
            });
        }

        if (centerVerticalBtn) {
            centerVerticalBtn.addEventListener('click', function() {
                if (selectedElement && elements[selectedElement]) {
                    elements[selectedElement].y = canvas.height / 2;
                    immediateDrawText();
                } else {
                    // If no element is selected, center all text elements vertically
                    elements.name.y = canvas.height / 2 - 30;
                    elements.description.y = canvas.height / 2 + 30;
                    immediateDrawText();
                }
            });
        }

        // Grid controls
        const showGridlinesCheckbox = document.getElementById("showGridlines");
        const showMarginLinesCheckbox = document.getElementById("showMarginLines");
        const showCenterLinesCheckbox = document.getElementById("showCenterLines");

        if (showGridlinesCheckbox) {
            showGridlinesCheckbox.addEventListener('change', function() {
                showGridlines = this.checked;
                immediateDrawText();
            });
        }

        if (showMarginLinesCheckbox) {
            showMarginLinesCheckbox.addEventListener('change', function() {
                showMarginLines = this.checked;
                immediateDrawText();
            });
        }

        if (showCenterLinesCheckbox) {
            showCenterLinesCheckbox.addEventListener('change', function() {
                showCenterLines = this.checked;
                immediateDrawText();
            });
        }

        // Font and formatting controls
        const fontFamilySelect = document.getElementById("fontFamily");
        const fontSizeSelect = document.getElementById("fontSize");
        const textColorInput = document.getElementById("textColor");
        const lineSpacingSelect = document.getElementById("lineSpacing");

        if (fontFamilySelect) {
            fontFamilySelect.addEventListener("change", immediateDrawText);
        }

        if (fontSizeSelect) {
            fontSizeSelect.addEventListener("change", immediateDrawText);
        }

        if (textColorInput) {
            textColorInput.addEventListener("change", immediateDrawText);
        }

        if (lineSpacingSelect) {
            lineSpacingSelect.addEventListener("change", function() {
                lineSpacing = parseFloat(this.value) || 1.3;
                immediateDrawText();
            });
        }
    }

    // Mouse event handlers
    canvas.addEventListener("mousedown", function(e) {
        const coords = getCanvasCoordinates(e);
        const elementAtPoint = getElementAtCoordinates(coords.x, coords.y);

        if (elementAtPoint) {
            isDragging = true;
            selectedElement = elementAtPoint;

            // Calculate drag offset
            dragOffset.x = coords.x - elements[selectedElement].x;
            dragOffset.y = coords.y - elements[selectedElement].y;

            canvas.style.cursor = 'grabbing';
            immediateDrawText();
        }
    });

    canvas.addEventListener("mousemove", function(e) {
        const coords = getCanvasCoordinates(e);

        if (isDragging && selectedElement) {
            // Update element position
            elements[selectedElement].x = coords.x - dragOffset.x;
            elements[selectedElement].y = coords.y - dragOffset.y;

            // Update input fields if it's signature
            if (selectedElement === 'signature') {
                const sigXInput = document.getElementById("sigX");
                const sigYInput = document.getElementById("sigY");
                if (sigXInput) sigXInput.value = Math.round(elements.signature.x);
                if (sigYInput) sigYInput.value = Math.round(elements.signature.y);
            }

            // Redraw immediately for smooth dragging
            immediateDrawText();
        } else {
            // Handle hover effects - ONLY when not dragging
            const elementAtPoint = getElementAtCoordinates(coords.x, coords.y);

            if (elementAtPoint !== hoveredElement) {
                hoveredElement = elementAtPoint;

                if (hoveredElement) {
                    canvas.style.cursor = 'grab';
                    canvas.title = `Click and drag to move ${hoveredElement}`;
                } else {
                    canvas.style.cursor = 'default';
                    canvas.title = '';
                }

                // Only redraw for hover if we have content to show
                if (elements.name.text || elements.description.text || elements.date.text) {
                    immediateDrawText();
                }
            }
        }
    });

    canvas.addEventListener("mouseup", function() {
        if (isDragging) {
            isDragging = false;
            canvas.style.cursor = hoveredElement ? 'grab' : 'default';
            immediateDrawText();
        }
    });

    canvas.addEventListener("mouseleave", function() {
        isDragging = false;
        selectedElement = null;
        hoveredElement = null;
        canvas.style.cursor = 'default';
        canvas.title = '';
        immediateDrawText();
    });

    // File upload event listeners
    const uploadInput = document.getElementById("upload");
    if (uploadInput) {
        uploadInput.addEventListener("change", function(event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    hasTemplate = true;

                    // Display the file name
                    const templateFileName = document.getElementById("templateFileName");
                    if (templateFileName) {
                        templateFileName.textContent = file.name;
                        templateFileName.style.display = "block";
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    const imageUploadInput = document.getElementById("imageUpload");
    if (imageUploadInput) {
        imageUploadInput.addEventListener("change", function(event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    lowerImage.src = e.target.result;
                    lowerImage.onload = function() {
                        immediateDrawText();
                    };

                    // Display the file name
                    const signatureFileName = document.getElementById("signatureFileName");
                    if (signatureFileName) {
                        signatureFileName.textContent = file.name;
                        signatureFileName.style.display = "block";
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    img.onload = function() {
        // Set canvas to original image size
        canvas.width = img.width;
        canvas.height = img.height;

        // Auto-fit canvas to container
        fitCanvasToContainer();

        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

        // Set default positions based on canvas size
        if (elements.name.x === null) {
            elements.name.x = canvas.width / 2;
            elements.name.y = canvas.height / 2;
            elements.description.x = canvas.width / 2;
            elements.description.y = canvas.height / 2 + 50;
            elements.date.x = canvas.width - 50;
            elements.date.y = 100;
        }

        immediateDrawText();
    };

    // Function to fit canvas to container while maintaining aspect ratio
    function fitCanvasToContainer() {
        const container = document.querySelector('.canvas-container');
        if (!container) return;
        
        const containerWidth = container.clientWidth - 40;
        const containerHeight = container.clientHeight - 40;

        const scaleX = containerWidth / canvas.width;
        const scaleY = containerHeight / canvas.height;
        const scale = Math.min(scaleX, scaleY, 1);

        const scaledWidth = canvas.width * scale;
        const scaledHeight = canvas.height * scale;

        canvas.style.width = scaledWidth + 'px';
        canvas.style.height = scaledHeight + 'px';
    }

    // Add event listeners for signature position inputs
    const sigXInput = document.getElementById("sigX");
    if (sigXInput) {
        sigXInput.addEventListener("change", function(e) {
                        elements.signature.x = parseInt(e.target.value);
            immediateDrawText();
        });
    }

    const sigYInput = document.getElementById("sigY");
    if (sigYInput) {
        sigYInput.addEventListener("change", function(e) {
            elements.signature.y = parseInt(e.target.value);
            immediateDrawText();
        });
    }

    const imgWidthInput = document.getElementById("imgWidth");
    if (imgWidthInput) {
        imgWidthInput.addEventListener("change", function(e) {
            elements.signature.width = parseInt(e.target.value);
            immediateDrawText();
        });
    }

    const imgHeightInput = document.getElementById("imgHeight");
    if (imgHeightInput) {
        imgHeightInput.addEventListener("change", function(e) {
            elements.signature.height = parseInt(e.target.value);
            immediateDrawText();
        });
    }

    // Save certificate function - creates clean version without grid lines
    function saveCertificate() {
        if (!hasTemplate) {
            showStatusMessage("Please upload a certificate template first", "error");
            return;
        }

        // Get certificate name
        const certificateName = document.getElementById("certificateName").value.trim();
        if (!certificateName) {
            showStatusMessage("Please enter a certificate name", "error");
            return;
        }

        // Get certificate file description (the one stored in the database)
        const certificateFileDescription = document.getElementById("certificateFileDescription").value;

        // Get certificate content description (extract plain text from contenteditable)
        const descriptionDiv = document.getElementById("descriptionInput");
        const certificateContentDescription = descriptionDiv.textContent || descriptionDiv.innerText || "";

        // Create a clean version without grid lines for saving
        const tempShowGridlines = showGridlines;
        const tempShowMarginLines = showMarginLines;
        const tempShowCenterLines = showCenterLines;
        
        // Temporarily disable all grid lines
        showGridlines = false;
        showMarginLines = false;
        showCenterLines = false;
        
        // Redraw without grid lines
        drawText();
        
        // Get canvas data as base64 string
        const imageData = canvas.toDataURL('image/png');
        
        // Restore grid line settings
        showGridlines = tempShowGridlines;
        showMarginLines = tempShowMarginLines;
        showCenterLines = tempShowCenterLines;
        
        // Redraw with original grid settings
        drawText();

        // Show loading indicator
        showStatusMessage("Processing... Saving certificate...", "loading");

        // Create data object
        const data = {
            name: certificateName,
            file_description: certificateFileDescription,
            content_description: certificateContentDescription,
            image_data: imageData
        };

        // Send to server using fetch API
        fetch('save_certificate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showStatusMessage(data.message, "success");

                // Redirect to certificate_name&id_pos.php with the certificate ID
                setTimeout(() => {
                    window.location.href = 'certificate_name&id_pos.php?cert_id=' + data.certificate_id;
                }, 1500);
            } else {
                showStatusMessage(data.message, "error");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showStatusMessage("Failed to save certificate. Please try again.", "error");
        });
    }

    function showStatusMessage(message, type) {
        const statusDiv = document.getElementById('statusMessage');
        if (!statusDiv) return;
        
        statusDiv.style.display = 'block';

        if (type === "success") {
            statusDiv.style.backgroundColor = '#d4edda';
            statusDiv.style.color = '#155724';
            statusDiv.innerHTML = `<i class="fas fa-check-circle"></i> <strong>Success!</strong> ${message}`;
        } else if (type === "error") {
            statusDiv.style.backgroundColor = '#f8d7da';
            statusDiv.style.color = '#721c24';
            statusDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> <strong>Error!</strong> ${message}`;
        } else if (type === "loading") {
            statusDiv.style.backgroundColor = '#e2e3e5';
            statusDiv.style.color = '#383d41';
            statusDiv.innerHTML = `<i class="fas fa-spinner fa-spin"></i> <strong>Processing...</strong> ${message}`;
        }
    }

    // Window resize handler to maintain canvas fit
    window.addEventListener('resize', function() {
        if (hasTemplate) {
            setTimeout(() => {
                fitCanvasToContainer();
            }, 100);
        }
    });

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize canvas first
        initializeCanvas();
        
        // Setup text formatting
        setupTextFormatting();

        // Setup formatting panel controls
        setupFormattingPanelControls();

        // Initialize file input display
        document.querySelectorAll('.file-input-container').forEach(container => {
            const input = container.querySelector('input[type="file"]');
            const fileNameDiv = container.nextElementSibling;

            if (input && fileNameDiv) {
                input.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        fileNameDiv.textContent = this.files[0].name;
                        fileNameDiv.style.display = 'block';
                    } else {
                        fileNameDiv.style.display = 'none';
                    }
                });
            }
        });

        // Force initial draw after a short delay to ensure all elements are loaded
        setTimeout(() => {
            immediateDrawText();
        }, 100);
    });

    // Make saveCertificate function globally available
    window.saveCertificate = saveCertificate;
</script>

<?php
// Include the footer
include('footer.php');
?>
</body>
</html>



            
