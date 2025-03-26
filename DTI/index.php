<?php
include("dbcon.php");
include("logincode.php");
if (isset($_POST['login_btn'])) {   
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to get the user by email
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            $_SESSION['userId'] = $user['id']; // You can store user ID or other details in session
            $_SESSION['email'] = $user['email'];
            $_SESSION['status'] = "Login successful!";

        } else {
            // Password is incorrect
            $_SESSION['status'] = "Invalid email or password.";
            header("Location: index.php");
            exit();
        }
    } else {
        // Email not found
        $_SESSION['status'] = "Invalid email or password.";
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <img style="z-index: -2; position: absolute; width: 100%; height: 110%;" src="img/DTI-BACKGROUND.png" alt="">
   
    <div class="container vh-100 d-flex align-items-center">
        <div class="row w-100">
            <!-- Login Form -->
            <div class="col-md-6 d-flex align-items-center justify-content-center form-column">
                <div class="login-container">
                    <div class="login-header">
                        <br>
                        <h1 class="text-white fw-bold">DTI <br> E-CERTIFICATION <br> SYSTEM</h1> <br>
                        <p class="text-white" style="text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6); font-weight: 1000px">Welcome Back, Administrator!</p>
                    </div>
                   
                    <form action="dashboard.php" method="POST">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" name="email" placeholder="name@example.com" required>
                            <label for="floatingEmail">Email Address</label>
                        </div>
                       
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" name="password"  placeholder="Password" required>
                            <label for="floatingPassword">Password</label>
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
                                    $result = $con->query("SELECT COUNT(*) as user_count FROM users");
                                
                                    // Check if query was successful
                                    if ($result) {
                                         $row = $result->fetch_assoc();
                                        $user_count = isset($row['user_count']) ? $row['user_count'] : 0;
                                    }
                                if ($user_count == 0): ?>
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
</body>
</html>
