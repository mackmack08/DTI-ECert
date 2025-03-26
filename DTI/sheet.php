<?php include('dbcon.php'); // Include database connection

require 'vendor/autoload.php'; // Include PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;


$uploaded_files = []; // Retrieve all uploaded files from the database to display existing files
$query = "SELECT * FROM files ORDER BY upload_time DESC";
$result = $conn->query($query);


while ($row = $result->fetch_assoc()) {
    $uploaded_files[] = [
        'id' => $row['id'],
        'file_name' => $row['file_name'],
        'file_path' => $row['file_path'],
        'upload_time' => $row['upload_time']
    ];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_upload'])) {
    $file_uploads = $_FILES['file_upload'];
    $upload_dir = 'uploads/';

    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Iterate through all uploaded files
    for ($i = 0; $i < count($file_uploads['name']); $i++) {
        $file_name = $_POST['file_name'] ?? $file_uploads['name'][$i]; // Use the name from the input field if it's available
        $file_tmp_name = $file_uploads['tmp_name'][$i];
        $file_size = $file_uploads['size'][$i];
        $file_error = $file_uploads['error'][$i];
        $upload_path = $upload_dir . basename($file_name);

        if ($file_error === UPLOAD_ERR_OK) {
            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file_tmp_name, $upload_path)) {
                // Insert file information into the database
                $stmt = $conn->prepare("INSERT INTO files (file_name, file_path) VALUES (?, ?)");
                $stmt->bind_param("ss", $file_name, $upload_path);
                if ($stmt->execute()) {
                    // Get the last inserted ID and current timestamp
                    $last_id = $conn->insert_id;
                    $current_time = date("Y-m-d H:i:s");
                   
                    // File is successfully uploaded and saved to the database
                    // Add this file to the display array as well
                    $uploaded_files[] = [
                        'id' => $last_id,
                        'file_name' => $file_name,
                        'file_path' => $upload_path,
                        'upload_time' => $current_time
                    ];
                }
                $stmt->close();

                // Load the spreadsheet and process it
                $spreadsheet = IOFactory::load($upload_path);
                $sheet = $spreadsheet->getActiveSheet();
               
                // Iterate over the rows in the spreadsheet
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
               
                // Loop through each row and insert the data into the clients table
                for ($row = 2; $row <= $highestRow; $row++) { // Assuming row 1 contains headers
                    $timestamp = $sheet->getCell('A' . $row)->getValue();
                    $client_name = $sheet->getCell('B' . $row)->getValue();
                    $client_type = $sheet->getCell('C' . $row)->getValue();
                    $sex = $sheet->getCell('D' . $row)->getValue();
                    $age = $sheet->getCell('E' . $row)->getValue();
                    $region = $sheet->getCell('F' . $row)->getValue();
                    $contact = $sheet->getCell('G' . $row)->getValue();
                    $email = $sheet->getCell('H' . $row)->getValue();
                    $service_ro_objectives_achieved = $sheet->getCell('I' . $row)->getValue();
                    $service_ro_info_received = $sheet->getCell('J' . $row)->getValue();
                    $service_ro_relevance_value = $sheet->getCell('K' . $row)->getValue();
                    $service_ro_duration_sufficient = $sheet->getCell('L' . $row)->getValue();
                    $service_af_sign_up_access = $sheet->getCell('M' . $row)->getValue();
                    $service_af_audio_video_sync = $sheet->getCell('N' . $row)->getValue();
                    $resource_speaker_rq_knowledge = $sheet->getCell('O' . $row)->getValue();
                    $resource_speaker_rq_clarity = $sheet->getCell('P' . $row)->getValue();
                    $resource_speaker_rq_engagement = $sheet->getCell('Q' . $row)->getValue();
                    $resource_speaker_rq_visual_relevance = $sheet->getCell('R' . $row)->getValue();
                    $resource_speaker_ri_answer_questions = $sheet->getCell('S' . $row)->getValue();
                    $resource_speaker_ri_chat_responsiveness = $sheet->getCell('T' . $row)->getValue();
                    $moderator_rr_manage_discussion = $sheet->getCell('U' . $row)->getValue();
                    $moderator_rr_monitor_raises_questions = $sheet->getCell('V' . $row)->getValue();
                    $moderator_rr_manage_program = $sheet->getCell('W' . $row)->getValue();
                    $host_secretariat_rr_technical_assistance = $sheet->getCell('X' . $row)->getValue();
                    $host_secretariat_rr_admittance_management = $sheet->getCell('Y' . $row)->getValue();
                    $overall_satisfaction_rating = $sheet->getCell('Z' . $row)->getValue();
                    $feedback_dissatisfied_reasons = $sheet->getCell('AA' . $row)->getValue();
                    $feedback_improvement_suggestions = $sheet->getCell('AB' . $row)->getValue();
                   
                    // Insert into clients table
                    $stmt = $conn->prepare("INSERT INTO clients (timestamp, client_name, client_type, sex, age, region, contact,
                    email, service_ro_objectives_achieved, service_ro_info_received, service_ro_relevance_value,
                    service_ro_duration_sufficient, service_af_sign_up_access, service_af_audio_video_sync, resource_speaker_rq_knowledge,
                    resource_speaker_rq_clarity, resource_speaker_rq_engagement, resource_speaker_rq_visual_relevance, resource_speaker_ri_answer_questions,
                    resource_speaker_ri_chat_responsiveness, moderator_rr_manage_discussion, moderator_rr_monitor_raises_questions, moderator_rr_manage_program,
                    host_secretariat_rr_technical_assistance, host_secretariat_rr_admittance_management, overall_satisfaction_rating,
                    feedback_dissatisfied_reasons, feedback_improvement_suggestions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                    $stmt->bind_param("ssssssssssssssssssssssssssss", $timestamp, $client_name, $client_type, $sex, $age, $region, $contact, $email,
                    $service_ro_objectives_achieved, $service_ro_info_received, $service_ro_relevance_value, $service_ro_duration_sufficient,
                    $service_af_sign_up_access, $service_af_audio_video_sync, $resource_speaker_rq_knowledge, $resource_speaker_rq_clarity,
                    $resource_speaker_rq_engagement, $resource_speaker_rq_visual_relevance, $resource_speaker_ri_answer_questions,
                    $resource_speaker_ri_chat_responsiveness, $moderator_rr_manage_discussion, $moderator_rr_monitor_raises_questions,
                    $moderator_rr_manage_program, $host_secretariat_rr_technical_assistance, $host_secretariat_rr_admittance_management,
                    $overall_satisfaction_rating, $feedback_dissatisfied_reasons, $feedback_improvement_suggestions);
                    $stmt->execute();
                }
            } else {
                echo "Error uploading the file.";
            }
        } else {
            echo "There was an error with the file upload.";
        }
    }
}


