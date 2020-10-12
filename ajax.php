<?php
if(@$_GET['action']=="addHashtag")
{


    ?>
    <div>
        <div style="width: 48%;height: 200px;float: left;">
            <h3 style="font-weight: 300;" align="center">Add Hashtag</h3>

            <br><br>

            <form id="hashtag" action="hashtag_options.php"  enctype="multipart/form-data" method="post">

                <p style="margin-bottom: 30px;">
                    <label  for="first-name-icon">Enter hashtag below</label>
                    <input  id="first-name-icon" class="form-control" name="hashtag" type="text" required>

                </p>
                <p style="width: 100%;" class="right">
                    <input value="Submit"  style="border: 0px;" type="submit">
                </p>
            </form>
        </div>
        <div style="width: 48%;height: 200px;float: right;text-align: center;">
            <h4 style="color:indianred;">How to use hashtags</h4>
            <br>
            <iframe width="360" height="162" src="https://www.youtube.com/embed/ezhHkTXbyQw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
    <br>
    <?php

} else if(@$_GET['action']=="addProfile")
{


    ?>
    <div>
        <div style="width: 48%;height: 200px;float: left;">
            <h3 style="font-weight: 300;" align="center">Add User</h3>

            <h5 style="font-weight: 300;" align="center">Please Note : There is a 10k follower limit due to API limitations.</h5>

            <br><br>

            <form id="follower" action="follower_sniping_options.php"  enctype="multipart/form-data" method="post">

                <p style="margin-bottom: 30px;">
                    <label  for="first-name-icon">Enter Profile below</label>
                    <input  id="first-name-icon" class="form-control" name="follower" type="text" required>

                </p>
                <p style="width: 100%;" class="right">
                    <input value="Submit"  style="border: 0px;" type="submit">
                </p>
            </form>
        </div>
        <div style="width: 48%;height: 200px;float: right;text-align: center;">
            <h4 style="color:indianred;">How to use Profile Tracking</h4>
            <br>
            <iframe width="360" height="162" src="https://www.youtube.com/embed/ezhHkTXbyQw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
    <br>
    <?php

}