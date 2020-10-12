<?php

// Initialize
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer-master/PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/PHPMailer-master/src/OAuth.php';
require 'PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/PHPMailer-master/src/POP3.php';
require 'PHPMailer-master/PHPMailer-master/src/SMTP.php';

$error_log = array();

//Initiate purifier
require_once 'htmlpurifier/library/HTMLPurifier.auto.php';
$purifier = new HTMLPurifier();

// Check if user pressed submit
if (isset($_POST['submit']) && isset($_POST['email'])){

    // Retrieve user data
    $email = $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_POST['email'])));
    $password = $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_POST['pwd'])));

    // Check if email exists
    if (occurences($conn, "email", $email) == 0){
        $error_log['email'] = "This email is not registered with any account\n";
        return $error_log;

    } else {
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND `role` = 'user'"));

        // Check if account has been verified
        if ($row['verified'] == 0){
            $error_log['email'] = "Your account has not been verified yet\n";
        }
        // Check if password is correct
        if (!password_verify($password, $row['password'])){
            $error_log['pwd'] = "Incorrect password\n";
        }

        if (!empty($error_log)){
            unset($row);
            mysqli_close($conn);
            return $error_log;

            // Start session after valid authentication
        } else {
            $id = $row['id'];
            $pay_level = $row['payment_level'];
            $trial = $row['trial'];


            session_start();
            if (isset($row['access_token'], $row['access_secret'], $row['consumer_key'], $row['consumer_secret']) && $row['access_token'] !== '' && $row['access_secret'] !== '' && $row['consumer_key'] !== '' && $row['consumer_secret'] !== '') {
                $_SESSION['has_credentials'] = true;
            } else {
                $_SESSION['has_credentials'] = false;
            }
            unset($row);
            $_SESSION['id'] = $id;
            $_SESSION['pay_level'] = $pay_level;
            $_SESSION['trial'] = $trial;
            echo"<script>window.location.href='./index.php'</script>";
        }
    }

// Reset password
}