// Handle Edit Sheet form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_file_id'])) {
    $file_id = $_POST['edit_file_id'];
    $new_file_name = $_POST['edit_file_name'];
    $upload_dir = 'uploads/';
   
    // Update file name in database
    $stmt = $conn->prepare("UPDATE files SET file_name = ? WHERE id = ?");
    $stmt->bind_param("si", $new_file_name, $file_id);
    $stmt->execute();
   
    // Check if a new file was uploaded
    if (isset($_FILES['edit_file_upload']) && $_FILES['edit_file_upload']['error'] === UPLOAD_ERR_OK) {
        // Get the current file path
        $query = "SELECT file_path FROM files WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $old_file_path = $row['file_path'];
       
        // Upload the new file
        $file_tmp_name = $_FILES['edit_file_upload']['tmp_name'];
        $new_file_path = $upload_dir . basename($new_file_name);
       
        if (move_uploaded_file($file_tmp_name, $new_file_path)) {
            // Update the file path in the database
            $stmt = $conn->prepare("UPDATE files SET file_path = ? WHERE id = ?");
            $stmt->bind_param("si", $new_file_path, $file_id);
            $stmt->execute();
           
            // Delete the old file if it exists and is different from the new path
            if (file_exists($old_file_path) && $old_file_path != $new_file_path) {
                unlink($old_file_path);
            }
           
            // Process the new Excel file
            $spreadsheet = IOFactory::load($new_file_path);
            $sheet = $spreadsheet->getActiveSheet();
           
            // You may want to update the client data here as well
            // For simplicity, we're not implementing that part in this example
        }
    }
   
    // Redirect to refresh the page
    header("Location: sheet.php");
    exit;
}

// Handle Delete Sheet form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_file_id'])) {
    $file_id = $_POST['delete_file_id'];
    
    // Get the file path before deleting the record
    $query = "SELECT file_path FROM files WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $file_path = $row['file_path'];
    
    // Delete the record from the database
    $stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    
    // Delete the physical file if it exists
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    
    // Redirect to refresh the page
    header("Location: sheet.php");
    exit;
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <link rel="stylesheet" href="style5.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // JavaScript to update the input field with the selected file name
        function updateFileName(input) {
            var fileName = input.files[0] ? input.files[0].name : '';
            document.getElementById('file_name_input').value = fileName;
        }
       
        // JavaScript to update the edit form with the selected file name
        function updateEditFileName(input) {
            var fileName = input.files[0] ? input.files[0].name : '';
            document.getElementById('edit_file_name_display').textContent = fileName;
        }
       
        // Function to populate the edit modal with file data
        function populateEditModal(fileId, fileName, filePath) {
            document.getElementById('edit_file_id').value = fileId;
            document.getElementById('edit_file_name').value = fileName;
            document.getElementById('current_file_path').textContent = filePath;
            document.getElementById('edit_file_name_display').textContent = "No new file selected";
        }
        
        // Function to populate the delete modal with file data
        function populateDeleteModal(fileId, fileName) {
            document.getElementById('delete_file_id').value = fileId;
            document.getElementById('delete_file_name').textContent = fileName;
        }
    </script>
