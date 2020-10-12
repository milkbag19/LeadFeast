<?php
include_once("includes/session.inc.php");
check_session();
include('./includes/config.php');
require('./includes/twitter-api-php-master/twitter-api-php-master/TwitterAPIExchange.php');
if (isset($_POST['submit'])) {
    $stmts = $conn->prepare("UPDATE `users` SET `access_token` = ?, `access_secret` = ?, `consumer_key` = ?, `consumer_secret` = ? WHERE `id` = ?");
    $stmts->bind_param("ssssi", $_POST['access_token'], $_POST['access_secret'], $_POST['consumer_key'], $_POST['consumer_secret'], $_SESSION['id']);
    $results = $stmts->get_result();
    if (!$stmts->execute()) {
        $saved=false;
    } else {
        $saved=true;
        $_SESSION['has_credentials'] = true;

    }
    $settings = array(
        'oauth_access_token' => $_POST['access_token'],
        'oauth_access_token_secret' => $_POST['access_secret'],
        'consumer_key' => $_POST['consumer_key'],
        'consumer_secret' => $_POST['consumer_secret']
    );
    $url = "https://api.twitter.com/1.1/users/lookup.json";
    $requestMethod = "GET";
    $twitter = new TwitterAPIExchange($settings);
    $getfield = "?screen_name=Josh65485071";
    $string = json_decode($twitter->setGetfield($getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest(), $assoc = TRUE);
    if(isset($string['errors'][0]["message"])){
        $valid = "(API keys are not valid)";
    }else {
        $valid = "";
    }
}
include('./header.php');

?>
    <section id="multiple-column-form">
        <?php
        if(isset($saved)) {
            if (!$saved || $valid !== "") {
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Settings were not able to be saved due to an error! '.$valid.'
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else   {
                echo '<div class="alert alert-success" role="alert">
                        Settings successfully saved!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                     </div>';
            }
        }
        ?>

        <div class="row match-height">

            <div class="col-12">

                <div class="card">

                    <div class="card-header">
                        <h4 class="card-title">Settings</h4>
                    </div><div class="card-content">
                        <div class="card-body">
                            <form class="form" method="post">
                                <br>
                                <h5 class="card-title">Twitter Config</h5>
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-label-group">
                                                <input type="text" id="first-name-column" class="form-control" placeholder="Access Token" name="access_token">
                                                <label for="first-name-column">Access Token</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-label-group">
                                                <input type="text" id="last-name-column" class="form-control" placeholder="Access Secret" name="access_secret">
                                                <label for="last-name-column">Access Secret</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-label-group">
                                                <input type="text" id="city-column" class="form-control" placeholder="Consumer/API Key" name="consumer_key">
                                                <label for="city-column">Consumer/API Key</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-label-group">
                                                <input type="text" id="country-floating" class="form-control" name="consumer_secret" placeholder="Consumer/API Secret">
                                                <label for="country-floating">Consumer/API Secret</label>
                                            </div>
                                        </div>
                                        <div class="form-group col-12">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-light" name="submit">Save</button>
                                        </div>

                                    </div>
                                </div>
                            </form>
                            <div style="text-align: center;">
                                <h4 style="color:indianred;">All users on leadFeast.com MUST have their own Twitter API App created and configured properly. Please look at the video below for instructions on how to do so, or email us at jordan@leadfeast.com for help.</h4>
                                <br>
                                <iframe width="720" height="324" src="https://www.youtube.com/embed/ezhHkTXbyQw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
include('./footer.php');
