<?php



function check_session(){
    session_start();
    if (!isset($_SESSION['id'])){
        end_session();
        echo "<script>window.location.href = './login.php'</script>";
        return;
    }
    return;
}

function end_session(){
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600);
    }
    $_SESSION = [];
    session_destroy();
    return;
}

function get_record($conn, $table_name, $known_column, $known_value){
    $result = mysqli_query($conn, "SELECT * FROM `$table_name` WHERE $known_column = $known_value");
    if (!$result){
        mysqli_free_result($result);
        return false;
    }
    return $result;
}

function get_element($conn, $table_name, $known_column, $known_value, $column){
    $a = get_record($conn, $table_name, $known_column, $known_value);
    if (!$a){
        return "Query Unavailable";
    }
    $ret = mysqli_fetch_assoc(get_record($conn, $table_name, $known_column, $known_value));
    return $ret[$column];
}

function num_of_campaigns($conn, $brand_id){
    $result = mysqli_query($conn, "SELECT * FROM campaigns WHERE brand_id = $brand_id");
    return $result->num_rows;
}

function total_profiles($conn, $brand_id, $campaign_id  = NULL){
    $ret = 0;
    $specs = "";
    if (!empty($campaign_id)){
        $specs = "AND campaign_id = $campaign_id";
    }
    $result = mysqli_query($conn, "SELECT profiles_reached FROM posts WHERE brand_id = $brand_id " . $specs);
    while ($row = mysqli_fetch_assoc($result)) {
        $ret += $row["profiles_reached"];
    }
    return $ret;
}

function dateToDisplay($date){
    return str_replace("-", "/", substr($date, 0, 10));
}

?>
