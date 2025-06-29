<?php
session_start();
include("dbcon.php");

if(isset($_GET['token'])){
    $token = $_GET['token'];
    $verify_query = "SELECT verify_token, verify_status FROM users WHERE verify_token ='$token' LIMIT 1";
    $verify_query_run = mysqli_query($conn, $verify_query);

    if(mysqli_num_rows($verify_query_run) > 0){
        $row = mysqli_fetch_array($verify_query_run);
        if($row['verify_status'] == 0){
            $clicked_token = $row['verify_token'];
            $update_query = "UPDATE users SET verify_status='1' WHERE verify_token='$clicked_token' LIMIT 1";
            $update_query_run = mysqli_query($conn, $update_query);

            if($update_query_run){
                $_SESSION['status'] = "Your Account has been verified successfully!";
                $_SESSION['status_type'] = "success"; // This will make the notification green
                header("Location: index.php");
                exit(0);
            }
            else{
                $_SESSION['status'] = "Verification Failed!";
                $_SESSION['status_type'] = "error"; // This will make the notification red
                header("Location: index.php");
                exit(0);
            }
        }else{
            $_SESSION['status'] = "Email Already Verified. Please Login!";
            $_SESSION['status_type'] = "info"; // This will make the notification blue
            header("Location: index.php");
            exit(0);
        }
    }else{
        $_SESSION['status'] = "This token does not exists";
        $_SESSION['status_type'] = "error"; // This will make the notification red
        header("Location: index.php");
    }
}
else{
    $_SESSION['status'] = "Not Allowed";
    $_SESSION['status_type'] = "error"; // This will make the notification red
    header("Location: index.php");
}    
?>
