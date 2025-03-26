<?php
session_start();
include("dbcon.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

function sendemail_verify($email, $verify_token) {
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0; // Set to 2 to enable debug output
    $mail->isSMTP();                                             // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';  
    $mail->SMTPAuth   = true;
    $mail->Username   = 'idontplayinvoker@gmail.com';                     // SMTP username
    $mail->Password   = 'crjpoellqbxjawrq';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            // Enable implicit TLS encryption
    $mail->Port       = 587;

    $mail->setFrom('idontplayinvoker@gmail.com', 'DTI-Cebu Provincial');
    $mail->addAddress($email);
 
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Email Verification';

    $email_template = "
        <h2>You have registered with DTI-Provincial Office E-Ceritificate System</h2>
        <h4>Verify your email address to login using the link below:</h4>
        <br><br>
        <a href='http://localhost/dti/verifyemail.php?token=$verify_token'>Verify Email</a>";
    $mail->Body = $email_template;

    try {
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
    $stmt_check = $con->prepare($check_email);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if($result->num_rows > 0) {
        $_SESSION['status'] = "Email already exists. Please use a different email.";
        header("Location: signup.php");
        exit();
    }

    $insert = "INSERT INTO users (email, password, verify_token) VALUES (?, ?, ?)";
    $stmt = $con->prepare($insert);
    $password_hash = password_hash($password, PASSWORD_DEFAULT); // Hash the password before storing
    $stmt->bind_param("sss", $email, $password_hash, $verify_token);
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
            alert('Registration Failed: " . $con->error . "');
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body>
    <img style="z-index: -2; position: absolute; width: 100%; height: 110%;" src="img/DTI-BACKGROUND.png" alt="">
   
    <div class="container vh-100 d-flex align-items-center">
        <div class="row w-100">
            <!-- Signup Form -->
            <div class="col-md-6 d-flex align-items-center justify-content-center form-column">
                <div class="signup-container">
                    <div class="signup-header">
                        <h1 class="text-white" style="font-weight: 700;">REGISTER</h1><br>
                        <p class="text-white">Create your account</p>
                    </div>
                   
                    <form name="signupForm" action="" method="POST" onsubmit="return validateForm()">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                            <label for="floatingEmail">Email Address</label>
                        </div>
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                            <label for="floatingPassword">Password</label> 
                        </div>
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                            <label for="floatingPassword">Confirm Password</label>
                        </div>
                        <br>
                        <div class="text-center">
                            <button type="submit" name="signup_btn" class="custom-signup-btn">Sign Up</button>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"> <script>
</body>
</html>
