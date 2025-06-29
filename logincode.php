<?php
ob_start();
session_start(); // Start the session
include("dbcon.php"); // Include the database connection


date_default_timezone_set('Asia/Manila'); // Set timezone to Philippines

if (isset($_POST['login_btn'])) {
    if (!empty(trim($_POST['email'])) && !empty(trim($_POST['password']))) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Use prepared statement to prevent SQL injection
        $login_query = "SELECT * FROM users WHERE email='$email'";
        $login_query_run = mysqli_query($conn, $login_query);

        if (mysqli_num_rows($login_query_run) > 0) {
            $row = mysqli_fetch_array($login_query_run);

            if (password_verify($password, $row['password'])) {
                if ($row['verify_status'] == "1") {
                    // Set session variables for authenticated user
                    $_SESSION['authenticated'] = TRUE;
                    $_SESSION['auth_user'] = [
                        'userId' => $row['id'],
                        'email' => $row['email']
                    ];
                    
                        $query = "SELECT id FROM users WHERE email = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $stmt->bind_result($id);
                        $stmt->fetch();
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['userId'] = $row['id'];
                        $_SESSION['status'] = "Login Successful";
                        $stmt->close();

                    // Redirect to dashboard after successful login
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $_SESSION['status'] = "Please verify your email first.";
                    header("Location: index.php");
                    exit();
                }
            } else {
                $_SESSION['status'] = "Invalid email or password.";
                header("Location: index.php");
                exit();
            }
        } else {
            $_SESSION['status'] = "Your email is not registered.";
            header("Location: index.php");
            exit();
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['status'] = "All fields are required.";
        header("Location: index.php");
        exit();
    }
}
?>