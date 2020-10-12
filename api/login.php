<?php
  //headers
  header ('Access-Control-Allow-Origin: *');
  header ('Content-Type: application/json');
  header ('Access-Control-Allow-Methods: POST');
  header ('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Method, Authorization, X-Requested-With');

  include_once '../includes/Database.php';
  require_once '../includes/htmlpurifier/library/HTMLPurifier.auto.php';
  $purifier = new HTMLPurifier();
  //instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  //instantiate user object


  //generate random string of characters for hard-coded API key
  $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $api_key = substr(str_shuffle($permitted_chars), 0, 8);

  if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $return = [];

    //get raw posted data
    $login = json_decode(file_get_contents("php://input"));

    $email = $purifier->purify(mysqli_real_escape_string($db, stripslashes($login->email)));
    $password = $purifier->purify(mysqli_real_escape_string($db, stripslashes($login->password)));
    var_dump($email);
    $user_found = $database->find_row($db, 'users', 'email', $email);

    if($user_found){
      //user exists. try and log them in

      if(password_verify($password, $user_found['password'])){
        //user password is correct
        //match API key

        if($api_key == $api_key) {
          //api keys match
          //check if account verified

          if($user_found['verified'] == 1) {
            //account is verified
            http_response_code(200);
            $return['ok'] = http_response_code(200);
            echo "Logged in.";

            //create array of user info to send
            $user_info = array(
              'id'=> $user_found['id'],
              'email'=> $user_found['email'],
              'payment_level'=> $user_found['payment_level']
            );

            //make JSON
            print_r(json_encode($user_info));

          } else {
            //account not verified
            http_response_code(403);
            $return['error'] = http_response_code(403);
            echo "Account is not verified.";
          }

        } else {
          //api key does not match
          http_response_code(500);
          $return['error'] = http_response_code(500);
          echo "Authentication failed.";
        }

      } else {
        //password is incorrect
        http_response_code(401);
        $return['error'] = http_response_code(401);
        echo "Incorrect authentication information provided.";
      }

    } else {
      //user does not exist. they need to create a new account
      http_response_code(402);
      $return['error'] = http_response_code(402);
      echo "No account found with authentication information provided.";
    }

    echo json_encode($return, JSON_PRETTY_PRINT); exit;
  } else {
    //exit. redirect the user. etc.
    exit('Invalid URL');
  }

?>
