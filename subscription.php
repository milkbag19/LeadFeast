<?php
$page_name = "Select a plan";
$page_desc = "";
$page_author = "Josh Wells";

require("./includes/config.php");
require("./includes/session.inc.php");

check_session();

require("./header.php");



?>


<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor"><?php echo $page_name; ?></h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active"><?php echo $page_name; ?></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <div class="priceing-table w3l" >
                        			<div class="wrap">
                        			<form method="post">
                        				<div class="priceing-table-main">

                        						<div class="price-grid">
                        							<div class="price-block agile">
                        								<div class="price-gd-top pric-clr1">
                        									<h4>Start Up</h4>
                        									<h3>$25 per month</h3>
                        								</div>
                        								<div class="price-gd-bottom">
                        									<div class="price-list">
                        										<ul>
                        											<li>99 Influencer Capacity</li>
                        											<li>Base Price</li>

                        										</ul>
                        									</div>
                        								</div>
                        								<div class="price-selet pric-sclr1">
                        									<button class="popup-with-zoom-anim" name="pay" value="25" style="border:none;">Subscribe</button>
                        								</div>
                        							</div>
                        						</div>
                        						<div class="price-grid">
                        							<div class="price-block agile">
                        								<div class="price-gd-top pric-clr2">
                        									<h4>Established Business</h4>
                        									<h3>$65 per month</h3>
                        								</div>
                        								<div class="price-gd-bottom">
                        									<div class="price-list">
                        										<ul>
                        											<li>299 Influencer Capacity</li>
                        											<li>Save!</li>
                        										</ul>
                        									</div>
                        								</div>
                        								<div class="price-selet pric-sclr2">
                        									<button class="popup-with-zoom-anim" name="pay" value="65" style="border:none;">Subscribe</button>
                        								</div>
                        							</div>
                        						</div>
                        						<div class="price-grid wthree">
                        							<div class="price-block agile">
                        								<div class="price-gd-top pric-clr3">
                        									<h4>Enterprise</h4>
                        									<h3>$110 per month</h3>
                        								</div>
                        								<div class="price-gd-bottom">
                        									<div class="price-list">
                        										<ul>
                        											<li>599 Influencer Capacity</li>
                        											<li>Save even more!</li>
                        										</ul>
                        									</div>
                        								</div>
                        								<div class="price-selet pric-sclr3">
                        									<button class="popup-with-zoom-anim" name="pay" value="110" style="border:none;">Subscribe</button>
                        								</div>
                        							</div>
                        						</div>
                        						<div class="clear"> </div>
                        					<?php
                        						if(isset($_POST['pay'])){
													$_SESSION['value'] = $_POST['pay'];
                        							echo"<script>window.location.href='./subscriptionPay.php';</script>";
                        						}
                        					?>
                        				</div>
                        				</form>
                        			</div>

                    </div>
                </div>
            </div>
        </div>
        </div>
<?php require("./footer.php"); ?>
