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
                        <div class="text-center mt-3">
                            <span class="small-text">Already have an account? <a href="index.php" class="login-link text-white">Login</a></span>
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
    document.addEventListener("DOMContentLoaded", function () {
        const certificateColumn = document.getElementById("certificateColumn");

        function scrollCertificates() {
            certificateColumn.scrollBy({
                top: certificateColumn.clientHeight, // Scroll by one full container height
                behavior: "smooth"
            });
        }

        // Attach event listeners to sign-up and login links
        document.querySelector(".signup-link")?.addEventListener("click", scrollCertificates);
        document.querySelector(".login-link")?.addEventListener("click", scrollCertificates);
    });
</script>
</body>
</html>
