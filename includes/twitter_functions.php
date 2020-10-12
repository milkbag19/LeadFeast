<?php
require('twitter-api-php-master/twitter-api-php-master/TwitterAPIExchange.php');

function get_num_followers($twitter_username, $access_token, $access_secret, $consumer_key, $consumer_secret){
    $settings = array(
        'oauth_access_token' => $access_token,
        'oauth_access_token_secret' => $access_secret,
        'consumer_key' => $consumer_key,
        'consumer_secret' => $consumer_secret
    );
    $url = "https://api.twitter.com/1.1/users/lookup.json";
    $requestMethod = "GET";
    $twitter = new TwitterAPIExchange($settings);
    $getfield = "?screen_name=".$twitter_username;
    $string = json_decode($twitter->setGetfield($getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest(), $assoc = TRUE);
    if(isset($string[0]['followers_count'])) {
        return $string[0]['followers_count'];
    }
    return var_dump($string);
}
function get_profile_picture($twitter_username, $access_token, $access_secret, $consumer_key, $consumer_secret){
    $settings = array(
        'oauth_access_token' => $access_token,
        'oauth_access_token_secret' => $access_secret,
        'consumer_key' => $consumer_key,
        'consumer_secret' => $consumer_secret
    );
    $url = "https://api.twitter.com/1.1/users/lookup.json";
    $requestMethod = "GET";
    $twitter = new TwitterAPIExchange($settings);
    $getfield = "?screen_name=".$twitter_username;
    $string = json_decode($twitter->setGetfield($getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest(), $assoc = TRUE);
    if(isset($string[0]['profile_image_url_https'])) {
        return $string[0]['profile_image_url_https'];
    }
    return var_dump($string);
}

function getTweets($hashtag_id, $payment_level, $conn){
    $tweet_count = 0;
    $stmt = $conn->prepare("SELECT * FROM hashtag_tweets WHERE hashtag_id = ? ORDER BY created_at DESC;");
    $stmt->bind_param("i", $hashtag_id);
    $stmt->execute();
    $results = $stmt->get_result();

    $stmt = $conn->prepare("SELECT * FROM hashtags WHERE id = ?");
    $stmt->bind_param("i", $hashtag_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $hashtag_data = $result->fetch_assoc();

    echo "<thead>
                  <tr>
                    <th>Platform</th>
                    <th>Go to Post</th>
                    <th>Hashtag</th>
                    <th>Lead Value</th>
                    <th>Post Date</th>
                    <th>Posters Username</th>
                    <th>Posters Followers</th>
                    <th>Poster Location</th>
                    <th>Post Content</th>
                  </tr>
               </thead>
               <tfoot>
                  <tr>
                     <th>Platform</th>
                     <th>Go to Post</th>
                     <th>Hashtag</th>
                     <th>Lead Value</th>
                     <th>Post Date</th>
                     <th>Posters Username</th>
                     <th>Posters Followers</th>
                     <th>Poster Location</th>
                     <th>Post Content</th>
                  </tr>
                  </tr>
               </tfoot>
               <tbody>";
    $tweets_array = array();
    $loops = 0;
    while(($tweet_data = $results->fetch_assoc())) {
        if($loops < 1000) {
            $tweets_array[] = $tweet_data;
            echo "<tr style='background: transparent;'>" .
                "<td>Twitter</td>" .
                "<td><a target=\"_blank\" href='https://twitter.com/" . $tweet_data['user_id'] . "/status/" . $tweet_data['source'] . "'>View</a></td>" .
                "<td>#" . $hashtag_data['name'] . "</td>" .
                "<td>";
                if($payment_level === '4'){
                    if($tweet_data['checked_status'] === 1){
                        echo $tweet_data['lead_value'] . "/10";
                    } else {
                        echo "Processing";
                    }
                } else {
                    echo "Upgrade Plan" . $payment_level;
                }

                echo "</td><td>" . gmdate("Y-m-d \ TH:i:s ", (int)$tweet_data['created_at']) . "</td>" .
                "<td>" . $tweet_data['username'] . "</td>" .
                "<td>" . $tweet_data['followers'] . "</td>" .
                "<td>" . $tweet_data['location'] . "</td>" .
                "<td>" . $tweet_data['text'] . "</td>".
                "</tr>";
            $loops++;
        } else {
            break;
        }
    }


}
function get_all_followers($tw_user_id, $payment_level, $user_id, $access_token, $access_secret, $consumer_key, $consumer_secret, $conn){

    $stmt = $conn->prepare("SELECT * FROM false_positives WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $results = $stmt->get_result();
    $false_positives = array();
    while($fpd = $results->fetch_assoc()){
        $false_positives[] = $fpd['tw_username'];
    }

    $stmt = $conn->prepare("SELECT * FROM twitter_follower_list WHERE tracked_user_id = ?");
    $stmt->bind_param("s", $tw_user_id);
    $stmt->execute();
    $results = $stmt->get_result();
    if($results->num_rows > 0) {
        echo '<div class="tab-pane active" id="campaigns-tab" role="tabpanel">';
        echo '<div class="table-responsive m-t-40">';
        echo '<table id="campaigns" class="display nowrap table table-hover table-bordered" cellspacing="0" width="100%">';
        echo "<thead>
                  <tr>
                    <th>Platform</th>
                    <th>Go to Profile</th>
                    <th>Username</th>
                    <th>Lead Value</th>
                    <th>Followers</th>
                    <th>Tweets</th>
                    <th>Location</th>
                    <th>Verified</th>
                    <th>Private Profile</th>
                  </tr>
               </thead>
               <tfoot>
                  <tr>
                     <th>Platform</th>
                    <th>Go to Profile</th>
                    <th>Username</th>
                    <th>Lead Value</th>
                    <th>Followers</th>
                    <th>Tweets</th>
                    <th>Location</th>
                    <th>Verified</th>
                    <th>Private Profile</th>
                  </tr>
                  </tr>
               </tfoot>
               <tbody>";


        $follower_list = array();
        $settings = array(
            'oauth_access_token' => $access_token,
            'oauth_access_token_secret' => $access_secret,
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret
        );
        $url = "https://api.twitter.com/1.1/users/lookup.json";
        $requestMethod = "GET";
        $twitter = new TwitterAPIExchange($settings);

        while (($follower_data = $results->fetch_assoc())) {
            if (count($follower_list) < 100 || count($follower_list) == 0) {
                $follower_list[] = $follower_data['tw_follower_id'];
                $follower_data_list[] = [$follower_data['checked_status'], $follower_data['lead_value'], $follower_data['location']];
            } else if (count($follower_list)==100) {
                $getfield = "?user_id=" . implode(",", $follower_list);
                $string = json_decode($twitter->setGetfield($getfield)
                    ->buildOauth($url, $requestMethod)
                    ->performRequest(), $assoc = TRUE);
                foreach ($string as $index => $follower) {
                    if(!in_array($follower['screen_name'], $false_positives)) {
                        echo "<tr style='background: transparent;'>" .
                            "<td><i class=\"fa fa-twitter\" aria-hidden=\"true\"></i> Twitter</td>" .
                            "<td><a target=\"_blank\" href='https://twitter.com/" . $follower['screen_name'] . "'>View</a></td>" .
                            "<td>@" . $follower['screen_name'] . "</td>" .
                            "<td>";
                        if($payment_level === '4'){
                            if($follower_data_list[$index][0] === 1){
                                echo $follower_data_list[$index][1] . "/10";
                            } else {
                                echo "Processing";
                            }
                        } else {
                            echo "Upgrade Plan" . $payment_level;
                        }

                        echo"</td>
                             <td>" . $follower['followers_count'] . "</td>" .
                            "<td>" . $follower['statuses_count'] . "</td>" .
                            "<td>" . $follower['location'] . "</td>" .
                            "<td>" . (($follower['verified']) ? "true" : "false") . "</td>" .
                            "<td>" . (($follower['protected']) ? "true" : "false") . "</td>" .
                            "</tr>";
                    }
                }
                unset($follower_list);
                $follower_list = array();
                $follower_list[] = $follower_data['tw_follower_id'];


            }
        }
        if (count($follower_list) > 0) {
            $getfield = "?user_id=" . implode(",", $follower_list);

            $string = json_decode($twitter->setGetfield($getfield)
                ->buildOauth($url, $requestMethod)
                ->performRequest(), $assoc = TRUE);
            foreach ($string as $index => $follower) {
                if(!in_array($follower['screen_name'], $false_positives)) {
                    echo "<tr style='background: transparent;'>" .
                        "<td><i class=\"fa fa-twitter\" aria-hidden=\"true\"></i> Twitter</td>" .
                        "<td><a target=\"_blank\" href='https://twitter.com/" . $follower['screen_name'] . "'>View</a></td>" .
                        "<td>@" . $follower['screen_name'] . "</td>" .
                        "<td>";

                    if($payment_level === '4'){
                        if($follower_data_list[$index][0] === '1'){
                            echo $follower_data_list[$index][1] . "/10";
                        } else {
                            echo "Processing";
                        }
                    } else {
                        echo "Upgrade Plan";
                    }

                    echo"</td>
                         <td>" . $follower['followers_count'] . "</td>" .
                        "<td>" . $follower['statuses_count'] . "</td>" .
                        "<td>" . $follower['location'] . "</td>" .
                        "<td>" . (($follower['verified']) ? "true" : "false") . "</td>" .
                        "<td>" . (($follower['protected']) ? "true" : "false") . "</td>" .
                        "</tr>";
                }
            }
            unset($follower_list);
        }
        echo "</tbody>" . "</table>" . "</div>";
    } else {
        echo '<div class="row m-t-40" style="text-align:center"><div class="col-md-12" style="text-align:center"><h1>There are no new followers for this user today.</h1>';
    }
}
function getFollowers($tw_user_id, $payment_level, $user_id, $access_token, $access_secret, $consumer_key, $consumer_secret, $conn){

    $stmt = $conn->prepare("SELECT * FROM false_positives WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $results = $stmt->get_result();
    $false_positives = array();
    while($fpd = $results->fetch_assoc()){
        $false_positives[] = $fpd['tw_username'];
    }

    $stmt = $conn->prepare("SELECT * FROM twitter_follower_list WHERE tracked_user_id = ? AND `follow_date` = ?");
    $date = date("Y/m/d");
    $stmt->bind_param("ss", $tw_user_id, $date);
    $stmt->execute();
    $results = $stmt->get_result();
    if($results->num_rows > 0) {
        echo '<div class="tab-pane active" id="campaigns-tab" role="tabpanel">';
        echo '<div class="table-responsive m-t-40">';
        echo '<table id="campaigns" class="display nowrap table table-hover table-bordered" cellspacing="0" width="100%">';
        echo "<thead>
                  <tr>
                    <th>Platform</th>
                    <th>Go to Profile</th>
                    <th>Username</th>
                    <th>Lead Value</th>
                    <th>Followers</th>
                    <th>Tweets</th>
                    <th>Location</th>
                    <th>Verified</th>
                    <th>Private Profile</th>
                  </tr>
               </thead>
               <tfoot>
                  <tr>
                     <th>Platform</th>
                    <th>Go to Profile</th>
                    <th>Username</th>
                    <th>Lead Value</th>
                    <th>Followers</th>
                    <th>Tweets</th>
                    <th>Location</th>
                    <th>Verified</th>
                    <th>Private Profile</th>
                  </tr>
                  </tr>
               </tfoot>
               <tbody>";


        $follower_list = array();
        $settings = array(
            'oauth_access_token' => $access_token,
            'oauth_access_token_secret' => $access_secret,
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret
        );
        $url = "https://api.twitter.com/1.1/users/lookup.json";
        $requestMethod = "GET";
        $twitter = new TwitterAPIExchange($settings);

        while (($follower_data = $results->fetch_assoc())) {
                if (count($follower_list) < 100 || count($follower_list) == 0) {
                    $follower_list[] = $follower_data['tw_follower_id'];
                } else if (count($follower_list)==100) {
                    $getfield = "?user_id=" . implode(",", $follower_list);
                    $string = json_decode($twitter->setGetfield($getfield)
                        ->buildOauth($url, $requestMethod)
                        ->performRequest(), $assoc = TRUE);
                    foreach ($string as $follower) {
                            if(!in_array($follower['screen_name'], $false_positives)) {
                                echo "<tr style='background: transparent;'>" .
                                    "<td><i class=\"fa fa-twitter\" aria-hidden=\"true\"></i> Twitter</td>" .
                                    "<td><a target=\"_blank\" href='https://twitter.com/" . $follower['screen_name'] . "'>View</a></td>" .
                                    "<td>@" . $follower['screen_name'] . "</td>" .
                                    "<td>";
                                $stmt = $conn->prepare("SELECT * FROM twitter_follower_list WHERE tracked_user_id = ?");
                                $stmt->bind_param("s", $follower['id_str']);
                                $stmt->execute();
                                $results_follower = $stmt->get_result();
                                $follower_info = $results_follower->fetch_assoc();

                                if($payment_level === '4'){
                                    if($follower_info['checked_status'] === 1){
                                        echo $follower_info['lead_value'] . "/10";
                                    } else {
                                        echo "Processing";
                                    }
                                } else {
                                    echo "Upgrade Plan" . $payment_level;
                                }

                                echo"</td>
                                     <td>" . $follower['followers_count'] . "</td>" .
                                    "<td>" . $follower['statuses_count'] . "</td>" .
                                    "<td>" . $follower['location'] . "</td>" .
                                    "<td>" . (($follower['verified']) ? "true" : "false") . "</td>" .
                                    "<td>" . (($follower['protected']) ? "true" : "false") . "</td>" .
                                    "</tr>";
                            }
                    }
                    unset($follower_list);
                    $follower_list = array();
                    $follower_list[] = $follower_data['tw_follower_id'];
                }
        }
        if (count($follower_list) > 0) {
            $getfield = "?user_id=" . implode(",", $follower_list);

            $string = json_decode($twitter->setGetfield($getfield)
                ->buildOauth($url, $requestMethod)
                ->performRequest(), $assoc = TRUE);
            foreach ($string as $follower) {
                if(!in_array($follower['screen_name'], $false_positives)) {
                    echo "<tr style='background: transparent;'>" .
                        "<td><i class=\"fa fa-twitter\" aria-hidden=\"true\"></i> Twitter</td>" .
                        "<td><a target=\"_blank\" href='https://twitter.com/" . $follower['screen_name'] . "'>View</a></td>" .
                        "<td>@" . $follower['screen_name'] . "</td>" .
                        "<td>";
                    $stmt = $conn->prepare("SELECT * FROM twitter_follower_list WHERE tracked_user_id = ?");
                    $stmt->bind_param("s", $follower['id_str']);
                    $stmt->execute();
                    $results_follower = $stmt->get_result();
                    $follower_info = $results_follower->fetch_assoc();

                    if($payment_level === '4'){
                        if($follower_info['checked_status'] === 1){
                            echo $follower_info['lead_value'] . "/10";
                        } else {
                            echo "Processing";
                        }
                    } else {
                        echo "Upgrade Plan" . $payment_level;
                    }

                    echo"</td>
                         <td>" . $follower['followers_count'] . "</td>" .
                        "<td>" . $follower['statuses_count'] . "</td>" .
                        "<td>" . $follower['location'] . "</td>" .
                        "<td>" . (($follower['verified']) ? "true" : "false") . "</td>" .
                        "<td>" . (($follower['protected']) ? "true" : "false") . "</td>" .
                        "</tr>";
                }
            }
            unset($follower_list);
        }
        echo "</tbody>" . "</table>" . "</div>";
    } else {
        echo '<div class="row m-t-40" style="text-align:center"><div class="col-md-12" style="text-align:center"><h1>There are no new followers for this user today.</h1>';
    }
}

//echo get_num_followers('jabrils', '1229628837329285121-i9M9a4olZVaBGEXm6rnbQZfrnLXNZY', 'HSeUMgPHV5sx1AxRAflcko52ZVy2augmGhnoSymSAdjnS', 'zVK4ZQPWWmZH8mkR7Mnm4aH2h', 'bH3grKFMYVLvBVnRmSB5oCapwtLPmtFUkylfIRm4fAq9Gjbacp');