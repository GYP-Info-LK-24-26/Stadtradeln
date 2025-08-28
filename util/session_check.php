<?php

session_start();

if(session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: login.php");
    return;
}