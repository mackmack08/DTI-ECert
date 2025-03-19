<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- Fixed background image - same as index.php -->
    <img style="z-index: -2; position: fixed; width: 100%; height: 110%; top: 0; left: 0;" src="img/DTI-BACKGROUND.png" alt="">
   
    <div class="container vh-100 d-flex align-items-center justify-content-center">
        <div class="col-md-4 p-4 shadow rounded text-center">
            <h2 class="text-white fw-bold">DTI E-CERTIFICATION SYSTEM</h2>
            <form action="password_reset_code.php" method="POST">
                <input type="hidden" name="password_token" value="<?php if(isset($_GET['token'])){echo$_GET['token'];} ?>">

                <div class="form-group mb-3">
                    <input type="text" name="email" value="<?php if(isset($_GET['email'])){echo$_GET['email'];} ?>" class="form-control" placeholder="Enter Email Address">
                </div>
                <div class="form-group mb-3">
                    <input type="password" name="new_password" class="form-control" placeholder="Enter New Password">
                </div>
                <div class="form-group mb-3">
                    <input type="password" name="confirm_password" class="form-control" placeholder="Enter Confirm Password">
                </div>
                <div class="form-group mb-3">
                    <button type="submit" name="password_update" class="btn btn-primary">Update Password</button>
                </div>
            </form>
            <a href="index.php" class="password-reset-back-link">Back to Login</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
