<?php

const maxTime = 60 * 30;

session_start();

if($_SESSION["last_activity"] + maxTime < time()) {
    $_SESSION["loggedin"] = false;
}else{
    $_SESSION["last_activity"] = time();
}

if(session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: login.php?redirect=" . $_SERVER["REQUEST_URI"]);
    return;
}