<?php
session_start();
require 'vendor/autoload.php';
require_once('dbcon.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

// Check if user is authenticated
if(!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== TRUE) {
    header('Location: login.php');
    exit();
}

// Utility function to send certificate email
function sendCertificateEmail($toEmail, $clientName, $attachmentPath, $referenceId = '', $clientId = null) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'idontplayinvoker@gmail.com'; // Your email
        $mail->Password   = 'crjpoellqbxjawrq';           // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('idontplayinvoker@gmail.com', 'DTI-Cebu Provincial');
        $mail->addAddress($toEmail, $clientName);
 
        $mail->isHTML(true);
        $mail->Subject = 'Your DTI Certificate';
        
        // Enhanced email body with reference ID if available
        $emailBody = "<p>Dear <strong>$clientName</strong>,</p>
                      <p>Attached is your digital certificate issued by DTI Cebu Provincial Office.</p>";
        
        if (!empty($referenceId)) {
            $emailBody .= "<p>Reference ID: <strong>$referenceId</strong></p>";
        }
        
        $emailBody .= "<p>Thank you.</p>";
        $mail->Body = $emailBody;

        // Check if the attachment path is a URL or a relative path
        if (preg_match('/^https?:\/\//', $attachmentPath)) {
            // It's a URL, extract the path part
            $parsedUrl = parse_url($attachmentPath);
            $relativePath = $parsedUrl['path'];
            
            // Convert to server path
            $serverPath = $_SERVER['DOCUMENT_ROOT'] . $relativePath;
            
            if (file_exists($serverPath)) {
                $mail->addAttachment($serverPath, "Certificate_$referenceId.pdf");
            } else {
                // Try with direct path
                if (file_exists(__DIR__ . '/' . basename($relativePath))) {
                    $mail->addAttachment(__DIR__ . '/' . basename($relativePath), "Certificate_$referenceId.pdf");
                } else {
                    throw new Exception("Certificate file not found at: $serverPath");
                }
            }
        } else {
            // It's a relative path
            if (file_exists(__DIR__ . '/' . $attachmentPath)) {
                $mail->addAttachment(__DIR__ . '/' . $attachmentPath, "Certificate_$referenceId.pdf");
            } else {
                throw new Exception("Certificate file not found at: " . __DIR__ . '/' . $attachmentPath);
            }
        }

        $mail->send();
        
        // Update the client record to mark email as sent
        if ($clientId) {
            global $conn;
            $stmt = $conn->prepare("UPDATE clients SET email_sent = 1, email_sent_date = NOW() WHERE id = ?");
            $stmt->bind_param("i", $clientId);
            $stmt->execute();
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Email failed for $toEmail. Error: " . $mail->ErrorInfo);
        
        // Update the client record to mark email as failed
        if ($clientId) {
            global $conn;
            $stmt = $conn->prepare("UPDATE clients SET email_sent = 0, email_error = ? WHERE id = ?");
            $errorMsg = substr($e->getMessage(), 0, 255); // Limit error message length
            $stmt->bind_param("si", $errorMsg, $clientId);
            $stmt->execute();
        }
        
        return false;
    }
}

// Helper to generate full file path URL
function getFullFilePath($relativePath) {
    if (preg_match('/^https?:\/\//', $relativePath)) return $relativePath;
    $baseDir = dirname($_SERVER['SCRIPT_NAME']);
    $baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    if ($baseDir == '/') $baseDir = '';
    return $baseURL . $baseDir . '/' . ltrim($relativePath, '/');
}

// Check if the clients table has the email_sent columns, add if not
$result = $conn->query("SHOW COLUMNS FROM clients LIKE 'email_sent'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE clients ADD COLUMN email_sent TINYINT(1) DEFAULT 0");
    $conn->query("ALTER TABLE clients ADD COLUMN email_sent_date DATETIME NULL");
    $conn->query("ALTER TABLE clients ADD COLUMN email_error VARCHAR(255) NULL");
}

// Send selected clients
if (isset($_POST['selected_clients']) && isset($_POST['file_id'])) {
    $file_id = intval($_POST['file_id']);
    $selected_clients = $_POST['selected_clients'];
    
    if (empty($selected_clients)) {
        echo "<script>alert('No clients selected.'); window.location.href='view_certificate.php?file_id=$file_id';</script>";
        exit;
    }
    
    // Create placeholders for the IN clause
    $placeholders = str_repeat('?,', count($selected_clients) - 1) . '?';
    
    // Prepare the query with dynamic placeholders
    $query = "SELECT id, client_name, email, reference_id, file_path FROM clients WHERE id IN ($placeholders) AND file_id = ?";
    $stmt = $conn->prepare($query);
    
    // Create the types string for bind_param
    $types = str_repeat('i', count($selected_clients)) . 'i';
    
    // Create the parameters array
    $params = array_merge($selected_clients, [$file_id]);
    
    // Bind parameters dynamically
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sent = 0;
    $failed = 0;
    
    while ($row = $result->fetch_assoc()) {
        $client_id = $row['id'];
        $client_name = $row['client_name'];
        $email = $row['email'];
        $reference_id = $row['reference_id'];
        $path = $row['file_path'];
        
        if (empty($path) || $path === '#') {
            $failed++;
            continue;
        }
        
        // Process the file path
        $file_path = $path;
        if (preg_match('/^https?:\/\//', $path)) {
            $file_path = str_replace(getFullFilePath(''), '', $path);
        }
        
        if (sendCertificateEmail($email, $client_name, $file_path, $reference_id, $client_id)) {
            $sent++;
        } else {
            $failed++;
        }
    }
    
    $message = "Sent $sent certificates successfully.";
    if ($failed > 0) {
        $message .= " Failed to send $failed certificates.";
    }
    
    echo "<script>alert('$message'); window.location.href='view_certificate.php?file_id=$file_id';</script>";
    exit;
}

