<?php
session_start();
include('dbcon.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_default_settings') {
    $file_id = intval($_POST['file_id']);
    
    // Use default positioning settings for faster processing
    $defaultSettings = [
        'ref_id_x' => 242,
        'ref_id_y' => 12,
        'ref_id_size' => 13,
        'ref_id_color' => '0,0,0',
        'client_name_x' => 'center',
        'client_name_y' => 'middle',
        'client_name_size' => 25,
        'client_name_color' => '38,61,128'
    ];
    
    // Save template settings to session
    $_SESSION['certificate_template'] = [
        'file_id' => $file_id,
        'positioning_data' => $defaultSettings
    ];
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
