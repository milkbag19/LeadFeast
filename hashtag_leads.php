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
if(isset($_POST['remove'])){
    $stmt = $conn->prepare("DELETE FROM `user_hashtags` WHERE `hashtag_id` = ? AND `user_id` = ?");
    $stmt->bind_param("si", $_POST['remove'], $_SESSION['id']);
    if($stmt->execute()){
        echo "<script>window.location.href='hashtag_options.php';</script>";
    }
}
?>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?php
                        if($_SESSION['pay_level'] !== '0') {
                            echo '<h3 class="text-white m-t-10 m-b-0" style="text-align: center; width:80vw;position: center;height:3vh;">#';
                            $stmt = $conn->prepare("SELECT * FROM hashtags WHERE id = ?");
                            $stmt->bind_param("s", $_GET['select_hashtag']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $user_hashtag_data = $result->fetch_assoc();
                            echo $user_hashtag_data['name'];
                            echo '</h3>
                        <form method="post">
                            <button  class = "btn btn-info" style="padding:  8px 8px; border:  0px; border-radius:  3px;width:150px; font-size: 0.75rem;font-stretch: expanded;" name="remove" value="' . $_GET['select_hashtag'] . '" name="remove">Remove Hashtag</button>
                        </form>';


                            if (!isset($_GET['select_hashtag'])) {
                                echo '<div class="row m-t-40" style="text-align:center"><div class="col-md-12" style="text-align:center"><h1>No hashtag selected.</h1>';

                            } else {

                                echo '<h6 class="card-subtitle">Contact us @ digitera.agency for help with any issue!</h6>';
                                echo '<h6 class="card-subtitle">NOTE : will only display the latest 1000 tweets to reduce load times.</h6>';
                                echo '<div class="tab-content">';
                                echo '<div class="tab-pane active" id="campaigns-tab" role="tabpanel">';
                                echo '<div class="table-responsive m-t-40">';
                                echo '<table id="campaigns" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">';
                                getTweets($_GET['select_hashtag'], $_SESSION['pay_level'], $conn);
                                echo "</tbody>" . "</table>" . "</div>";
                                echo '</div>';
                                echo '</div>';
                                echo '<br>';


                            }
                        } else {
                            echo '<h4 class="card-subtitle">Trial data is not live, and may be out dated.</h4>';
                            echo '<div class="tab-content">';

                            get_trial_tweets($conn);


                            echo '</div>';
                        }
                        ?>

                    </div>
                </div>
        <?php include('footer.php'); ?>
