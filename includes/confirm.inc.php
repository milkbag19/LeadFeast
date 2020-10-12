<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require 'PHPMailer-master/PHPMailer-master/src/Exception.php';
    require 'PHPMailer-master/PHPMailer-master/src/OAuth.php';
    require 'PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
    require 'PHPMailer-master/PHPMailer-master/src/POP3.php';
    require 'PHPMailer-master/PHPMailer-master/src/SMTP.php';
	// Connection to database
	include_once('config.php');
	$conn = mysqli_connect(DB_SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
	
	// Check for valid call
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])){
	    
	    // Retrieve token and password
	    $token = mysqli_real_escape_string($conn, stripslashes($_POST['token']));
	    $pwd = mysqli_real_escape_string($conn, stripslashes($_POST['pwd']));
	    
	    $entry = mysqli_query($conn, "SELECT * FROM users WHERE token = '$token' AND verified = 0");
	    
	    // Check if valid account exists
	    if ($entry->num_rows > 0){
	        $row = mysqli_fetch_assoc($entry);
	        
	        // Verify password
	        if (password_verify($pwd, $row['password'])){
	            mysqli_query($conn, "UPDATE users SET verified = 1, token = '' WHERE token = '$token'");
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = "smtp.gmail.com";
                $mail->SMTPAuth = true;
                $mail->Username = 'crowdfluenceapp@gmail.com';
                $mail->Password = 'qtDa5LOM';
                $mail->SMTPSecure = "ssl";
                $mail->Port = 465;
                $mail->SetFrom("crowdfluenceapp@gmail.com", "LeadFeast Support");
                $mail->addAddress($row['email'], $row['username']);
                $mail->Subject = "Account Confirmed";
                $mail->isHTML();
                $mail->Body = "
                    Thank you for Confirming your account!<br><br>
            
                    You may now login!<br><br>
            
                    Sincerely,<br><br>
            
                    The Crowdfluence Team
                ";

                // Check if email was sent successfully
                if ($mail->send()){
                    unset($mail);
                    echo "<script>window.location.href='../login.php'</script>";
                } else {
                    unset($mail);
                    $error_log['script'] = "email failed to send.\n";
                }

	        } else {
	            header("Location: ../confirm.php");
	        }
			
		} else {
			header("Location: ../confirm.php");
		}
		mysqli_free_result($entry);
		exit();

	} else {
		
		echo "<p>Something went wrong. Please try again.</p>";
		exit();
	}