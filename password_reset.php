<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="shortcut icon" href="img/OIP.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">    
</head>
<body>
    <img style="z-index: -2; position: absolute; width: 100%; height: 110%;" src="img/ECS-BG.png" alt="">
    
    <div class="container vh-100 d-flex align-items-center justify-content-center">
        <div class="col-md-4 p-4 shadow rounded text-center">
            <h2 class="text-white fw-bold">DTI E-CERTIFICATION SYSTEM</h2>
            <p class="text-white mb-4">Enter your email to reset your password</p>
            
            <form action="password_reset_code.php" method="POST">
                <div class="form-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Enter Email Address" required>
                </div>
                <div class="form-group mb-3">
                    <button type="submit" name="password_reset" class="btn btn-primary">Send Password Reset Link</button>
                </div>
            </form>
            
            <a href="index.php" class="password-reset-back-link">Back to Login</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
