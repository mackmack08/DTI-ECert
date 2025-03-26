<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
  <input type="file" id="upload" accept="image/*" />
  <br />
  <canvas id="certificateCanvas"></canvas>
  <br />
  <div class="controls">
    <input type="text" id="nameInput" placeholder="Enter Name" />
    <textarea id="descriptionInput" placeholder="Enter Description (use *...* for bold)"></textarea>
    <input type="date" id="dateInput" />
    <br />
    <label>Font Size:</label>
    <input type="number" id="fontSize" value="40" min="10" max="100" />
    <br />
    <input type="file" id="imageUpload" accept="image/*" />
    <label>Signature X:</label>
    <input type="number" id="sigX" value="100" min="0" max="1000" />
    <label>Signature Y:</label>
    <input type="number" id="sigY" value="500" min="0" max="1000" />
    <label>Image Width:</label>
    <input type="number" id="imgWidth" value="200" min="50" max="1000" />
    <label>Image Height:</label>
    <input type="number" id="imgHeight" value="100" min="50" max="1000" />
    <button onclick="drawText()">Generate</button>
    <button onclick="downloadCertificate()">Download</button>
  </div>
  
  <script>
    let canvas = document.getElementById("certificateCanvas");
    let ctx = canvas.getContext("2d");
    let img = new Image();
    let lowerImage = new Image();

    // Load background image
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

    // Load signature image
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
      let fontSize = parseInt(document.getElementById("fontSize").value);
      let sigX = parseInt(document.getElementById("sigX").value);
      let sigY = parseInt(document.getElementById("sigY").value);
      let imgWidth = parseInt(document.getElementById("imgWidth").value);
      let imgHeight = parseInt(document.getElementById("imgHeight").value);
      
      // Redraw background
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

      // Draw Name (centered above description)
      ctx.font = fontSize + "px Arial";
      ctx.fillStyle = "black";
      ctx.textAlign = "center";
      ctx.fillText(name, canvas.width / 2, canvas.height / 2 - 50);

      // Process and Draw Description line by line
      let lines = description.split("\n");
      let lineHeight = fontSize * 0.6;
      for (let i = 0; i < lines.length; i++) {
        let line = lines[i];
        // Split line into segments based on bold markers.
        // This regex captures segments that start and end with asterisk, including spaces.
        let segments = line.split(/(\*[^*]+\*)/g);
        // Calculate total width of the line.
        let totalWidth = 0;
        segments.forEach(seg => {
          let text = seg;
          let isBold = false;
          if (seg.startsWith("*") && seg.endsWith("*")) {
            text = seg.slice(1, -1);
            isBold = true;
          }
          ctx.font = isBold ? "bold " + (fontSize * 0.6) + "px Arial" : (fontSize * 0.6) + "px Arial";
          totalWidth += ctx.measureText(text).width;
        });
        // Center the line by calculating the starting X coordinate.
        let startX = (canvas.width - totalWidth) / 2;
        let y = canvas.height / 2 + 50 + (i * lineHeight);
        // Draw each segment in sequence.
        segments.forEach(seg => {
          let text = seg;
          let isBold = false;
          if (seg.startsWith("*") && seg.endsWith("*")) {
            text = seg.slice(1, -1);
            isBold = true;
          }
          ctx.font = isBold ? "bold " + (fontSize * 0.6) + "px Arial" : (fontSize * 0.6) + "px Arial";
          ctx.textAlign = "left";
          ctx.fillText(text, startX, y);
          startX += ctx.measureText(text).width;
        });
      }

      // Draw Date at the top right corner
      ctx.textAlign = "right";
      ctx.font = fontSize + "px Arial";
      ctx.fillText(date, canvas.width - 50, 100);

      // Draw Signature Image if available
      if (lowerImage.src) {
        if (!lowerImage.complete) {
          lowerImage.onload = function() {
            ctx.drawImage(lowerImage, sigX, sigY, imgWidth, imgHeight);
          };
        } else {
          ctx.drawImage(lowerImage, sigX, sigY, imgWidth, imgHeight);
        }
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
