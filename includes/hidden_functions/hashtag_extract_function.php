<?php
include_once('../config.php');
require('../twitter-api-php-master/twitter-api-php-master/TwitterAPIExchange.php');

/*
 * getTweetsFromHashtag is the function the manually gathers the tweets from a specified hashtag.
 *
 */
function getTweetsFromHashtag($hashtag, $access_token, $access_secret, $consumer_key, $consumer_secret, $conn)
{
    $platform = "twitter";
    //-------------------------------------------------------------//
    // The $hashtag var is simply the id for the hashtag, and therefore we need to make a query to get the name of the specified hashtag
    //-------------------------------------------------------------//
    $stmt = $conn->prepare("SELECT * FROM hashtags WHERE id = ?");
    $stmt->bind_param("i", $hashtag);
    $stmt->execute();
    $result = $stmt->get_result();
    $hashtag_data = $result->fetch_assoc();
    //-------------------------------------------------------------//



    //-------------------------------------------------------------//
    // Specifying the settings for the twitter API request
    //-------------------------------------------------------------//
    $settings = array(
        'oauth_access_token' => $access_token,
        'oauth_access_token_secret' => $access_secret,
        'consumer_key' => $consumer_key,
        'consumer_secret' => $consumer_secret
    );
    $url = "https://api.twitter.com/1.1/search/tweets.json";
    $requestMethod = "GET";
    $getfield = "q=%23".$hashtag_data['name']."&include_entities=true&result_type=recent&count=100";
    $twitter = new TwitterAPIExchange($settings);
    //-------------------------------------------------------------//



    //-------------------------------------------------------------//
    // Time to make the request
    //-------------------------------------------------------------//
    $string = json_decode($twitter->setGetfield("?".$getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest(), $assoc = TRUE);
    //-------------------------------------------------------------//



    //-------------------------------------------------------------//
    // This loop will go through the first set of tweets from the API response and if a tweet meets the requirements, then its saved to the database
    //-------------------------------------------------------------//
    foreach ($string['statuses'] as $items) {
        $time = strtotime($items['created_at']);
        $interval = time();
        $diff= ($interval-$time)/60;
        $word = "RT @";
        if($diff<WINDOW){
            if(strpos($items['text'], $word) === false) {
                $timestamp = strtotime($items['created_at']);
                echo " ".$timestamp." ";

                $stmts = $conn->prepare("INSERT INTO `hashtag_tweets`(`hashtag_id`, `created_at`, `username`, `followers`, `location`, `text`, `source`, `user_id`, `platform`) VALUES (?,?,?,?,?,?,?,?,?)");
                $stmts->bind_param("issssssss", $hashtag, $timestamp, $items['user']['screen_name'], $items['user']['followers_count'], $items['user']['location'], $items['text'], $items['id'], $items['user']['id'], $platform);
                if($stmts->execute()){
                }
            }
        }else{
            $done = true;
            break;
        }

    }
    //-------------------------------------------------------------//



    //-------------------------------------------------------------//
    // getting the parameters for the next page of tweet results
    $query = explode("&",$string['search_metadata']['next_results']);
    //-------------------------------------------------------------//



    //-------------------------------------------------------------//
    // This will loop through the rest of the results, going to the next page of tweets if the response meets the API limit
    //-------------------------------------------------------------//
    while(isset($string['search_metadata']['next_results']) && !$done) {
        $string = json_decode($twitter->setGetfield($query[0] ."&". $getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest(), $assoc = TRUE);
        foreach ($string['statuses'] as $items) {
            $time = strtotime($items['created_at']);
            $interval = time();
            $diff= ($interval-$time)/60;
            $word = "RT @";
            if($diff<WINDOW){
                if(strpos($items['text'], $word) === false) {
                    $timestamp = strtotime($items['created_at']);
                    $stmts = $conn->prepare("INSERT INTO `hashtag_tweets`(`hashtag_id`, `created_at`, `username`, `followers`, `location`, `text`, `source`, `user_id`, `platform`) VALUES (?,?,?,?,?,?,?,?,?)");
                    $stmts->bind_param("issssssss", $hashtag, $timestamp, $items['user']['screen_name'], $items['user']['followers_count'], $items['user']['location'], $items['text'], $items['id'], $items['user']['id'], $platform);
                    $stmts->execute();
                }
            }else{
                $done = true; // if the tweets gathered are past the 15 minute limit, the loop will break
                break;
            }
        }
        if(isset($string['search_metadata']['next_results'])) {
            $query = explode("&", $string['search_metadata']['next_results']);
        }
    }
    //-------------------------------------------------------------//
} // FUNCTION END



//-------------------------------------------------------------//
// Sending a query to get a list of all the hashtags with our users id
//-------------------------------------------------------------//
$stmt = $conn->prepare("SELECT * FROM user_hashtags WHERE user_id = ?");
$stmt->bind_param("i", $_POST['user_id']);
$stmt->execute();
$result = $stmt->get_result();
//-------------------------------------------------------------//



//-------------------------------------------------------------//
// Now we must get the data from our users account so we can send their API credentials to the function
$stmts = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmts->bind_param("i", $_POST['user_id']);
$stmts->execute();
$results = $stmts->get_result();
$user_data = $results->fetch_assoc();
//-------------------------------------------------------------//



//-------------------------------------------------------------//
// Loop through all the users hashtags and execute the function for each one
//-------------------------------------------------------------//
while ($hashtag_user_data = $result->fetch_assoc()) {
    if (isset($user_data['access_token'], $user_data['access_secret'], $user_data['consumer_key'], $user_data['consumer_secret'])) {
        getTweetsFromHashtag($hashtag_user_data['hashtag_id'], $user_data['access_token'], $user_data['access_secret'], $user_data['consumer_key'], $user_data['consumer_secret'], $conn);
        echo "user : ".$user_data['id']." "; // NOTE : The user id is echoed so that I could see if each user is actually being called or not.
    }
}
//-------------------------------------------------------------//