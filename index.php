<?php
include("dbcon.php"); // Include database connection
include("logincode.php"); // Login logic

if (isset($_POST['login_btn'])) {   
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to get the user by email
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            $_SESSION['userId'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['status'] = "Login successful!";
            $_SESSION['status_type'] = "success";
    

            // Redirect to dashboard after successful login
            header("Location: dashboard.php");
            exit();
        } else {
            // Password is incorrect
            $_SESSION['status'] = "Invalid email or password.";
            $_SESSION['status_type'] = "error";
            header("Location: index.php");
            exit();
        }
    } else {
        // Email not found
        $_SESSION['status'] = "Invalid email or password.";
        $_SESSION['status_type'] = "error";
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
    <title>Login Page</title>
    <link rel="shortcut icon" href="img/OIP.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <img style="z-index: -2; position: absolute; width: 100%; height: 110%;" src="img/ECS-BG.png" alt="">
   
    <div class="container vh-100 d-flex align-items-center">
        <div class="row w-100">
            <!-- Login Form -->
            <div class="col-md-6 d-flex align-items-center justify-content-center form-column">
                <div class="login-container">
                    <div class="login-header">
                        <br>
                        <?php
                        if (isset($_SESSION['status'])) {
                            $alertClass = isset($_SESSION['status_type']) && $_SESSION['status_type'] == "success" 
                                ? "alert-success" 
                                : "alert-danger";
                            $iconClass = isset($_SESSION['status_type']) && $_SESSION['status_type'] == "success"
                                ? "bi-check-circle-fill"
                                : "bi-exclamation-triangle-fill";
                        ?>
                            <div class="alert <?= $alertClass ?> alert-dismissible fade show notification-alert" role="alert">
                                <i class="bi <?= $iconClass ?> me-2"></i>
                                <strong><?= $_SESSION['status'] ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php
                            unset($_SESSION['status']);
                            unset($_SESSION['status_type']);
                        }
                        ?>
                        <h1 class="text-white fw-bold">DTI <br> E-CERTIFICATION <br> SYSTEM</h1> <br>
                        <p class="text-white" style="text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6); font-weight: 1000px">Welcome Back, Administrator!</p>
                    </div>
                   
                    <form action="logincode.php" method="POST">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" name="email" placeholder="name@example.com" required>
                            <label for="floatingEmail">Email Address</label>
                        </div>
                       
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                            <label for="floatingPassword">Password</label>
                            <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                        </div>

                        <div class="d-flex justify-content-end mb-3">
                             <a href="password_reset.php" class="text-white" style="text-decoration: none; text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6); font-size: 15px;">Forgot password?</a>
                        </div> <br>
                       
                        <div class="form-group text-center">
                            <button type="submit" name="login_btn" class="btn custom-login-btn">Login</button>
                        </div>
                        <?php 
                            $user_count = 0;

                            // Count users in the database
                            $result = $conn->query("SELECT COUNT(*) as user_count FROM users");
                        
                            // Check if query was successful
                            if ($result) {
                                $row = $result->fetch_assoc();
                                $user_count = isset($row['user_count']) ? $row['user_count'] : 0;
                            }
                            if ($user_count == 0): 
                        ?>
                        <div class="text-center mt-3">
                            <p class="small-text">Don't have an account?
                             <a href="signup.php" class="text-white signup-link">Sign up</a>
                            </p>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Right Side: Infinite Scrolling Vertical Image Slider -->
            <div class="col-md-6 certificate-column" id="certificateColumn">
                <div class="slider-container">
                    <div class="image-slider">
                        <img src="img/SampleCertificate1.png" alt="Certificate">
                        <img src="img/SampleCertificate2.png" alt="Certificate">
                        <img src="img/SampleCertificate3.png" alt="Certificate">
                        <img src="img/SampleCertificate4.png" alt="Certificate">
                        <img src="img/SampleCertificate5.png" alt="Certificate">
                        <img src="img/SampleCertificate6.png" alt="Certificate">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
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
