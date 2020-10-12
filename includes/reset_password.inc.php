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

if (isset($_POST['reset_email'])){
    $reset_email = $_POST['reset_email'];
    $error_log = array();

    // Check for registered email
    if (occurences($conn, "email", $reset_email) == 0){
        $error_log['reset_email'] = "This email is not registered with any account\n";
        return $error_log;
    }

    $error_log['script'] = "<script type='text/javascript'>$('#loginform').slideUp(0);$('#loginform').fadeIn(0);</script>";

    // Check if the user entered a correct email
    if (!isset($error_log['reset_email'])){

        // Generate reset token
        $token = get_token(10);
        while (occurences($conn, 'token', $token) > 0){
            $token = get_token(10);
        }
        mysqli_query($conn, "UPDATE users SET token='$token' WHERE email='$reset_email'");
        $name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE email = '$reset_email'"))['username'];

        // Set up PHPMailer object


        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = 'crowdfluenceapp@gmail.com';
        $mail->Password = 'qtDa5LOM';
        $mail->SMTPSecure = "ssl";
        $mail->Port = 465;
        $mail->SetFrom("crowdfluenceapp@gmail.com", "LeadFeast Support");
        $mail->addAddress($reset_email, $name);
        $mail->Subject = "Password Reset";
        $mail->isHTML();
        $mail->Body = "
            Dear $name,
    	    <br><br>
    	    You recently asked to reset your password. To do so, please <a href = 'app.leadfeast.com/reset_password.php'>click here</a> and you will be taken
    	    to our password reset page. You will then be prompted to enter your email address and your new password, along with the verification code.
    	    <br><br>
    	    If you didn't ask us to reset your password, please let us know at <a href = 'mailto:support@crowdfluence.app'>support@crowdfluence.app</a>
    	    <br><br>
    	    <b>Enter this verification code on the password reset page:</b> <b>$token</b>
    	    <br><br>
    		Sincerely,
    		<br><br>
    		The LeadFeast Team
    	";
        if ($mail->send()){
            unset($mail);
            echo"<script>window.location.href='login.php';</script>";
        } else {
            unset($mail);
            $error_log['reset_email'] = "Email was not sent. Something went wrong.";
            return $error_log;
        }
    } else {
        return $error_log;
    }
}