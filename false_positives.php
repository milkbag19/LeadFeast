<?php
include_once("includes/session.inc.php");
check_session();
include('./header.php');
include('./includes/config.php');

if(isset($_POST['submit']))
{
    if(ignoreTwit($_SESSION['id'], $_POST['username'], $_POST['reason'], $conn) && store_feedback($_POST['feedback'], $_SESSION['id'], $conn))
    {
        echo"<script>window.location.href='./index.php';</script>";
    }
}
else if(isset($_POST['reset']))
{
    echo'<script>window.location.href="./index.php";</script>';
}
else if(isset($_POST['delete']))
{

    $stmt = $conn->prepare("DELETE FROM `false_positives` WHERE `tw_username` = ? AND `user_id` = ?");
        $stmt->bind_param('ss', $_POST['delete'], $_SESSION['id']);
        if($stmt->execute()){
            echo'<script>window.location.href="./false_positives.php";</script>';
        }

}


echo'<section id="multiple-column-form">
      <div class="col-12">
        <div class="card">
         <div class="card-content">
                <div class="card-body">
                    <h4 class="card-title">False Positives Form</h4>
                </div>
                <div class="card-body">
                    <form class="form" method="post">
                        <div class="form-body">
                            <div class="form-group">
                                <label for="feedback1" >Twitter Username</label>
                                <input type="text" id="feedback1" class="form-control" placeholder="Name" name="username" required maxlength="250">
                            </div>
        
                            <div class="form-group">
                                <label for="feedback2" >Reason (optional)</label>
                                <input type="text" id="feedback2" class="form-control" placeholder="Reason for False Positive" name="reason" maxlength="250">
                            </div>
        
                            <div class="form-group">
                                <label for="feedback3" >LeadFeast Feedback (optional)</label>
                                <textarea id="feedback3" rows="3" class="form-control" name="feedback" placeholder="Give us some feedback on our platform!" maxlength="250"></textarea>
                            </div>
                        </div>
        
                        <div class="form-actions">
                            <button type="submit" name="submit" class="btn btn-primary mr-1 waves-effect waves-light">Submit</button>
                        </div>
                    </form>
                </div>
             </div>
         </div>
          <form method="post">
              <h6 class="card-subtitle">Contact us @ digitera.agency for help with any issue!</h6>
              <div class="tab-content">';

              get_fp($_SESSION['id'], $conn);

              echo '
              </div>
          </form>
       </div>
    </div>
  </div>
</section>';

include('./footer.php');
?>
<style>
    .sorting_1{
        background: transparent !important;
    }
</style>
