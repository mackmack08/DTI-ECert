<?php
session_start(); // Start the session
include("dbcon.php"); // Include the database connection

date_default_timezone_set('Asia/Manila'); // Set timezone to Philippines

if (isset($_POST['login_btn'])) {
    if (!empty(trim($_POST['email'])) && !empty(trim($_POST['password']))) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Query to fetch user with the given email
        $login_query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($login_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $row['password'])) {
                if ($row['verify_status'] == "1") {
                    // Set session variables for authenticated user
                    $_SESSION['authenticated'] = true;
                    $_SESSION['auth_user'] = [
                        'id' => $row['id'],
                        'email' => $row['email']
                    ];
                    // Redirect to dashboard
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $_SESSION['status'] = "Please verify your email first.";
                }
            } else {
                $_SESSION['status'] = "Invalid email or password.";
            }
        } else {
            $_SESSION['status'] = "Your email is not registered.";
        }
        $stmt->close();
    } else {
        $_SESSION['status'] = "All fields are required.";
    }

    // Redirect back to the login page
    header("Location: index.php");
    exit();
}
?>
