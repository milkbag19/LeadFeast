<?php
include_once('../config.php');
require('../twitter-api-php-master/twitter-api-php-master/TwitterAPIExchange.php');

define('DB_SERVER_NAME', "");
define('DB_USERNAME', "");
define('DB_PASSWORD', "");
define('DB_NAME', "");
$conn = mysqli_connect(DB_SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME) or die(mysqli_connect_error());
$stmts = $conn->prepare("SELECT * FROM users");
$stmts->execute();
$results = $stmts->get_result();
$start = time();
$mh = curl_multi_init();
while ($user_data = $results->fetch_assoc()) {
    echo "bruh";
    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, "https://www.app.leadfeast.com/includes/private_functions/hashtag_extract_function.php");
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "user_id=".$user_data['id']);
    curl_multi_add_handle($mh, $curl);
}
$stillRunning = false;
do {
    curl_multi_exec($mh, $stillRunning);
} while ($stillRunning);
curl_multi_close($mh);
$end = time();
echo ($end - $start). "seconds";