// Send all for file_id
if (isset($_POST['send_all']) && isset($_POST['file_id'])) {
    $file_id = intval($_POST['file_id']);
    
    $stmt = $conn->prepare("SELECT id, client_name, email, reference_id, file_path FROM clients 
                           WHERE file_id = ? AND file_path IS NOT NULL AND file_path != '#'");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sent = 0;
    $failed = 0;
    
    while ($row = $result->fetch_assoc()) {
        $client_id = $row['id'];
        $client_name = $row['client_name'];
        $email = $row['email'];
        $reference_id = $row['reference_id'];
        $path = $row['file_path'];
        
        if (empty($path)) {
            $failed++;
            continue;
        }
        
        // Process the file path
        $file_path = $path;
        if (preg_match('/^https?:\/\//', $path)) {
            $file_path = str_replace(getFullFilePath(''), '', $path);
        }
        
        if (sendCertificateEmail($email, $client_name, $file_path, $reference_id, $client_id)) {
            $sent++;
        } else {
            $failed++;
        }
    }
    
    $message = "Sent $sent certificates successfully.";
    if ($failed > 0) {
        $message .= " Failed to send $failed certificates.";
    }
    
    echo "<script>alert('$message'); window.location.href='view_certificate.php?file_id=$file_id';</script>";
    exit;
}

// Send individual client
if (isset($_POST['client_id']) && isset($_POST['file_id'])) {
    $client_id = intval($_POST['client_id']);
    $file_id = intval($_POST['file_id']);
    
    $stmt = $conn->prepare("SELECT client_name, email, reference_id, file_path FROM clients WHERE id = ? AND file_id = ?");
    $stmt->bind_param("ii", $client_id, $file_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $client_name = $row['client_name'];
        $email = $row['email'];
        $reference_id = $row['reference_id'];
        $path = $row['file_path'];
        
        if (empty($path) || $path === '#') {
            echo "<script>alert('Certificate file not found for this client.'); window.location.href='view_certificate.php?file_id=$file_id';</script>";
            exit;
        }
        
        // Process the file path
        $file_path = $path;
        if (preg_match('/^https?:\/\//', $path)) {
            $file_path = str_replace(getFullFilePath(''), '', $path);
        }
        
        if (sendCertificateEmail($email, $client_name, $file_path, $reference_id, $client_id)) {
            echo "<script>alert('Certificate sent to $client_name successfully.'); window.location.href='view_certificate.php?file_id=$file_id';</script>";
        } else {
            echo "<script>alert('Failed to send certificate to $client_name.'); window.location.href='view_certificate.php?file_id=$file_id';</script>";
        }
    } else {
        echo "<script>alert('Client not found.'); window.location.href='view_certificate.php?file_id=$file_id';</script>";
    }
    exit;
}

// Send individual by reference_id (for backward compatibility)
if (isset($_POST['reference_id']) && isset($_POST['file_id'])) {
    $reference_id = $_POST['reference_id'];
    $file_id = intval($_POST['file_id']);
    
    $stmt = $conn->prepare("SELECT id, client_name, email, file_path FROM clients WHERE reference_id = ? AND file_id = ?");
    $stmt->bind_param("si", $reference_id, $file_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $client_id = $row['id'];
        $client_name = $row['client_name'];
        $email = $row['email'];
        $path = $row['file_path'];
        
        if (empty($path) || $path === '#') {
            echo "<script>alert('Certificate file not found.'); window.location.href='view_certificate.php?file_id=$file_id';</script>";
            exit;
        }
        
        // Process the file path
        $file_path = $path;
        if (preg_match('/^https?:\/\//', $path)) {
            $file_path = str_replace(getFullFilePath(''), '', $path);
        }
        
        if (sendCertificateEmail($email, $client_name, $file_path, $reference_id, $client_id)) {
            echo "<script>alert('Certificate sent to $client_name.'); window.location.href='view_certificate.php?file_id=$file_id';</script>";
        } else {
            echo "<script>alert('Failed to send certificate.'); window.location.href='view_certificate.php?file_id=$file_id';</script>";
        }
    } else {
        echo "<script>alert('Client not found.'); window.location.href='view_certificate.php?file_id=$file_id';</script>";
    }
    exit;
}

// If we get here, no valid action was specified
echo "<script>alert('Invalid request.'); window.history.back();</script>";
?>
