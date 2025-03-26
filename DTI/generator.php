<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&family=Montserrat:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4caf50;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
       
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
       
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }
       
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
       
        header {
            text-align: center;
            margin-bottom: 30px;
        }
       
        h2 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
       
        .app-container {
            display: flex;
            flex-direction: row;
            gap: 30px;
            flex-wrap: wrap;
        }
       
        .input-section, .preview-section {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow);
        }
       
        .section-title {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--secondary-color);
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 10px;
        }
       
        .form-group {
            margin-bottom: 15px;
        }
       
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--dark-color);
        }
       
        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-family: 'Poppins', Arial, sans-serif;
            transition: border-color 0.3s;
        }
       
        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--primary-color);
            outline: none;
        }
       
        textarea {
            resize: vertical;
            min-height: 80px;
        }
       
        .file-input {
            margin-bottom: 15px;
        }
       
        .file-input label {
            display: block;
            margin-bottom: 5px;
        }
       
        .position-controls {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
       
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
       
        button {
            padding: 12px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 500;
            transition: background-color 0.3s;
        }
       
        button:hover {
            background-color: var(--secondary-color);
        }
       
        button.download-btn {
            background-color: var(--success-color);
        }
       
        button.download-btn:hover {
            background-color: #3d8b40;
        }
       
        canvas {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            background-color: white;
            cursor: move;
        }
       
        .canvas-container {
            overflow: auto;
            margin-bottom: 20px;
            position: relative;
        }
       
        .drag-mode {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
       
        .drag-mode label {
            margin-right: 10px;
            margin-bottom: 0;
        }
       
        .element-selector {
            margin-bottom: 15px;
        }
       
        .drag-instructions {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            font-size: 0.9rem;
            border-left: 4px solid var(--accent-color);
        }

        .text-formatting {
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: var(--border-radius);
        }

        .text-formatting h4 {
            margin-bottom: 10px;
            color: var(--secondary-color);
        }

        .formatting-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .bold-instructions {
            margin-top: 10px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
        }
       
        @media (max-width: 768px) {
            .app-container {
                flex-direction: column;
            }
           
            .input-section, .preview-section {
                width: 100%;
            }

            .formatting-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h2>Certificate Generator</h2>
            <p>Create professional certificates with ease</p>
        </header>
       
        <div class="app-container">
            <div class="input-section">
                <h3 class="section-title">Input Settings</h3>
               
                <div class="file-input">
                    <label for="upload">Upload Certificate Template</label>
                    <input type="file" id="upload" accept="image/*">
                </div>
               
                <div class="form-group">
                    <input type="hidden" id="nameInput" placeholder="Enter recipient's name">
                </div>

                <div class="text-formatting">
                    <h4>Text Formatting</h4>
                    <div class="formatting-row">
                        <div class="form-group">
                            <label for="fontFamily">Font Family</label>
                            <select id="fontFamily">
                                <option value="Poppins, Arial, sans-serif">Poppins</option>
                                <option value="'Playfair Display', serif">Playfair Display</option>
                                <option value="Montserrat, sans-serif">Montserrat</option>
                                <option value="Roboto, sans-serif">Roboto</option>
                                <option value="Arial, sans-serif">Arial</option>
                                <option value="'Times New Roman', serif">Times New Roman</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="textColor">Text Color</label>
                            <input type="color" id="textColor" value="#000000">
                        </div>
                        <div class="form-group">
                            <input type="hidden" id="boldName">
                        </div>
                    </div>
                </div>
               
                <div class="form-group">
                    <label for="descriptionInput">Certificate Description</label>
                    <textarea id="descriptionInput" placeholder="Enter certificate description or achievement"></textarea>
                    
                    <div class="bold-instructions">
                        <p><strong>Bold Text Instructions:</strong> Use <code>**text**</code> to make text bold. For example: "This is **bold text** in a sentence."</p>
                    </div>
                </div>
               
                <div class="form-group">
                    <input type="hidden" id="dateInput">
                </div>
               
                <div class="form-group">
                    <label for="fontSize">Text Font Size</label>
                    <input type="number" id="fontSize" value="40" min="10" max="100">
                </div>
               
                <div class="file-input">
                    <label for="imageUpload">Upload Signature Image</label>
                    <input type="file" id="imageUpload" accept="image/*">
                </div>
               
                <div class="form-group">
                    <label>Signature Size</label>
                    <div class="position-controls">
                    <input type="hidden" id="sigX" value="100">
                    <input type="hidden" id="sigY" value="500">
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
            </div>
           
            <div class="preview-section">
                <h3 class="section-title">Certificate Preview</h3>
               
                <div class="drag-instructions">
                    <strong>Drag & Position:</strong> Select an element below and use your mouse to drag and position it on the certificate.
                </div>
               
                <div class="element-selector">
                    <label for="dragElement">Select element to position:</label>
                    <select id="dragElement">
                        <option value="description">Description</option>
                        <option value="signature">Signature</option>
                    </select>
                </div>
               
                <div class="canvas-container">
                    <canvas id="certificateCanvas"></canvas>
                </div>
               
                <div class="button-group">
                    <button onclick="drawText()" class="generate-btn">Generate Certificate</button>
                    <button onclick="downloadCertificate()" class="download-btn">Download Certificate</button>
                </div>
            </div>
        </div>
    </div>
   
    <script>
        let canvas = document.getElementById("certificateCanvas");
        let ctx = canvas.getContext("2d");
        let img = new Image();
        let lowerImage = new Image();
        let hasTemplate = false;
       
        // Element positions
        let elements = {
            name: { x: null, y: null, text: "", fontSize: 40, fontFamily: "Poppins, Arial, sans-serif", color: "#000000", bold: false },
            description: { x: null, y: null, text: "", fontSize: 24, fontFamily: "Poppins, Arial, sans-serif", color: "#000000", lines: [] },
            date: { x: null, y: null, text: "", fontFamily: "Poppins, Arial, sans-serif", color: "#000000" },
            signature: { x: 100, y: 500, width: 200, height: 100 }
        };
       
        // Dragging state
        let isDragging = false;
        let selectedElement = "name";
        let startX, startY;
       
        document.getElementById("upload").addEventListener("change", function(event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    hasTemplate = true;
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById("imageUpload").addEventListener("change", function(event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    lowerImage.src = e.target.result;
                    lowerImage.onload = function() {
                        drawText();
                    };
                };
                reader.readAsDataURL(file);
            }
        });

        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
           
            // Set default positions
            if (elements.name.x === null) {
                elements.name.x = canvas.width / 2;
                elements.name.y = canvas.height / 2;
                elements.description.x = canvas.width / 2;
                elements.description.y = canvas.height / 2 + 50;
                elements.date.x = canvas.width - 50;
                elements.date.y = 100;
            }
            
            drawText();
        };
       
        // Update selected element
        document.getElementById("dragElement").addEventListener("change", function(e) {
            selectedElement = e.target.value;
        });
        
        // Update font family
        document.getElementById("fontFamily").addEventListener("change", function(e) {
            elements.name.fontFamily = e.target.value;
            elements.description.fontFamily = e.target.value;
            elements.date.fontFamily = e.target.value;
            drawText();
        });
        
        // Update text color
        document.getElementById("textColor").addEventListener("change", function(e) {
            elements.name.color = e.target.value;
            elements.description.color = e.target.value;
            elements.date.color = e.target.value;
            drawText();
        });
        
        // Update bold name
        document.getElementById("boldName").addEventListener("change", function(e) {
            elements.name.bold = e.target.checked;
            drawText();
        });
       
                // Mouse events for dragging
        canvas.addEventListener("mousedown", function(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
           
            startX = (e.clientX - rect.left) * scaleX;
            startY = (e.clientY - rect.top) * scaleY;
           
            // Check if we're on the selected element
            let isOnElement = false;
           
            if (selectedElement === "signature" && lowerImage.src) {
                const sig = elements.signature;
                if (startX >= sig.x && startX <= sig.x + sig.width &&
                    startY >= sig.y && startY <= sig.y + sig.height) {
                    isOnElement = true;
                }
            } else {
                // For text elements, we'll just enable dragging anywhere
                isOnElement = true;
            }
           
            if (isOnElement) {
                isDragging = true;
            }
        });
       
        canvas.addEventListener("mousemove", function(e) {
            if (!isDragging) return;
           
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
           
            const mouseX = (e.clientX - rect.left) * scaleX;
            const mouseY = (e.clientY - rect.top) * scaleY;
           
            const dx = mouseX - startX;
            const dy = mouseY - startY;
           
            if (selectedElement === "signature") {
                elements.signature.x += dx;
                elements.signature.y += dy;
                document.getElementById("sigX").value = Math.round(elements.signature.x);
                document.getElementById("sigY").value = Math.round(elements.signature.y);
            } else if (selectedElement === "name") {
                elements.name.x += dx;
                elements.name.y += dy;
            } else if (selectedElement === "description") {
                elements.description.x += dx;
                elements.description.y += dy;
            } else if (selectedElement === "date") {
                elements.date.x += dx;
                elements.date.y += dy;
            }
           
            startX = mouseX;
            startY = mouseY;
           
            // Redraw
            drawText();
        });
       
        canvas.addEventListener("mouseup", function() {
            isDragging = false;
        });
       
        canvas.addEventListener("mouseleave", function() {
            isDragging = false;
        });
       
        // Update element positions from input fields
        document.getElementById("sigX").addEventListener("change", function(e) {
            elements.signature.x = parseInt(e.target.value);
            drawText();
        });
       
        document.getElementById("sigY").addEventListener("change", function(e) {
            elements.signature.y = parseInt(e.target.value);
            drawText();
        });
       
        document.getElementById("imgWidth").addEventListener("change", function(e) {
            elements.signature.width = parseInt(e.target.value);
            drawText();
        });
       
        document.getElementById("imgHeight").addEventListener("change", function(e) {
            elements.signature.height = parseInt(e.target.value);
            drawText();
        });

        // Function to parse text with bold markers and render it
        function drawTextWithBoldMarkers(text, x, y, fontSize, fontFamily, color) {
            const segments = text.split(/(\*\*.*?\*\*)/g);
            
            ctx.textAlign = "center";
            ctx.fillStyle = color;
            
            let currentX = x;
            let totalWidth = 0;
            
            // First measure the total width to center properly
            segments.forEach(segment => {
                let cleanSegment = segment;
                if (segment.startsWith('**') && segment.endsWith('**')) {
                    cleanSegment = segment.substring(2, segment.length - 2);
                }
                
                ctx.font = segment.startsWith('**') && segment.endsWith('**') 
                    ? `bold ${fontSize}px ${fontFamily}`
                    : `normal ${fontSize}px ${fontFamily}`;
                    
                totalWidth += ctx.measureText(cleanSegment).width;
            });
            
            // Start drawing from the left side of the centered text
            currentX = x - (totalWidth / 2);
            
            // Now draw each segment
            segments.forEach(segment => {
                let cleanSegment = segment;
                if (segment.startsWith('**') && segment.endsWith('**')) {
                    cleanSegment = segment.substring(2, segment.length - 2);
                }
                
                ctx.font = segment.startsWith('**') && segment.endsWith('**') 
                    ? `bold ${fontSize}px ${fontFamily}`
                    : `normal ${fontSize}px ${fontFamily}`;
                
                ctx.textAlign = "left";
                ctx.fillText(cleanSegment, currentX, y);
                
                currentX += ctx.measureText(cleanSegment).width;
            });
        }

        function drawText() {
            if (!hasTemplate) {
                alert("Please upload a certificate template first");
                return;
            }
           
            // Get values from form
            elements.name.text = document.getElementById("nameInput").value;
            elements.description.text = document.getElementById("descriptionInput").value;
            const dateInput = document.getElementById("dateInput").value;
            if (dateInput) {
                elements.date.text = new Date(dateInput).toLocaleDateString('en-US', {
                    year: 'numeric', month: 'long', day: 'numeric'
                });
            }
            elements.name.fontSize = parseInt(document.getElementById("fontSize").value);
            elements.description.fontSize = elements.name.fontSize * 0.6;
           
            // Update signature dimensions
            elements.signature.width = parseInt(document.getElementById("imgWidth").value);
            elements.signature.height = parseInt(document.getElementById("imgHeight").value);
           
            // Split description into lines
            elements.description.lines = elements.description.text.split('\n');
           
            // Clear canvas and redraw template
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
           
            // Draw name
            if (elements.name.bold) {
                ctx.font = `bold ${elements.name.fontSize}px ${elements.name.fontFamily}`;
            } else {
                ctx.font = `${elements.name.fontSize}px ${elements.name.fontFamily}`;
            }
            ctx.fillStyle = elements.name.color;
            ctx.textAlign = "center";
            ctx.fillText(elements.name.text, elements.name.x, elements.name.y);
           
            // Draw description with bold markers
            let lineHeight = elements.description.fontSize * 1.3;
            for (let i = 0; i < elements.description.lines.length; i++) {
                drawTextWithBoldMarkers(
                    elements.description.lines[i],
                    elements.description.x,
                    elements.description.y + (i * lineHeight),
                    elements.description.fontSize,
                    elements.description.fontFamily,
                    elements.description.color
                );
            }
           
            // Draw date
            if (elements.date.text) {
                ctx.textAlign = "right";
                ctx.font = `${elements.description.fontSize}px ${elements.date.fontFamily}`;
                ctx.fillStyle = elements.date.color;
                ctx.fillText(elements.date.text, elements.date.x, elements.date.y);
            }
           
            // Draw signature image if available
            if (lowerImage.complete && lowerImage.src) {
                ctx.drawImage(
                    lowerImage,
                    elements.signature.x,
                    elements.signature.y,
                    elements.signature.width,
                    elements.signature.height
                );
            }
        }

        function downloadCertificate() {
            if (!hasTemplate) {
                alert("Please generate a certificate first");
                return;
            }
           
            // Generate a filename with date
            const recipientName = document.getElementById("nameInput").value || "certificate";
            const filename = `certificate_${recipientName.replace(/\s+/g, '_')}_${new Date().toISOString().slice(0, 10)}.png`;
           
            let link = document.createElement("a");
            link.download = filename;
            link.href = canvas.toDataURL("image/png");
            link.click();
        }
       
        // Add event listeners to update the preview when inputs change
        document.getElementById("nameInput").addEventListener("input", drawText);
        document.getElementById("descriptionInput").addEventListener("input", drawText);
        document.getElementById("dateInput").addEventListener("change", drawText);
        document.getElementById("fontSize").addEventListener("change", drawText);
    </script>
</body>
</html>

