<?php
define("WINDOW", 15);

/*  ~~ TODO ~~
 *
 * Implement newly created Database object class for simplified queries
 *
 */
function get_fp($user_id, $conn){
    $stmt = $conn->prepare("SELECT * FROM `false_positives` WHERE `user_id` = ?");
    $stmt->bind_param('s', $user_id);
    if(!$stmt->execute()){
        echo "<h3>ERROR 101 - FALSE POSITIVES COULD NOT BE LOADED DUE TO AN ERROR</h3>";
    }
    $results = $stmt->get_result();
    if($results->num_rows>0){
        echo '<div class="tab-pane active" id="campaigns-tab" role="tabpanel">';
        echo '<div class="table-responsive m-t-40">';
        echo '<table id="campaigns" class="display nowrap table table-hover table-bordered" cellspacing="0" width="100%">';
        echo "<thead>
                  <tr>
                    <th>Go to Profile</th>
                    <th>Username</th>
                    <th>Filter Reason</th>
                    <th>Delete</th>
                  </tr>
               </thead>
               <tfoot>
                  <tr>
                    <th>Go to Profile</th>
                    <th>Username</th>
                    <th>Filter Reason</th>
                    <th>Delete</th>
                  </tr>
                  </tr>
               </tfoot>
               <tbody>";
        while($false_positive_data = $results->fetch_assoc()) {
            echo "<tr style='background: transparent;'>" .
                "<td><a target=\"_blank\" href='https://twitter.com/" . $false_positive_data['tw_username'] . "'>View</a></td>" .
                "<td>@" . $false_positive_data['tw_username'] . "</td>" .
                "<td>" . $false_positive_data['reason'] . "</td>" .
                "<td><button value='".$false_positive_data['tw_username']."' class=\"btn btn-outline-danger square mr-1 mb-1 waves-effect waves-light\" style='width:75%;' name='delete'>DELETE</button></td>" .
                "</tr>";
        }
        echo "</tbody>" . "</table>" . "</div>";
    } else {
        echo '<div class="row m-t-40" style="text-align:center"><div class="col-md-12" style="text-align:center"><h1>There are no false positives to show.</h1>';
    }
}
function store_feedback($feedback_text, $user_id, $conn){
    if($feedback_text !== "" && $feedback_text !== null){
        $stmt = $conn->prepare("INSERT INTO user_feedback (user_id, feedback) VALUES (?,?)");
        $stmt->bind_param('ss', $user_id, $feedback_text);
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
function ignoreTwit($user_id, $tw_username, $reason, $conn){
    if (substr($tw_username, 0, 1) === "@") {
        $tw_username = substr($tw_username, 1, strlen($tw_username));
    }
    $stmt = $conn->prepare("INSERT INTO false_positives (user_id, tw_username, reason) VALUES (?,?,?)");
    $stmt->bind_param('sss', $user_id, $tw_username, $reason);
    if($stmt->execute()){
        return true;
    }
    return false;
}
function needed_tw_calls($followers){
    return ceil($followers/5000);
}
function get_value_from_table($conn, $table_name, $column_name, $desired_column, $value){
    $stmt = $conn->prepare("SELECT * FROM `$table_name` WHERE `$column_name` = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()[$desired_column];
}
function get_token($length){

    $token = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789!$()";
    $token = str_shuffle($token);
    $token = substr($token, 0, $length);
    return $token;
}

function occurences($conn, $column_name, $value){

    $entries = mysqli_query($conn, "SELECT * FROM `users` WHERE $column_name = '$value'");

    return $entries->num_rows;
}
function occurences_custom($conn, $table_name, $column_name, $value){

    $entries = mysqli_query($conn, "SELECT * FROM $table_name WHERE $column_name = '$value'");

    return $entries->num_rows;
}

function intersect($Set1AsString, $Set2AsString, $Separator){
    $set1 = explode($Separator, $Set1AsString);
    $set2 = explode($Separator, $Set2AsString);
    return array_intersect($set1, $set2);
}

function sqlQueryToValue($query_result){
    $a = mysqli_fetch_array($query_result);
    return $a[0];
}
function create_field($tag = "input", $class, $type, $name, $placeholder = "", $close_tag = "", $preWrap = "", $postWrap = "", $error_log = array(), $below = false, $attribute = "", $val = ""){
    $value = $val;
    $tooltip_segment = "";
    $style = "";

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST[$name])){
        require_once 'htmlpurifier/library/HTMLPurifier.auto.php';
        $purifier = new HTMLPurifier();
        $value = $purifier->purify($_POST[$name]);
    }

    if (isset($error_log[$name])){
        $tooltip_segment .= "title='" . "$error_log[$name]'";
        $style = 'background-color: #ffc0cb;border-color: #ff0000;border-style:solid;border-width: 1px;';

        if ($below){
            $postWrap = "<br><small style='color: #ff0000'>" . str_replace("\n", "<br>", $error_log[$name]) . "</small>" . $postWrap;
        }
    }

    echo $preWrap . "<$tag class='$class' style='$style' type='$type' id='$name' name='$name' " . $tooltip_segment . " required value='$value' placeholder='$placeholder' " . $attribute . ">" . $close_tag . $postWrap;
}

function unique_value($conn, $column_name, $value){
    $returnValue = true;

    $entries = mysqli_query($conn, "SELECT * FROM users WHERE $column_name = '$value'");

    if ($entries->num_rows > 0){
        $returnValue = false;
    }
    mysqli_free_result($entries);
    return $returnValue;
}
function checkPwd($_pwd){
    $error_log = "";

    $pwd = str_split($_pwd);
    $num = str_split("0123456789");
    $upper = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
    $lower = str_split("abcdefghijklmnopqrstuvwxyz");
    $symbol = str_split("!@#$%^&*()~-_+=`?:{}<>,.");

    if (sizeof($pwd) < 8){
        $error_log .= "Password must have at least 8 characters.\n";
    }

    if (sizeof(array_intersect($num, $pwd)) == 0){
        $error_log .= "Password must contain at least 1 number.\n";
    }

    if (sizeof(array_intersect($upper, $pwd)) == 0){
        $error_log .= "Password must contain at least 1 uppercase letter.\n";
    }

    if (sizeof(array_intersect($lower, $pwd)) == 0){
        $error_log .= "Password must contain at least 1 lowercase letter.\n";
    }

    if (sizeof(array_intersect($symbol, $pwd)) == 0){
        $error_log .= "Password must contain at least 1 symbol.\n";
    }
    return $error_log;
}
define('DB_SERVER_NAME', "");
define('DB_USERNAME', "");
define('DB_PASSWORD', "");
define('DB_NAME', "");
$conn = mysqli_connect(DB_SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME) or die(mysqli_connect_error());

