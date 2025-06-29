<?php
session_start();
include("dbcon.php");
 // Assumes $conn is created here

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

function sendemail_verify($email, $verify_token) {
    global $conn;

    // Fetch SMTP credentials from users table
    $query = "SELECT email, email_pass FROM users";
    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        $_SESSION['mail_error'] = 'SMTP credentials not found.';
        return false;
    }

    $row = mysqli_fetch_assoc($result);
    $smtp_email = $row['email'];
    $smtp_pass = $row['email_pass'];

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0; // Set to 2 to enable debug output
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_email;
        $mail->Password   = $smtp_pass;
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // ✅ Allow self-signed certificates
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom($smtp_email, 'DTI-Cebu Provincial');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';

        $email_template = "
            <h2>You have registered with DTI-Provincial Office E-Ceritificate System</h2>
            <h4>Verify your email address to login using the link below:</h4>
            <br><br>
            <a href='http://localhost/dti/verifyemail.php?token=$verify_token'>Verify Email</a>";

        $mail->Body = $email_template;

        $mail->send();
        return true;
    } catch (Exception $e) {
        $_SESSION['mail_error'] = 'Mailer Error: ' . $mail->ErrorInfo;
        return false;
    }
}

// Get user data if ID is provided
$user_id = isset($_GET['id']) ? $_GET['id'] : '';
$user_email = '';
$user_email_pass = '';

if($user_id) {
    // Get the user's data from the database
    $query = "SELECT email, email_pass FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_email = $row['email'];
        $user_email_pass = $row['email_pass'];
    } else {
        // User not found
        $_SESSION['status'] = "User not found.";
        header("Location: index.php");
        exit();
    }
}

