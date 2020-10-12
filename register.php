<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("./includes/config.php");
$error_log = include("includes/register.inc.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">


    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <!--Crowdfluence-->
    <title>Sign Up</title>

    <link rel="apple-touch-icon" href="./app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="./app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="./app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/vendors/css/extensions/tether-theme-arrows.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/vendors/css/extensions/tether.min.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/vendors/css/extensions/shepherd-theme-default.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="./app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="./app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/pages/dashboard-analytics.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/pages/card-analytics.css">
    <link rel="stylesheet" type="text/css" href="./app-assets/css/plugins/tour/tour.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <!-- Bootstrap Core CSS -->
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- page css -->
    <link href="css/pages/login-register-lock.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <!--<link href="css/custom.css" rel="stylesheet">-->

    <!-- You can change the theme colors from here -->
    <link href="css/colors/default-dark.css" id="theme" rel="stylesheet">
    <script src="assets/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap tether Core JavaScript -->
    <script src="assets/plugins/bootstrap/js/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src='https://www.google.com/recaptcha/api.js'></script>


<!-- ============================================================== -->
<!-- Preloader - style you can find in spinners.css -->
<!-- ============================================================== -->
<div class="preloader">
    <div class="loader">
        <div class="loader__figure"></div>
        <p class="loader__label">Crowdfluence</p>
    </div>
</div>
<!-- ============================================================== -->
<!-- Main wrapper - style you can find in pages.scss -->
<!-- ============================================================== -->

<body class="vertical-layout vertical-menu-modern dark-layout 1-column  navbar-floating footer-static bg-full-screen-image  blank-page blank-page  pace-done" data-open="click" data-menu="vertical-menu-modern" data-col="1-column" data-layout="dark-layout"><div class="pace  pace-inactive"><div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
        <div class="pace-progress-inner"></div>
    </div>
    <div class="pace-activity"></div></div>
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section class="row flexbox-container">
                <div class="col-xl-8 col-10 d-flex justify-content-center">
                    <div class="card bg-authentication rounded-0 mb-0">
                        <div class="row m-0">
                            <div class="col-lg-6 d-lg-block d-none text-center align-self-center pl-0 pr-3 py-0">
                                <img src="../../../app-assets/images/pages/register.jpg" alt="branding logo">
                            </div>
                            <div class="col-lg-6 col-12 p-0">
                                <div class="card rounded-0 mb-0 p-2">
                                    <p style="color:lawngreen;margin:1vh;"><?php

                                        if($error_log != 1) {
                                            foreach ($error_log as $err) {
                                                echo $err . "<br>";
                                            }
                                        }

                                        ?></p>
                                    <div class="card-header pt-50 pb-1">
                                        <div class="card-title">
                                            <h4 class="mb-0">Create Account</h4>
                                        </div>
                                    </div>
                                    <p class="px-2">Fill the below form to create a new account.</p>
                                    <div class="card-content">
                                        <div class="card-body pt-0">
                                            <form method="post" novalidate>
                                                <div class="form-label-group">
                                                    <input type="text" id="inputName" class="form-control" placeholder="Name" name="name" required>
                                                    <label for="inputName">Name</label>
                                                </div>
                                                <div class="form-label-group">
                                                    <input type="email" id="inputEmail" class="form-control" placeholder="Email" name="email"  data-validation-required-message="Provide a valid email" required>
                                                    <label for="inputEmail">Email</label>
                                                </div>
                                                <div class="form-label-group">
                                                    <input type="password" id="inputPassword" class="form-control" placeholder="Password" name="pwd"  required>
                                                    <label for="inputPassword">Password</label>
                                                </div>
                                                <div class="form-label-group">
                                                    <input type="password" id="inputConfPassword" class="form-control" placeholder="Confirm Password" name="confirm_pwd"  required>
                                                    <label for="inputConfPassword">Confirm Password</label>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <fieldset class="checkbox">
                                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                                <input type="checkbox" checked="">
                                                                <span class="vs-checkbox">
                                                                        <span class="vs-checkbox--check">
                                                                            <i class="vs-icon feather icon-check"></i>
                                                                        </span>
                                                                    </span>
                                                                <span class=""> I accept the terms &amp; conditions.</span>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                                <a href="./login.php" class="btn btn-outline-primary float-left btn-inline mb-50 waves-effect waves-light">Login</a>
                                                <button type="submit" name="submit" class="btn btn-primary float-right btn-inline mb-50 waves-effect waves-light">Register

                                                </button></form></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<!-- END: Content-->


<!-- BEGIN: Vendor JS-->
<script src="./app-assets/vendors/js/vendors.min.js"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="./app-assets/js/core/app-menu.js"></script>
<script src="./app-assets/js/core/app.js"></script>
<script src="./app-assets/js/scripts/components.js"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<!-- END: Page JS-->


<!-- END: Body-->

<!-- END: Body-->
<script type="text/javascript">
    $(function() {
        $(".preloader").fadeOut();
    });
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    });
    // ==============================================================
    // Login and Recover Password
    // ==============================================================
    $('#to-recover').on("click", function() {
        $("#loginform").slideUp();
        $("#recoverform").fadeIn();
    });
