<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container vh-100 d-flex align-items-center justify-content-center">
        <div class="col-md-4 p-4 shadow rounded text-center ">
            <h2 class="text-white fw-bold">DTI E-CERTIFICATION SYSTEM</h2>
            
            
            <form>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingEmail" placeholder="name@example.com" required>
                    <label class="text-dark" for="floatingEmail">Email Address</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingPassword" placeholder="New_Password" required>
                    <label class="text-dark" for="floatingPassword">New Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingPassword" placeholder="Confirm_Password" required>
                    <label class="text-dark" for="floatingPassword">Confirm Password</label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Update Password</button>
            </form>
        </div>
        <img style="z-index: -2; position: absolute; width: 100%; height: 110%; border-radius: 50%; margin-left: -70%;" src="img/bg.jpg" alt="">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
