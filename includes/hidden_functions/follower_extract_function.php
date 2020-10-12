<?php
include_once('../config.php');
require('../twitter-api-php-master/twitter-api-php-master/TwitterAPIExchange.php');



/*
 * getFollowerList is the function the manually gathers the follower data from a specified account.
 *
 */
function getFollowerList($tw_user, $access_token, $access_secret, $consumer_key, $consumer_secret, $conn)
{
    $platform = 'twitter';
    //-------------------------------------------------------------//
    // Below will extract a list of followers that have been previously saved to the database.
    //
    // This is done beforehand, so to avoid recursive queries, and therefore saving time.
    //
    // Time is a limited asset when extracting data since this function is run every 15 minutes, and therefore must be kept under that time limit.
    //-------------------------------------------------------------//
    $stmts1 = $conn->prepare("SELECT * FROM `twitter_follower_list` WHERE`tracked_user_id`=?");
    $stmts1->bind_param("s", $tw_user);
    $stmts1->execute();
    $results = $stmts1->get_result();
    $follower_list = array();
    $new_follower_list = array();
    while($follower_list_data = $results->fetch_assoc()){
        $follower_list[] = $follower_list_data['tw_follower_id'];
    }
    //-------------------------------------------------------------//



    //-------------------------------------------------------------//
    // Below is the setup for the Twitter API.
    //
    // $extracted_followers will be used to store any new followers not detected within our database
    //-------------------------------------------------------------//
    $extracted_followers = array();
    $settings = array(
        'oauth_access_token' => $access_token,
        'oauth_access_token_secret' => $access_secret,
        'consumer_key' => $consumer_key,
        'consumer_secret' => $consumer_secret
    );
    $url = "https://api.twitter.com/1.1/followers/ids.json";
    $requestMethod = "GET";
    $getfield = "screen_name=" . $tw_user . "&count=5000";
    $twitter = new TwitterAPIExchange($settings);
    //-------------------------------------------------------------//



    //-------------------------------------------------------------//
    // $string is used to store the decoded json response of the API request
    //
    // This will be used to analyze the response and gather the data we need.
    //-------------------------------------------------------------//
    $string = json_decode($twitter->setGetfield("?" . $getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest(), $assoc = TRUE);
    //-------------------------------------------------------------//



    //-------------------------------------------------------------//
    // We need to see if there are any errors sent back from the API.
    //
    // If we find there is an error response, then it will be echoed as to allow us to debug.
    //
    // The echoed data can be seen in the cronjob email.
    //
    // To setup your own email for cronjob responses, simply go to CPanel and go to cronjobs, then you will see the email input.
    //-------------------------------------------------------------//
    if(isset($string['errors'][0]["message"])){
        echo $string['errors'][0]["message"]."<br>";
    }
    //-------------------------------------------------------------//



    //-------------------------------------------------------------//
    // We loop through the ids sent back from the API and check if the id has already been saved to the database or not.
    //
    // If it has not been detected, then we save it to the extracted_followers array we discussed before.
    //
    // Every 200 extracted followers, we join the array and slide it into the query.
    //
    // We use a BULK SQL query as to save time by limiting the amount of queries are sent.
    //
    // Queries are relatively quite slow, so limiting the amount we have to execute is a good practice for scalability.
    //-------------------------------------------------------------//
    foreach ($string['ids'] as $items) {
        $new_follower_list[] = $items;
        if(!in_array($items, $follower_list)){
            $extracted_followers[] = "('".$items."', '".$tw_user."', '".date('Y/m/d')."', '".$platform."')";
        }
        if(count($extracted_followers) === 200){
            $sql = "INSERT INTO `twitter_follower_list`(`tw_follower_id`, `tracked_user_id`, `follow_date`, `platform`) VALUES ".implode(',', $extracted_followers);
            $bruh = $conn->prepare($sql);
            if($bruh->execute()){
                $extracted_followers = array();
            }
        }
    }
    //-------------------------------------------------------------//



    //-------------------------------------------------------------//
    // Here, we do the same thing as before, but often there are some ids left over that never reached 200, so we just save them after the loop.
    //-------------------------------------------------------------//
    if(count($extracted_followers) > 0) {
        $sql = "INSERT INTO `twitter_follower_list`(`tw_follower_id`, `tracked_user_id`, `follow_date`, `platform`) VALUES " . implode(',', $extracted_followers);
        $bruh = $conn->prepare($sql);
        if ($bruh->execute()) {
            $extracted_followers = array();
        }

    }
    //-------------------------------------------------------------//



    //-------------------------------------------------------------//
    // If there are more ids in the response than the maximum 5000, then we are able to use pagination to get the next results.
    //
    // The index in the response is 'next_cursor', and we check if there is a next page set.
    //
    // If so, then we send another request, but for the next page and repeat the same process as before.
    //-------------------------------------------------------------//
    while (isset($string['next_cursor']) && $string['next_cursor']!="" && $string['next_cursor']!=null) {
        $string = json_decode($twitter->setGetfield("?cursor=" . $string['next_cursor'] . "&" . $getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest(), $assoc = TRUE);
        if(isset($string['errors'][0]["message"])){
            echo $string['errors'][0]["message"]."<br>";
        }
        foreach ($string['ids'] as $items) {
            $new_follower_list[] = $items;
            if(!in_array($items,$follower_list)){
                $extracted_followers[] = "('".$items."', '".$tw_user."', '".date('Y/m/d')."', '".$platform."')";
            }
            if(count($extracted_followers) === 200){
                $bruh = $conn->prepare("INSERT INTO `twitter_follower_list`(`tw_follower_id`, `tracked_user_id`, `follow_date`, `role`) VALUES ".implode(',', $extracted_followers));
                if($bruh->execute()){
                    $extracted_followers = array();
                }

            }

        }
        if(count($extracted_followers) > 0) {
            $sql = "INSERT INTO `twitter_follower_list`(`tw_follower_id`, `tracked_user_id`, `follow_date`, `role`) VALUES " . implode(',', $extracted_followers);
            $bruh = $conn->prepare($sql);
            if ($bruh->execute()) {
                $extracted_followers = array();
            }

        }
    }
    if(count($extracted_followers) > 0) {
        $sql = "INSERT INTO `twitter_follower_list`(`tw_follower_id`, `tracked_user_id`, `follow_date`, `role`) VALUES " . implode(',', $extracted_followers);
        $bruh = $conn->prepare($sql);
        if ($bruh->execute()) {
            $extracted_followers = array();
        }

    }
    //-------------------------------------------------------------//



    //-------------------------------------------------------------//
    // We compare the list we gathered from our database in the beginning to the one we have been making throughout the function, which will be the update follower list.
    //
    // If we find that some of the ids that we detected as following are no longer found in the users follower list, we mark it as an unfollow.
    //
    // After we get a list of all unfollows, we make a query to delete every unfollow id from our database.
    //-------------------------------------------------------------//
    $unfollows = array();
    foreach($follower_list as $follower_list_data){
        if(!in_array($follower_list_data, $new_follower_list)){
            if($conn->query("DELETE FROM `twitter_follower_list` WHERE `tracked_user_id` = '".$tw_user."' AND `tw_follower_id` = ".$follower_list_data)){
                echo"-1";
            }
        }
    }

    //-------------------------------------------------------------//
} // Function END

//-------------------------------------------------------------//
// Here we are going to make a query to the tracked users, and get the data with the user_id given through cURL.
//-------------------------------------------------------------//
$stmt = $conn->prepare("SELECT * FROM twitter_follower_user WHERE user_id = ?");
$stmt->bind_param("i", $_POST['user_id']);
$stmt->execute();
$result = $stmt->get_result();
//-------------------------------------------------------------//



//-------------------------------------------------------------//
// We also need to query the users table so that we can get the API keys
//-------------------------------------------------------------//
$stmts = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmts->bind_param("i", $_POST['user_id']);
$stmts->execute();
$results = $stmts->get_result();
$user_data = $results->fetch_assoc();
//-------------------------------------------------------------//



//-------------------------------------------------------------//
// We loop through the first query's results, so that we can run the function for each tracked profile this user has.
//-------------------------------------------------------------//
while ($follower_user_data = $result->fetch_assoc()) {
    if (isset($user_data['access_token'], $user_data['access_secret'], $user_data['consumer_key'], $user_data['consumer_secret'])) {
        getFollowerList($follower_user_data['twuser_name'], $user_data['access_token'], $user_data['access_secret'], $user_data['consumer_key'], $user_data['consumer_secret'], $conn);
        echo "user : ".$user_data['id']." "; // NOTE : The user id is echoed so that I could see if each user is actually being called or not.
        sleep(2); // NOTE : we have to wait 2 seconds because the script will call the API too fast, resulting in a rate limit error.  DO NOT REMOVE. (Unless you can find a work around to fix the rate limit issue)
    }
}
//-------------------------------------------------------------//