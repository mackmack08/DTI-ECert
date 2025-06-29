<?php
session_start();
include("dbcon.php"); // Assumes $conn is created here

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

if(isset($_POST['signup_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $verify_token = md5(rand());
    $email_pass = $_POST['email_pass'];

    if (strlen($password) < 8 || 
    !preg_match('/[A-Z]/', $password) || 
    !preg_match('/[a-z]/', $password) || 
    !preg_match('/[0-9]/', $password) || 
    !preg_match('/[\W_]/', $password)) {
    $_SESSION['status'] = "Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.";
    header("Location: signup.php");
    exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['status'] = "Passwords do not match.";
        header("Location: signup.php");
        exit();
    }

    // Check if email already exists
    $check_email = "SELECT email FROM users WHERE email = ?";
    $stmt_check = $conn->prepare($check_email);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if($result->num_rows > 0) {
        $_SESSION['status'] = "Email already exists. Please use a different email.";
        header("Location: signup.php");
        exit();
    }

    $insert = "INSERT INTO users (email, password, verify_token, email_pass) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert);
    $password_hash = password_hash($password, PASSWORD_DEFAULT); // Hash the password before storing
    $stmt->bind_param("ssss", $email, $password_hash, $verify_token, $email_pass);
    $query_run = $stmt->execute();

    if ($query_run) {
        $email_sent = sendemail_verify($email, $verify_token);
        
        if ($email_sent) {
            echo "<script>
                alert('Registration Complete! Please verify your email address.');
                window.location.href = 'index.php ';
            </script>";
        } else {
            echo "<script>
                alert('Registration Complete! But verification email could not be sent. " . ($_SESSION['mail_error'] ?? '') . "');
                window.location.href = 'signup.php';
            </script>";
        }
        exit();
    } else {
        echo "<script>
            alert('Registration Failed: " . $conn->error . "');
            window.location.href = 'signup.php';
        </script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link rel="shortcut icon" href="img/OIP.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body>
    <img style="z-index: -2; position: absolute; width: 100%; height: 110%;" src="img/ECS-BG.png" alt="">
   
    <div class="container vh-100 d-flex align-items-center">
        <div class="row w-100">
            <!-- Signup Form -->
            <div class="col-md-6 d-flex align-items-center justify-content-center form-column">
                <div class="signup-container">
                    <div class="signup-header">
                        <h1 class="text-white" style="font-weight: 700;">REGISTER</h1><br>
                        <p class="text-white">Create your account</p>
                    </div>
                   
                    <!-- Improved notification display -->
                    <?php if(isset($_SESSION['status'])): 
                        $alertClass = isset($_SESSION['status_type']) ? 
                            ($_SESSION['status_type'] == "success" ? "alert-success" : 
                            ($_SESSION['status_type'] == "warning" ? "alert-warning" : "alert-danger")) : 
                            "alert-danger";
                            
                        $iconClass = isset($_SESSION['status_type']) ? 
                            ($_SESSION['status_type'] == "success" ? "bi-check-circle-fill" : 
                            ($_SESSION['status_type'] == "warning" ? "bi-exclamation-circle-fill" : "bi-exclamation-triangle-fill")) : 
                            "bi-exclamation-triangle-fill";
                    ?>
                    <div class="alert <?= $alertClass ?> alert-dismissible fade show notification-alert" role="alert">
                        <i class="bi <?= $iconClass ?> me-2"></i>
                        <strong><?= $_SESSION['status'] ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php 
                        unset($_SESSION['status']); 
                        unset($_SESSION['status_type']);
                    ?>
                    <?php endif; ?>
                   
                    <form name="signupForm" action="" method="POST" onsubmit="return validateForm()">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                            <label for="floatingEmail">Email Address</label>
                        </div>
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                            <label for="floatingPassword">Password</label>
                            <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                        </div>
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                            <label for="floatingPassword">Confirm Password</label>
                            <i class="bi bi-eye-slash password-toggle" id="toggleConfirmPassword"></i>
                        </div>
                        <div class="form-floating mb-3 position-relative">
                            <input type="text" class="form-control" name="email_pass" id="email_pass" placeholder="Email Code" required>
                            <label>Email Code</label>
                        </div>
                        <div id="password-feedback" class="invalid-feedback" style="display: none;">
                            Passwords do not match.
                        </div>
                        <br>
                        <div class="text-center">
                            <button type="submit" name="signup_btn" class="custom-signup-btn">Sign Up</button>
                        </div>
                        <div class="text-center mt-3">
                            <p class="small-text">Already have an account? 
                                <a href="index.php" class="text-white login-link">Login</a>
                            </p>
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
    
    // Toggle password visibility for password field
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this;
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    });
    
    // Toggle password visibility for confirm password field
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const confirmPasswordInput = document.getElementById('confirm_password');
        const icon = this;
        
        if (confirmPasswordInput.type === 'password') {
            confirmPasswordInput.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            confirmPasswordInput.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    });
    
    // Auto-dismiss alerts after 5 seconds
    window.addEventListener('DOMContentLoaded', (event) => {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
    </script>
</body>
</html>
