<?php
  //headers
  header ('Access-Control-Allow-Origin: *');
  header ('Content-Type: application/json');
  header ('Access-Control-Allow-Methods: POST');
  header ('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Method, Authorization, X-Requested-With');
  include_once '../includes/Database.php';
  include_once '../includes/config.php';

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  use PHPMailer\PHPMailer\SMTP;

  require '../includes/PHPMailer-master/PHPMailer-master/src/Exception.php';
  require '../includes/PHPMailer-master/PHPMailer-master/src/OAuth.php';
  require '../includes/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
  require '../includes/PHPMailer-master/PHPMailer-master/src/POP3.php';
  require '../includes/PHPMailer-master/PHPMailer-master/src/SMTP.php';

  
  
try{
  //instantiate DB & connect
  $database = new Database();
  $db = $database->connect();
  require_once '../includes/htmlpurifier/library/HTMLPurifier.auto.php';
  $purifier = new HTMLPurifier();
  //instantiate user object
  //$user = new User($db);

  if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $return = [];

    //get raw posted data
    $register = json_decode(file_get_contents("php://input"));

    //filter inputs
    $email = $purifier->purify(mysqli_real_escape_string($db, stripslashes($register->email)));
    $password = $purifier->purify(mysqli_real_escape_string($db, stripslashes($register->password)));
    $confirm_password = $purifier->purify(mysqli_real_escape_string($db, stripslashes($register->confirm_password)));

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      //email is not valid
      http_response_code(401);
      $return['error'] = http_response_code(401);
      echo "Invalid email address.";

    } else {
      //email is valid
      try{
      $user_found = $database->find_row($db, 'users', 'email', $email);
      } catch(Exception $e){echo $e;}
      if($user_found){
        //user already exists
        http_response_code(400);
        $return['error'] = http_response_code(400);

      } else {
        //user does not exist. start adding them

        if($password == $confirm_password){
          //password confirmed and email valid. register user

          $token = get_token(10);

          // Add system that checks for existing tokens
          while (!unique_value($conn, 'token', $token)){
          $token = get_token(10);
          }
          
          $password = password_hash($password, PASSWORD_DEFAULT);
          $params = array({});

          if($database->insert_values($db, 'users', $params)) {
            //user has been registered
            http_response_code(200);
            $return['ok'] = http_response_code(200);

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = '';
            $mail->Password = '';
            $mail->SMTPSecure = "ssl";
            $mail->Port = 465;
            $mail->SetFrom("", "LeadFeast Support");
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

          } else {
            echo json_encode(
              array('message' => 'User Was Not Registered.')
            );

          }
        } else {
          //password was not confirmed
          http_response_code(401);
          $return['error'] = http_response_code(401);
          echo "Incorrect authentication information provided.";
        }
      }
    }

    echo json_encode($return, JSON_PRETTY_PRINT); exit;
  } else {
    //exit. redirect the user. etc.
    exit('Invalid URL');
  }
} catch(Exception $g){echo $g;}
?>
