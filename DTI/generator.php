<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Generator</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        canvas { border: 1px solid #000; max-width: 100%; }
        .controls { margin: 10px 0; }
        textarea { width: 80%; height: 60px; }
    </style>
</head>
<body>
    <h2>Certificate Generator</h2>
    <input type="file" id="upload" accept="image/*">
    <br>
    <canvas id="certificateCanvas"></canvas>
    <br>
    <div class="controls">
        <input type="text" id="nameInput" placeholder="Enter Name">
        <textarea id="descriptionInput" placeholder="Enter Description"></textarea>
        <input type="date" id="dateInput">
        <br>
        <label>Font Size:</label>
        <input type="number" id="fontSize" value="40" min="10" max="100">
        <br>
        <input type="file" id="imageUpload" accept="image/*">
        <label>Signature X:</label>
        <input type="number" id="sigX" value="100" min="0" max="1000">
        <label>Signature Y:</label>
        <input type="number" id="sigY" value="500" min="0" max="1000">
        <label>Image Width:</label>
        <input type="number" id="imgWidth" value="200" min="50" max="1000">
        <label>Image Height:</label>
        <input type="number" id="imgHeight" value="100" min="50" max="1000">
        <button onclick="drawText()">Generate</button>
        <button onclick="downloadCertificate()">Download</button>
    </div>
    
    <script>
        let canvas = document.getElementById("certificateCanvas");
        let ctx = canvas.getContext("2d");
        let img = new Image();
        let lowerImage = new Image();

        document.getElementById("upload").addEventListener("change", function(event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
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
                };
                reader.readAsDataURL(file);
            }
        });

        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        };

        function drawText() {
            let name = document.getElementById("nameInput").value;
            let description = document.getElementById("descriptionInput").value;
            let date = document.getElementById("dateInput").value;
            let fontSize = document.getElementById("fontSize").value;
            let sigX = parseInt(document.getElementById("sigX").value);
            let sigY = parseInt(document.getElementById("sigY").value);
            let imgWidth = parseInt(document.getElementById("imgWidth").value);
            let imgHeight = parseInt(document.getElementById("imgHeight").value);
            
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            ctx.font = fontSize + "px Arial";
            ctx.fillStyle = "black";
            ctx.textAlign = "center";
            ctx.fillText(name, canvas.width / 2, canvas.height / 2);
            
            ctx.font = (fontSize * 0.6) + "px Arial";
            let lines = description.split('\n');
            let lineHeight = fontSize * 0.6;
            for (let i = 0; i < lines.length; i++) {
                ctx.fillText(lines[i], canvas.width / 2, canvas.height / 2 + 50 + (i * lineHeight));
            }
            
            ctx.textAlign = "right";
            ctx.fillText(date, canvas.width - 50, 100);
            
            if (lowerImage.src) {
                lowerImage.onload = function() {
                    ctx.drawImage(lowerImage, sigX, sigY, imgWidth, imgHeight);
                };
                ctx.drawImage(lowerImage, sigX, sigY, imgWidth, imgHeight);
            }
        }

        function downloadCertificate() {
            let link = document.createElement("a");
            link.download = "certificate.png";
            link.href = canvas.toDataURL("image/png");
            link.click();
        }
    </script>
</body>
</html>
