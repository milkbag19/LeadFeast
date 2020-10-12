<?php

// Include script for connecting to database
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer-master/PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/PHPMailer-master/src/OAuth.php';
require 'PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/PHPMailer-master/src/POP3.php';
require 'PHPMailer-master/PHPMailer-master/src/SMTP.php';

//Initiate purifier
require_once 'htmlpurifier/library/HTMLPurifier.auto.php';
$purifier = new HTMLPurifier();

// This function checks for unique values in a given column of a table
function unique_value($conn, $column_name, $value){
    $returnValue = true;

    $entries = mysqli_query($conn, "SELECT * FROM users WHERE $column_name = '$value'");

    if ($entries->num_rows > 0){
        $returnValue = false;
    }
    mysqli_free_result($entries);
    return $returnValue;
}

// Preload namespaces


// require_once('../recaptchalib.php');

// Check if user pressed the submit button or not
if (isset($_POST['submit'])){

    // Initialize
    $error_log = array();
    $hasError = false;

    // Retrieve user data
    $name =  $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_POST['name'])));
    $email = $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_POST['email'])));
    $pwd = $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_POST['pwd'])));
    $confirm_pwd = $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_POST['confirm_pwd'])));



    // ERROR HANDLERS
    // Check password security
    if (!empty(checkPwd($pwd))){
        $error_log['pwd'] = "Password too weak.\n";
        $hasError = true;
    }

    // Check that passwords match
    if ($pwd != $confirm_pwd){
        $error_log['pwd'] .= "Passwords must match.\n";
        $error_log['confirm_pwd'] = "Passwords must match.\n";
        $hasError = true;
    }

    // Check for valid email address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error_log['email'] = "Invalid email address.\n";
        $hasError = true;
    }

    // Check for existing email
    if (occurences($conn, 'email', $email) > 0){
        if (isset($error_log['email'])){
            $error_log['email'] .= "Email already exists.\n";
        } else {
            $error_log['email'] = "Email already exists.\n";
        }
        $hasError = true;
    }
    if ($name == ""){
        $error_log['name'] = "Please provide a username.\n";
        $hasError = true;
    }

    // Check that user agreed to Terms and Conditions
    /*if (!isset($_POST['chkbox'])){
        $error_log['chkbox'] = "Please agree to Terms and Conditions.\n";
        $hasError = true;
        echo "<p>chkbox</p>";
    }*/

    // Check reCAPTCHA
    //if (!$jsonResponse["success"]){
    /*if (false){
        $error_log['reCAPTCHA'] = "Please check the reCAPTCHA box.\n";
        $hasError = true;
        /*echo "<p>" . json_encode($jsonResponse) . "</p>";
        echo "<p>done</p>";
    }*/

    // Print error log or proceed to signup script
    if ($hasError){
        return $error_log;
    }

    // ADDING NEW USER
    // Begin user integration once all fields are validated
    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
    $token = get_token(10);

    // Add system that checks for existing tokens
    while (!unique_value($conn, 'token', $token)){
        $token = get_token(10);
    }

    // Insert user into the database
    $query = "INSERT INTO users (username, role, email, password, token, verified, signup_date, payment_level)
				VALUES ('$name', 'user', '$email', '$hashedPwd', '$token', 0, '".date("Y/m/d") ."', 0);";
    $result = mysqli_query($conn, $query);
    if(!$result) {
        die($conn->error);
    }
    header("Location: ../login.php");

    // Send email
    //include_once("PHPMailer/PHPMailer.php");

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
    $mail->addAddress($email, $name);
    $mail->Subject = "Account Verification";
    $mail->isHTML();
    $mail->Body = "
		Thank you for registering your brand with LeadFeast! Please click on the link below verify your email and activate your account<br><br>

		<a href = 'https://app.leadfeast.com/confirm.php?token=$token'>Click Here</a><br><br>

		Sincerely,<br><br>

		The Crowdfluence Team
	";

    // Check if email was sent successfully
    if ($mail->send()){
        unset($mail);
        header("Location: ../login.php");
    } else {
        unset($mail);
        $error_log['script'] = "email failed to send.\n";
    }
    return [];
    exit();
}

/* else {

	// User did not trigger this script by pressing the submit button.
	// Redirect to home page
	header("Location: ../signup.html");
	exit();
}*/



