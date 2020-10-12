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

if(isset($_POST['hashtag'])){

    $payment_level = get_value_from_table($conn, "users", "id", "payment_level", $_SESSION['id']);
    $allowed_hashtags = get_value_from_table($conn, "payment_levels", "payment_level", "hashtag_limit", $payment_level);
    $num_hashtags = occurences_custom($conn, "user_hashtags", "user_id", $_SESSION['id']);

    if($num_hashtags < $allowed_hashtags){
        $hashtag = $purifier->purify(mysqli_real_escape_string($conn, stripslashes($_POST['hashtag'])));
        if(substr($hashtag,0, 1)=="#"){
            $hashtag = substr($hashtag,1, strlen($hashtag));
        }
        $stmt = $conn->prepare("SELECT * FROM `hashtags` WHERE `name` = ?");
        $stmt->bind_param("s", $hashtag);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows === 0){
            $stmt = $conn->prepare("INSERT INTO `hashtags` (`name`) VALUES (?)");
            $stmt->bind_param("s", $hashtag);

            if(!$stmt->execute()){
                var_dump($stmt);
                echo "<script>alert('$hashtag');</script>";
            }else{
                $stmt = $conn->prepare("SELECT * FROM `hashtags` WHERE `name` = ?");
                $stmt->bind_param("s", $hashtag);
                $stmt->execute();
                $result = $stmt->get_result();
                $hashtag_data = $result->fetch_assoc();
                $hashtag_id = $hashtag_data['id'];

                $stmt = $conn->prepare("INSERT INTO `user_hashtags` (`hashtag_id`,`user_id`) VALUES (?,?)");
                $stmt->bind_param("ss", $hashtag_id, $_SESSION['id']);
                if(!$stmt->execute()){
                    echo "<script>alert('2');</script>";
                }
            }
        }else{
            $stmt = $conn->prepare("SELECT * FROM `hashtags` WHERE `name` = ?");
            $stmt->bind_param("s", $hashtag);
            $stmt->execute();
            $result = $stmt->get_result();
            $hashtag_data = $result->fetch_assoc();
            $hashtag_id = $hashtag_data['id'];

            $stmt = $conn->prepare("INSERT INTO `user_hashtags` (`hashtag_id`,`user_id`) VALUES (?,?)");
            $stmt->bind_param("ii", $hashtag_id, $_SESSION['id']);
            if(!$stmt->execute()){
                echo "<script>alert('3');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
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
        xmlhttp.open("GET","./ajax.php?action=addHashtag");
        xmlhttp.send();
    }
</script>



        <div class="row page-titles" style="background: transparent;margin-left:0vw;">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Tracked Hashtags <?php

                    $payment_level = get_value_from_table($conn, "users", "id", "payment_level", $_SESSION['id']);
                    $allowed_hashtags = get_value_from_table($conn, "payment_levels", "payment_level", "hashtag_limit", $payment_level);
                    $num_hashtags = occurences_custom($conn, "user_hashtags", "user_id", $_SESSION['id']);

                    echo' : '.$num_hashtags.'/'.$allowed_hashtags;?></h3>
            </div>
        </div>
        <div>
        <?php
        if($_SESSION['pay_level'] !== '0') {
            if($_SESSION['has_credentials']) {
                echo '  <div id="PopupParent" style="background: transparent;margin-left:1vw;" >
                  <div class="wdth"> 
                     <div id="contentReceived"> 
                        <a href="#" onclick="addHashtag();">
                           <button class = "btn btn-info" style="margin-left:0%;padding:  8px 8px; border:  0px; border-radius:  3px;width:150px; font-size: 0.75rem;font-stretch: expanded;">Add new Hashtag</button>
                        </a>
                     </div>
                  </div>
                 </div>
                 <br><br><br>
                 <div class="row match-height">';
            }
            $stmt = $conn->prepare("SELECT * FROM user_hashtags WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $num_hashtags = $result->num_rows;
            echo "<script>console.log(";
            echo $num_hashtags;
            echo ");</script>";
            $count = 4;
            if ($num_hashtags > 0) {
                while ($user_hashtag_data = $result->fetch_assoc()) {
                    $stmts = $conn->prepare("SELECT * FROM hashtags WHERE id = ?");
                    $stmts->bind_param("i", $user_hashtag_data['hashtag_id']);
                    $stmts->execute();
                    $results = $stmts->get_result();
                    $hashtag_data = $results->fetch_assoc();

                    $stmt1 = $conn->prepare("SELECT * FROM hashtag_tweets WHERE hashtag_id = ?");
                    $stmt1->bind_param("i", $user_hashtag_data['hashtag_id']);
                    $stmt1->execute();
                    $result1 = $stmt1->get_result();
                    $color_array = ["bg-danger", "bg-primary", "bg-info", "bg-success"];

                echo'
                                <div class="card border-info text-center bg-transparent" style="border-radius:15%;width:250px;margin:2vw;box-shadow: 1px 1px 1px 1px rgba(0,0,0,0.2);">
                                    <div class="card-content"style="">
                                        <img style="border-radius:100%;" src="https://us.123rf.com/450wm/fokaspokas/fokaspokas1808/fokaspokas180800027/106818749-hashtag-icon-colorful-logo-concept-with-soft-shadow-on-dark-background-icon-color-of-azure-ocean.jpg?ver=6" alt="element 04" height="75" width="75" class="mt-3 pl-2 img-fluid">
                                        <div class="card-body">
                                            <h4 class="card-title mt-3"># '.$hashtag_data['name'].'</h4>
                                            <p class="card-text">'.$result1->num_rows.' Posts</p>
                                            <a class="btn btn-info waves-effect waves-light" href="window.location.href=\'hashtag_leads.php?select_hashtag=' . $hashtag_data['id'] . '\'">View Leads</a>
                                        </div>
                                    </div>
                                </div>';
                    $count++;
                }

            }
            echo '</div>';
        } else {
            $stmt1 = $conn->prepare("SELECT * FROM trial_hashtag_tweets");
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            echo'
            <div class="card border-info text-center bg-transparent" style="margin:2vw;box-shadow: 1px 1px 1px 1px rgba(0,0,0,0.2);">
                <div class="card-content"style="">
                    <img src="https://www.newstatesman.com/sites/default/files/blogs_2016/03/hashtag.png" alt="element 04" width="75" class="mt-3 pl-2 img-fluid">
                    <div class="card-body">
                        <h4 class="card-title mt-3"># engaged</h4>
                        <p class="card-text">'.$result1->num_rows.' Posts</p>
                        <a class="btn btn-info waves-effect waves-light" href="window.location.href=\'hashtag_leads.php?select_hashtag=engaged\'">View Leads</a>
                    </div>
                </div>
            </div>';

        }





        ?>
        </div>
        <!-- ============================================================== -->
        <!-- Subscribe -->
        <!-- ============================================================== -->


        <!-- ============================================================== -->
        <!-- End Right panel -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- End Page Content -->
        <!-- ============================================================== -->
        <?php include('footer.php'); ?>
