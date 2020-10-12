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
include_once("includes/trial_twitter_functions.php");
check_session();
$id = $_SESSION['id'];
include_once("./header.php");
require_once './includes/htmlpurifier/library/HTMLPurifier.auto.php';
$purifier = new HTMLPurifier();

?>

<!DOCTYPE html>
<html lang="en">
<style>
    .sorting_1{
        background: transparent !important;
    }
</style>
<?php

if(isset($_GET['follower'])){
    $user = $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_GET['follower'])));
    if(substr($user,0, 1)=="@"){
        $user = substr($user,1, strlen($user));
    }
    $stmt = $conn->prepare("SELECT * FROM `twitter_follower_user` WHERE `twuser_name` = ? AND `user_id` = ?");
    $stmt->bind_param("si", $user, $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO `twitter_follower_user` (`user_id`, `twuser_name`) VALUES (?,?)");
        $stmt->bind_param("is", $_SESSION['id'], $user);
        if(!$stmt->execute()){
            echo "<script>alert('There was an error adding your user!');</script>";
        }

    }
}
if(isset($_POST['remove'])){
    $stmt = $conn->prepare("DELETE FROM `twitter_follower_user` WHERE `twuser_name` = ? AND `user_id` = ?");
    $stmt->bind_param("si", $_POST['remove'], $_SESSION['id']);
    if($stmt->execute()){
        echo "<script>window.location.href='follower_sniping_options.php';</script>";
    }
}
if(isset($_POST['list_all'])){
    echo"<script>window.location.href='./follower_sniping.php?select_user=".$_GET['select_user']."';</script>";
}
?>

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
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3 class="text-white m-t-10 m-b-0" style="text-align: center; width:80vw;position: center;height:3vh;">
                    <a style="color: whitesmoke;" target="_blank" href="https://twitter.com/<?php echo $_GET['select_user']; ?>">
                        @<?php echo $_GET['select_user']; ?>
                    </a>
                </h3>
                <h4 style="font-weight: bold;"><?php if($_SESSION['pay_level']!=='0'){echo'Lists all followers!</h4>
                        <form method="post">
                            <button  class = "btn btn-danger" style="padding:  8px 8px; border:  0px; border-radius:  3px;width:150px; font-size: 0.75rem;font-stretch: expanded;" name="remove" value="' . $_GET['select_user']  . '" name="remove">Remove Profile</button>
                            <button  style="padding:  8px 8px; border:  0px; border-radius:  3px;width:150px; font-size: 0.75rem;font-stretch: expanded;" name="list_all" class="btn btn-warning waves-effect waves-light">Recent Followers</button>
                        </form> ';} ?>
                    <br><br>
                    <?php
                    if($_SESSION['pay_level'] !== '0') {
                        if (!isset($_GET['select_user'])) {
                            echo '<div class="row m-t-40" style="text-align:center"><div class="col-md-12" style="text-align:center"><h1>No profile selected.</h1>';
                        } else {
                            $stmt = $conn->prepare("SELECT * FROM twitter_follower_list WHERE tracked_user_id = ?");
                            $stmt->bind_param("i", $_GET['select_user']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $user_hashtag_data = $result->fetch_assoc();

                            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                            $stmt->bind_param("i", $_SESSION['id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $user_data = $result->fetch_assoc();
                            echo '<h6 class="card-subtitle">Contact us @ digitera.agency for help with any issue!</h6>';
                            echo '<div class="tab-content">';


                            get_all_followers($_GET['select_user'], $_SESSION['pay_level'], $_SESSION['id'], $user_data['access_token'], $user_data['access_secret'], $user_data['consumer_key'], $user_data['consumer_secret'], $conn);


                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<h4 class="card-subtitle">Trial data is not live, and may be out dated.</h4>';
                        echo '<div class="tab-content">';

                        get_trial_followers($conn);


                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
            </div>
        </div>
        <?php include('footer.php'); ?>
