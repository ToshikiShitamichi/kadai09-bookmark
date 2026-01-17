<?php
session_start();

$_SESSION = array();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}
session_destroy();

// トップ画面に遷移
header("Location:index.html");
exit();
