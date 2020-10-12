<?php

// Include script for connecting to database
include_once 'config.php';

//Initiate purifier
require_once 'htmlpurifier/library/HTMLPurifier.auto.php';
$purifier = new HTMLPurifier();

// Preload namespaces

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer-master/PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/PHPMailer-master/src/OAuth.php';
require 'PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/PHPMailer-master/src/POP3.php';
require 'PHPMailer-master/PHPMailer-master/src/SMTP.php';

// Check if user pressed the submit button or not
if (isset($_POST['submit'])){

    // Initialize
    $conn = mysqli_connect(DB_SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME) or die (mysqli_connect_errno());
	$error_log = array();
	$hasError = false;

	// Retrieve user data
	$email = $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_POST['email'])));
	$pwd = $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_POST['pwd'])));
	$confirm_pwd = $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_POST['confirm_pwd'])));
	$token = $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_POST['token'])));
	$match_query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND token='$token'") or die($conn->error);

	// Retrieve reCAPTCHA data

	// ERROR HANDLERS
    // Check password security
    if (!empty(checkPwd($pwd))){
	    $error_log['pwd'] = checkPwd($_POST['pwd']);
	    $hasError = true;
    }

    // Check that passwords match
	if ($pwd != $confirm_pwd){
		$error_log['pwd'] .= "Passwords must match.";
		$error_log['confirm_pwd'] = "Passwords must match.\n";
	    $hasError = true;
	}

	// Check for registered email address
	if (occurences($conn, 'email', $email) == 0){
	    $error_log['email'] = "This email is not registered with any account.\n";
	    $hasError = true;
	}

	// Check reCAPTCHA
	//if (!$jsonResponse["success"]){
	if (false) {
	    $error_log['reCAPTCHA'] = "Please check the reCAPTCHA box.";
	    $hasError = true;
	}

	// Verify code
	$num = $match_query->num_rows;
	if ($num == 0){
	    $error_log['token'] = "Verification code does not match name and email";
	    $error_log['name'] = "Verification code does not match name and email";
	    $error_log['email'] = "Verification code does not match name and email";
	    $hasError = true;
	}

	// Print error log or proceed to signup script
	if ($hasError){
	    return $error_log;
	}

	// RESET PASSWORD
	// Begin user integration once all fields are validated
	$hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

	// Insert new password into the database
	$query = "UPDATE users SET password='$hashedPwd', token = '' WHERE email = '$email'";
	mysqli_query($conn, $query);

	// Send email
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = 'crowdfluenceapp@gmail.com';
    $mail->Password = 'qtDa5LOM';
    $mail->SMTPSecure = "ssl";
    $mail->Port = 465;
    $mail->SetFrom("crowdfluenceapp@gmail.com", "LeadFeast Support");
    $mail->addAddress($email, 'LeadFeast User');
    $mail->Subject = "Password Reset";
    $mail->isHTML();
	$mail->Body = "
        Dear user,
        <br><br>
		We have successfully reset your password. You can now log in to your account and use LeadFeast.
		<br><br>
		If you did not reset your password, please let us know at <a href = 'mailto:support@crowdfluence.app'>support@crowdfluence.app</a>
		Sincerely,
		<br><br>
		The LeadFeast Team
	";
        	if ($mail->send()){
        	    unset($mail);
        	    echo"<script>window.location.href='./login.php';</script>";
        	    return [];
        	} else {
        	    unset($mail);
        	    $error_log['reset_email'] = "Email was not sent. Something went wrong.";
        	    return $error_log;
        	}

}
