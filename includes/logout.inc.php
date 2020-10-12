<?php

session_start();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600);
}
// session_unregister("id");
$_SESSION = [];
// echo "z" . $_SESSION['id'] . "z"; 
session_destroy();
// echo "a" . $_SESSION['id'] . "a"; 
// session_start();
// echo "b" . $_SESSION['id'] . "b"; 

echo"<script>window.location.href='../login.php'</script>";