// Handle update form submission
if(isset($_POST['update_btn'])) {
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $verify_token = md5(rand());
    $verify_status= 0;
    $email_pass = $_POST['email_pass'];

    // Password validation
    if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/[0-9]/', $password) || 
        !preg_match('/[\W_]/', $password)) {
        $_SESSION['status'] = "Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.";
        header("Location: update.php?id=$user_id");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['status'] = "Passwords do not match.";
        header("Location: update.php?id=$user_id");
        exit();
    }

    // Check if user ID exists
    $check_user = "SELECT id FROM users WHERE id = ?";
    $stmt_check = $conn->prepare($check_user);
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if($result->num_rows > 0) {
        // Update the existing user
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $update = "UPDATE users SET email = ?, password = ?, verify_token = ?, email_pass = ?, verify_status = ? WHERE id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("ssssii", $email, $password_hash, $verify_token, $email_pass, $verify_status, $user_id);
        $query_run = $stmt->execute();
        
        if ($query_run) {
            $email_sent = sendemail_verify($email, $verify_token);
            
            if ($email_sent) {
                echo "<script>
                    alert('Account updated! Please verify your email address.');
                    window.location.href = 'index.php';
                </script>";
            } else {
                echo "<script>
                    alert('Account updated! But verification email could not be sent. " . ($_SESSION['mail_error'] ?? '') . "');
                    window.location.href = 'update.php?id=$user_id';
                </script>";
            }
            exit();
        } else {
            echo "<script>
                alert('Update Failed: " . $conn->error . "');
                window.location.href = 'update.php?id=$user_id';
            </script>";
            exit();
        }
    } else {
        // User ID doesn't exist
        $_SESSION['status'] = "User not found. Cannot update non-existent account.";
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Account</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body>
    <img style="z-index: -2; position: absolute; width: 100%; height: 110%;" src="img/DTI-BACKGROUND.png" alt="">
   
    <div class="container vh-100 d-flex align-items-center">
        <div class="row w-100">
            <!-- Update Form -->
            <div class="col-md-6 d-flex align-items-center justify-content-center form-column">
                <div class="signup-container">
                    <div class="signup-header">
                        <h1 class="text-white" style="font-weight: 700;">UPDATE ACCOUNT</h1><br>
                        <p class="text-white">Update your account information</p>
                    </div>
                   
                    <form name="updateForm" action="" method="POST" onsubmit="return validateForm()">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" name="email" value="<?php echo $user_email; ?>" placeholder="Email" required>
                            <label for="floatingEmail">Email Address</label>
                        </div>
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                            <label for="floatingPassword">New Password</label> 
                        </div>
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                            <label for="floatingPassword">Confirm New Password</label>
                        </div>
                        <div class="form-floating mb-3 position-relative">
                            <input type="text" class="form-control" name="email_pass" id="email_pass" value="<?php echo $user_email_pass; ?>" placeholder="Email Code" required>
                            <label>Email Code</label>
                        </div>
                        <div id="password-feedback" class="invalid-feedback" style="display: none;">
                            Passwords do not match.
                        </div>
                        <br>
                        <div class="text-center">
                            <button type="su6bmit" name="update_btn" class="custom-signup-btn">Update Account</button>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="dashboard.php" class="text-white">Back to Dashboard</a>
                        </div>
                    </form>  
                </div>
            </div>

            <!-- Right Side: Infinite Scrolling Vertical Image Slider -->
            <div class="col-md-6 certificate-column" id="certificateColumn">
                <div class="slider-container">
                    <div class="image-slider">
                        <img src="img/SampleCertificate.png" alt="Certificate">
                        <img src="img/SampleCertificate.png" alt="Certificate">
                        <img src="img/SampleCertificate.png" alt="Certificate">
                        <img src="img/SampleCertificate.png" alt="Certificate">
                        <img src="img/SampleCertificate.png" alt="Certificate">
                        <img src="img/SampleCertificate.png" alt="Certificate">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function validateForm() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const passwordFeedback = document.getElementById('password-feedback');
        
        // Check if passwords match
        if (password !== confirmPassword) {
            passwordFeedback.style.display = 'block';
            passwordFeedback.innerHTML = 'Passwords do not match.';
            document.getElementById('confirm_password').classList.add('is-invalid');
            return false;
        } else {
            passwordFeedback.style.display = 'none';
            document.getElementById('confirm_password').classList.remove('is-invalid');
            
            // Check password complexity
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /[0-9]/.test(password);
            const hasSpecialChar = /[\W_]/.test(password);
            const isLongEnough = password.length >= 8;
            
            if (!isLongEnough || !hasUpperCase || !hasLowerCase || !hasNumbers || !hasSpecialChar) {
                passwordFeedback.innerHTML = 'Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.';
                passwordFeedback.style.display = 'block';
                document.getElementById('password').classList.add('is-invalid');
                return false;
            }
            
            return true;
        }
    }
    
    // Real-time validation as user types
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        const passwordFeedback = document.getElementById('password-feedback');
        
        if (confirmPassword && password !== confirmPassword) {
            passwordFeedback.innerHTML = 'Passwords do not match.';
            passwordFeedback.style.display = 'block';
            this.classList.add('is-invalid');
        } else {
            passwordFeedback.style.display = 'none';
            this.classList.remove('is-invalid');
        }
    });
    
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const passwordFeedback = document.getElementById('password-feedback');
        
        // If confirm password is already filled, check match on password change
        if (confirmPassword && password !== confirmPassword) {
            passwordFeedback.innerHTML = 'Passwords do not match.';
            passwordFeedback.style.display = 'block';
            document.getElementById('confirm_password').classList.add('is-invalid');
        } else if (confirmPassword) {
            passwordFeedback.style.display = 'none';
            document.getElementById('confirm_password').classList.remove('is-invalid');
        }
        
        // Check password complexity in real-time
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /[0-9]/.test(password);
        const hasSpecialChar = /[\W_]/.test(password);
        const isLongEnough = password.length >= 8;
        
        if (password && (!isLongEnough || !hasUpperCase || !hasLowerCase || !hasNumbers || !hasSpecialChar)) {
            passwordFeedback.innerHTML = 'Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.';
            passwordFeedback.style.display = 'block';
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            // Only hide feedback if confirm password also matches
            if (!confirmPassword || password === confirmPassword) {
                passwordFeedback.style.display = 'none';
            }
        }
    });
    </script>
</body>
</html>