</head>
<body>
    <button class="btn btn-primary upload-btn" data-toggle="modal" data-target="#uploadModal">
        <i class="bi bi-plus"></i> Upload New Sheet
    </button>
    <!-- Modal for File Upload -->
    <div class="modal" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="sheet.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Upload New Sheet</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- File Name Input Field (Above File Upload Input) -->
                        <label for="file_name_input">Sheet Information:</label>
                        <input type="text" id="file_name_input" name="file_name" value="" placeholder="Enter file name" class="form-control"><br>
                       
                        <!-- File Upload Input -->
                        <label for="file_upload">File Upload (Excel File):</label>
                        <input type="file" id="file_upload" name="file_upload[]" accept=".xlsx, .xls" multiple required onchange="updateFileName(this)">
                    </div>
                    <div class="modal-footer">
                        <div class="button-container">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-custom-upload btn-sm">Upload Sheet</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Edit Sheet -->
    <div class="modal" id="editSheetModal" tabindex="-1" role="dialog" aria-labelledby="editSheetModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="sheet.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSheetModalLabel">Edit Sheet</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_file_id" name="edit_file_id">
                       
                        <!-- File Name Input Field -->
                        <label for="edit_file_name">Sheet Information:</label>
                        <input type="text" id="edit_file_name" name="edit_file_name" placeholder="Enter file name" class="form-control"><br>
                       
                        <!-- Current File Information -->
                        <div class="form-group">
                            <label>Current File:</label>
                            <p id="current_file_path" class="form-control-static"></p>
                        </div>
                       
                        <!-- File Upload Input -->
                        <label for="edit_file_upload">Replace File (Excel File):</label>
                        <input type="file" id="edit_file_upload" name="edit_file_upload" accept=".xlsx, .xls" onchange="updateEditFileName(this)">
                        <p class="mt-2">Selected file: <span id="edit_file_name_display">No new file selected</span></p>
                    </div>
                    <div class="modal-footer">
                        <div class="button-container">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-custom-upload btn-sm">Update Sheet</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal for Delete Sheet -->
    <div class="modal" id="deleteSheetModal" tabindex="-1" role="dialog" aria-labelledby="deleteSheetModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="sheet.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteSheetModalLabel">Delete Sheet</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="delete_file_id" name="delete_file_id">
                        <p>Are you sure you want to delete the sheet: <strong><span id="delete_file_name"></span></strong>?</p>
                        <p class="text-danger">This action cannot be undone!</p>
                    </div>
                    <div class="modal-footer">
                        <div class="button-container">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger btn-sm">Delete Sheet</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Display Cards for Each Uploaded File -->
    <?php if (!empty($uploaded_files)) {
        foreach ($uploaded_files as $file) { ?>
            <div class="card">
            <div class="card-header">
                    Uploaded File: <?php echo htmlspecialchars($file['file_name']); ?>
                </div>
                <div class="card-body">
                    <div class="upload-time">
                        Upload Time: <?php echo htmlspecialchars($file['upload_time']); ?>
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-info btn-sm view-btn" data-toggle="modal" data-target="#viewModal<?php echo $file['id']; ?>">
                            View Details
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="populateEditModal('<?php echo $file['id']; ?>', '<?php echo htmlspecialchars($file['file_name']); ?>', '<?php echo htmlspecialchars($file['file_path']); ?>')" data-toggle="modal" data-target="#editSheetModal">
                            Edit Sheet
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="populateDeleteModal('<?php echo $file['id']; ?>', '<?php echo htmlspecialchars($file['file_name']); ?>')" data-toggle="modal" data-target="#deleteSheetModal">
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Details Modal -->
            <div class="modal fade" id="viewModal<?php echo $file['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel<?php echo $file['id']; ?>" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewModalLabel<?php echo $file['id']; ?>">Sheet Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Uploaded File:</strong> <?php echo htmlspecialchars($file['file_name']); ?></p>
                            <p><strong>Upload Time:</strong> <?php echo htmlspecialchars($file['upload_time']); ?></p>
                            <p><strong>File Path:</strong> <?php echo htmlspecialchars($file['file_path']); ?></p>
                        </div>
                        <div class="modal-footer">
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                <a href="<?php echo htmlspecialchars($file['file_path']); ?>" class="btn btn-success btn-sm" download>Download Excel</a>
                                <button type="button" class="btn btn-warning btn-sm" onclick="populateEditModal('<?php echo $file['id']; ?>', '<?php echo htmlspecialchars($file['file_name']); ?>', '<?php echo htmlspecialchars($file['file_path']); ?>')" data-toggle="modal" data-target="#editSheetModal" data-dismiss="modal">Edit Sheet</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
    } else { ?>
        <div class="alert alert-info">No files have been uploaded yet.</div>
    <?php } ?>
</body>
</html>

