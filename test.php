<?php
require('./includes/twitter-api-php-master/twitter-api-php-master/TwitterAPIExchange.php');
require('./includes/config.php');
function getTweetsFromHashtag($hashtag)
{
    $settings = array(
        'oauth_access_token' => "1229628837329285121-i9M9a4olZVaBGEXm6rnbQZfrnLXNZY",
        'oauth_access_token_secret' => "HSeUMgPHV5sx1AxRAflcko52ZVy2augmGhnoSymSAdjnS",
        'consumer_key' => "zVK4ZQPWWmZH8mkR7Mnm4aH2h",
        'consumer_secret' => "bH3grKFMYVLvBVnRmSB5oCapwtLPmtFUkylfIRm4fAq9Gjbacp"
    );
    $tweetsCount = 0;
    $url = "https://api.twitter.com/1.1/search/tweets.json";
    $requestMethod = "GET";
    $getfield = "q=%23".$hashtag."&include_entities=true&result_type=recent&count=100";
    $twitter = new TwitterAPIExchange($settings);
    $string = json_decode($twitter->setGetfield("?".$getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest(), $assoc = TRUE);
    $done = false;
    $now=new DateTime(date('D M j H:i:s O Y'));
    foreach ($string['statuses'] as $items) {
        $time=new DateTime($items['created_at']);
        $interval = $time->diff($now);
        $days=$interval->format('%a');
        $hours=$interval->h;
        $mins=$interval->i;
        $secs=$interval->s;
        $diff=$days*24 + $hours + $mins/60 + $secs/3600;
        if($diff<WINDOW){
            echo "Creation Date : " . $items['created_at'] . "<br>";
            echo "Tweet Id" . $items['id'] . "<br>";
            echo "Tweet Content : " . $items['text'] . "<br>";
            echo "Hashtags : <br>";
            foreach ($items['entities']['hashtags'] as $hashtags) {
                echo "#" . $hashtags['text'] . "<br>";
            }
            echo "Username : " . $items['user']['screen_name'] . "<br>";
            echo "_______________________________________________________________________________________________ <br>";
            $tweetsCount+=1;
        }else{
            $done = true;
            break;
        }

    }
    $query = explode("&",$string['search_metadata']['next_results']);


    while(isset($string['search_metadata']['next_results']) && !$done) {
        $string = json_decode($twitter->setGetfield($query[0] ."&". $getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest(), $assoc = TRUE);


        foreach ($string['statuses'] as $items) {
            $time=new DateTime($items['created_at']);
            $interval = $time->diff($now);
            $days=$interval->format('%a');
            $hours=$interval->h;
            $mins=$interval->i;
            $secs=$interval->s;
            $diff=$days*24 + $hours*60 + $mins + $secs/60;
            if($diff<WINDOW){
                echo "Creation Date : " . $items['created_at'] . "<br>";
                echo "Tweet Id" . $items['id'] . "<br>";
                echo "Tweet Content : " . $items['text'] . "<br>";
                echo "Hashtags : <br>";
                foreach ($items['entities']['hashtags'] as $hashtags) {
                    echo "#" . $hashtags['text'] . "<br>";
                }
                echo "Username : " . $items['user']['screen_name'] . "<br>";
                echo "_______________________________________________________________________________________________ <br>";
                $tweetsCount+=1;
            }else{
                $done = true;
                break;
            }
            /*echo "Creation Date : " . $items['created_at'] . "<br>";
            echo "Tweet Id" . $items['id'] . "<br>";
            echo "Tweet Content : " . $items['text'] . "<br>";
            echo "Hashtags : <br>";
            foreach ($items['entities']['hashtags'] as $hashtags) {
                echo "#" . $hashtags['text'] . "<br>";
            }
            echo "Username : " . $items['user']['screen_name'] . "<br>";
            echo "_______________________________________________________________________________________________ <br>";*/
        }
        if(isset($string['search_metadata']['next_results'])) {
            $query = explode("&", $string['search_metadata']['next_results']);
        }
    }
    echo $tweetsCount;
}
