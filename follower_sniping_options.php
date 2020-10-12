<?php

$page_name = "Home";
$page_desc = "";
$page_author = "";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once("includes/config.php");
include_once("includes/session.inc.php");
include_once("includes/twitter_functions.php");
check_session();
$id = $_SESSION['id'];
include_once("./header.php");
require_once './includes/htmlpurifier/library/HTMLPurifier.auto.php';
$purifier = new HTMLPurifier();
try{
?>

<!DOCTYPE html>
<html lang="en">
<?php

if(isset($_POST['follower'])){

    $payment_level = get_value_from_table($conn, "users", "id", "payment_level", $_SESSION['id']);

    $allowed_followers = get_value_from_table($conn, "payment_levels", "payment_level", "profile_limit", $payment_level);

    $num_profiles = occurences_custom($conn, "twitter_follower_user", "user_id", $_SESSION['id']);

    if($num_profiles < $allowed_followers) {
        $user = $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_POST['follower'])));
        if (substr($user, 0, 1) == "@") {
            $user = substr($user, 1, strlen($user));
        }
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE `id` = ?");
        $stmt->bind_param("i",$_SESSION['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        if(get_num_followers($user, $user_data['access_token'], $user_data['access_secret'], $user_data['consumer_key'], $user_data['consumer_secret']) <= 10000){
            $stmt = $conn->prepare("SELECT * FROM `twitter_follower_user` WHERE `twuser_name` = ? AND `user_id` = ?");
            $stmt->bind_param("si", $user, $_SESSION['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt = $conn->prepare("INSERT INTO `twitter_follower_user` (`user_id`, `twuser_name`) VALUES (?,?)");
                $stmt->bind_param("is", $_SESSION['id'], $user);
                if (!$stmt->execute()) {
                    echo "<script>alert('There was an error adding your user!');</script>";
                }
            }
        }
    }
}?>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script>
    function addHashtag()
    {
        document.getElementById("PopupParent").style.display="block";
        document.getElementsByClassName("wdth")[0].style.backgroundColor="transparent";
        document.getElementById("contentReceived").innerHTML="<div class='card' style='margin-top:150px;' align='center'><img src='img/loader.gif' width='150px'></div>";
        var xmlhttp;
        if(window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange=function()
        {
            if(xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                // alert(xmlhttp.responseText);
                document.getElementById('contentReceived').innerHTML=xmlhttp.responseText;
                document.getElementById('contentReceived').className = "card";
                document.getElementById('contentReceived').style.padding = "1vw";

            }
        }
        xmlhttp.open("GET","./ajax.php?action=addProfile");
        xmlhttp.send();
    }
</script>


        <div class="row page-titles" style="background: transparent;margin-left:1vw;">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Tracked Profiles <?php

                    $payment_level = get_value_from_table($conn, "users", "id", "payment_level", $_SESSION['id']);
                    $allowed_followers = get_value_from_table($conn, "payment_levels", "payment_level", "profile_limit", $payment_level);
                    $num_profiles = occurences_custom($conn, "twitter_follower_user", "user_id", $_SESSION['id']);

                    echo' : '.$num_profiles.'/'.$allowed_followers;?>
                </h3>

            </div>

        </div>
            <?php
            if($_SESSION['pay_level'] !== "0"){
                $stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt_user->bind_param("i", $_SESSION['id']);
                $stmt_user->execute();
                $result_user = $stmt_user->get_result();
                $user_data = $result_user->fetch_assoc();
                if($_SESSION['has_credentials']) {
                    echo '  <div id="PopupParent">
                          <div class="wdth"> 
                              <div id="contentReceived"> 
                                  <a href="#" onclick="addHashtag();">
                                  ';
                    echo '<button class = "btn btn-info" style=" border:  0px; border-radius:  3px;width:150px; font-size: 0.75rem;font-stretch: expanded;margin-left:2vw;">Add new Profile</button>
                                     
                                  </a>
                              </div>
                          </div>
                       </div>
                       <br><br><br>
                       <div class="row match-height">
                       ';
                }
                $stmt = $conn->prepare("SELECT * FROM twitter_follower_user WHERE user_id = ?");
                $stmt->bind_param("i", $_SESSION['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $num_users = $result->num_rows;
                $count=4;
                if($num_users > 0) {
                    while($user_follower_data = $result->fetch_assoc()){

                        $stmt1 = $conn->prepare("SELECT * FROM twitter_follower_list WHERE tracked_user_id = ?");
                        $stmt1->bind_param("s", $user_follower_data['twuser_name']);
                        $stmt1->execute();
                        $result1 = $stmt1->get_result();
                        $color_array = ["bg-danger", "bg-primary", "bg-info","bg-success"];

                        echo'
                                <div class="card border-info text-center bg-transparent" style="border-radius:15%;width:250px;margin:2vw;box-shadow: 1px 1px 1px 1px rgba(0,0,0,0.2);">
                                    <div class="card-content"style="">
                                        <img style="border-radius:100%;" src="'.get_profile_picture($user_follower_data['twuser_name'], $user_data['access_token'], $user_data['access_secret'], $user_data['consumer_key'], $user_data['consumer_secret']).'" alt="element 04" height="75" width="75" class="mt-3 pl-2 img-fluid">
                                        <div class="card-body">
                                            <h4 class="card-title mt-3">@ '.$user_follower_data['twuser_name'].'</h4>
                                            <p class="card-text">'.get_num_followers($user_follower_data['twuser_name'], $user_data['access_token'], $user_data['access_secret'], $user_data['consumer_key'], $user_data['consumer_secret']).' Followers</p>
                                            <a class="btn btn-info waves-effect waves-light" href="follower_sniping.php?select_user='.$user_follower_data['twuser_name'].'">View Leads</a>
                                        </div>
                                    </div>
                                </div>';
                        $count++;

                    }

                }
                echo '</div>';
            } else {
                $theknot = 'theknot';
                $stmt1 = $conn->prepare("SELECT * FROM twitter_follower_list WHERE tracked_user_id = ?");
                $stmt1->bind_param("s", $theknot);
                $stmt1->execute();
                $result1 = $stmt1->get_result();

                echo "<div class=\"row match-height\">";
                echo'
                                <div class="card border-info text-center bg-transparent" style="margin:2vw; width:15vw;box-shadow: 1px 1px 1px 1px rgba(0,0,0,0.2);">
                                    <div class="card-content"style="width:15vw;">
                                        <img src="'.get_profile_picture('theknot', '1286552196226613249-0uGT8da6q7rPLzJqS9nDOvr3KwZuDl', 'WHBrhHNPP898WbqSG58JIdCguMT9tSmJ2BgdIyACyq6d4', 'w44pvLs4nL78a6AcP8HWlEc5y', '7tytykEsa8C6LbMhg8jQkRXCFmGR1v3UmOe4dq7EW9T0XkC3Ue').'" alt="element 04" width="75" class="mt-3 pl-2 img-fluid">
                                        <div class="card-body">
                                            <h4 class="card-title mt-3">@ theknot</h4>
                                            <p class="card-text">'.get_num_followers('theknot', '1286552196226613249-0uGT8da6q7rPLzJqS9nDOvr3KwZuDl', 'WHBrhHNPP898WbqSG58JIdCguMT9tSmJ2BgdIyACyq6d4', 'w44pvLs4nL78a6AcP8HWlEc5y', '7tytykEsa8C6LbMhg8jQkRXCFmGR1v3UmOe4dq7EW9T0XkC3Ue').' Followers</p>
                                            <a class="btn btn-info waves-effect waves-light" href="follower_sniping.php?select_user=theknot">View Leads</a>
                                        </div>
                                    </div>
                                </div>';
            }
            ?>
        <?php include('footer.php'); } catch(Exception $e){ echo"window.location.href='error.php'"; }?>
