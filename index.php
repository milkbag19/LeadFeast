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

?>

<!DOCTYPE html>
<html lang="en">
<head>


    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/charts/apexcharts.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/components.css">

</head>


<div class="sidenav-overlay" style="touch-action: pan-y; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></div>
<div class="drag-target" style="touch-action: pan-y; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></div>




<?php $hashtags = occurences_custom($conn, 'user_hashtags', 'user_id', $_SESSION['id']); $limit1 = get_value_from_table($conn, 'payment_levels', 'payment_level','hashtag_limit', $_SESSION['pay_level']);
      $profiles = occurences_custom($conn, 'twitter_follower_user', 'user_id', $_SESSION['id']); $limit2 = get_value_from_table($conn, 'payment_levels', 'payment_level','profile_limit', $_SESSION['pay_level']);?>
<div class="row match-height">
    <div class="col-md-4 col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Usage Limits</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-25">
                        <div class="browser-info">
                            <p class="mb-25">Tracked Hashtags</p>
                            <h4><?php if($limit1>0 && $hashtags>0){echo round((((float)$hashtags / (float)$limit1)*100));} else{echo"0";}?>% Used</h4>
                        </div>
                        <div class="stastics-info text-right">
                            <span><?php echo $hashtags." / ".$limit1; ?> </span>
                            <span class="text-muted d-block"><?php echo date("H:m"); ?></span>
                        </div>
                    </div>
                    <div class="progress progress-bar-primary mb-2">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo ((floatval($hashtags)/floatval($limit1))*100); ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between mb-25">
                        <div class="browser-info">
                            <p class="mb-25">Tracked Profiles</p>
                            <h4><?php if($limit2>0 && $profiles>0){echo round((((float)$profiles / (float)$limit2)*100));} else{echo"0";} ?>% Used</h4>
                        </div>
                        <div class="stastics-info text-right">
                            <span><?php echo $profiles." / ".$limit2; ?></span>
                            <span class="text-muted d-block"><?php echo date("H:m"); ?></span>
                        </div>
                    </div>
                    <div class="progress progress-bar-primary mb-2">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo ((floatval($profiles)/floatval($limit2))*100); ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card" style="height: 355.188px;">
            <div class="card-header">
                <h4 class="card-title"><i class="feather icon-hash"></i>Hashtag Leads</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <p>Click the button below to open your hashtag leads dashboard!</p><br>
                    <form method="post">
                        <input type="submit" class="btn btn-outline-danger block btn-lg waves-effect waves-light" name="hashtag_button" value="Open" id="onshowbtn" data-target="#onshow">
                        <?php if(isset($_POST['hashtag_button'])){
                            echo "<script>window.location.href='hashtag_options.php';</script>";
                        } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card" style="height: 355.188px;">
            <div class="card-header">
                <h4 class="card-title"><i class="feather icon-users"></i>Follower Leads</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <p>Click the button below to open your follower leads dashboard!</p><br>
                    <!-- Button trigger modal -->
                    <form method="post">
                    <input type="submit" class="btn btn-outline-danger block btn-lg waves-effect waves-light" name="follower_button" value="Open" id="onshowbtn" data-target="#onshow">
                    <?php if(isset($_POST['follower_button'])){
                        echo "<script>window.location.href='follower_sniping_options.php';</script>";
                    } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
		<?php include('footer.php'); ?>
