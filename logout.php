<?php
session_start();
$_SESSION["loggedIn"] = false;
session_unset();

header("Location: ". "/");