</script>
</body>




<!--
<section id="wrapper" class="login-register login-sidebar" style="background-image:url(assets/images/background/login-register.jpg);">
    <div class="login-box card">
        <div class="card-body" style="overflow-y: auto">
            <form class="form-horizontal form-material" id="loginform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method = "post" onsubmit = "<?php /* echo $PwdField->getValue() == $ConfirmPwdField->getValue(); */ ?>"> onsubmit="return validateForm()"
                <a href="javascript:void(0)" class="text-center db"><img src="assets/images/logo-icon.png" alt="Home" /><br/><img src="assets/images/logo-text.png" alt="Home" /></a>
                <h3 class="box-title m-t-15 m-b-0">Register Now</h3><small>Create your account and enjoy</small>
                <?php

                create_field("input", "form-control", "text", "name", "Name", "", '<div class="form-group m-t-20"><div class="col-xs-12">', '</div></div>', $error_log, false, "style='margin-bottom:10px'");
                create_field("input", "form-control", "text", "email", "Email", "", '<div class="form-group"><div class="col-xs-12">', '</div></div>', $error_log, true, "style='margin-bottom:10px'");
                create_field("input", "form-control", "password", "pwd", "Password", "", '<div class="form-group"><div class="col-xs-12">', '</div></div>', $error_log, true, "style='margin-bottom:10px'");
                create_field("input", "form-control", "password", "confirm_pwd", "Confirm Password", "", '<div class="form-group"><div class="col-xs-12">', '</div></div>', $error_log, "style='margin-bottom:10px'");

                ?>
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="checkbox checkbox-primary p-t-0">
                            <input id="checkbox-signup" type="checkbox" name="chkbox" title="Please agree to the terms and conditions" required>
                            <label for="checkbox-signup"> I agree to all <a href="#">Terms</a></label>
                        </div>
                    </div>
                </div>
                <?php

                if (isset($error_log['chkbox'])){
                    echo "<small style='color: #ff0000;'>" . $error_log['chkbox'] . "</small>";
                }
                ?>
                <div class="g-recaptcha" name="g-recaptcha-response" data-sitekey="6LdmX2QUAAAAADLPZqKGa-4K2_S0-BMFQdFNkTdW" required></div>
                <?php

                if (isset($error_log['reCAPTCHA'])){
                    echo "<small style='color: #ff0000;'>" . $error_log['reCAPTCHA'] . "</small>";
                }
                ?>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit" name = "submit">Sign Up</button>
                    </div>
                </div>
                <div class="form-group m-b-0">
                    <div class="col-sm-12 text-center">
                        <p>Already have an account? <a href="login.php" class="text-info m-l-5"><b>Login</b></a></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>-->
<!-- ============================================================== -->
<!-- End Wrapper -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->



</html>
