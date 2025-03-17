<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container vh-100 d-flex align-items-center">
        <div class="row w-100">

            <!-- Left Side: Login Form -->
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <div class="login-container">
                    <div class="login-header">
                        <h2 class="text-primary" style="font-weight: 700;">DTI <br> E-CERTIFICATION <br> SYSTEM</h2>
                        <p class="text-white">Welcome to DTI</p>
                    </div>
                    
                    <form>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="floatingEmail" placeholder="name@example.com" required>
                            <label for="floatingEmail">Email Address</label>
                        </div>
                        
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                            <label for="floatingPassword">Password</label>
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <a href="#" class="text-primary">Forgot password?</a>
                        </div>
                        
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg" type="submit">Login</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Side: Infinite Scrolling Vertical Image Slider -->
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <div class="slider-container">
                    <div class="image-slider">
                        <img src="img/cert.png" alt="Certificate">

                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                        
                        <img src="img/cert.png" alt="Certificate">
                        <img src="img/cert.png" alt="Certificate">
                    </div>
                </div>
            </div>

        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
