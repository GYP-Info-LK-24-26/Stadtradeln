<?php
    $sites = array("/dashboard.php","/leaderboard.php","/team.php","/teamAss.php","logout.php");
    $site_names = array("Home","Leaderboard","Team","Join/Create Team","Logout");

    $localName = $_SERVER["SCRIPT_NAME"];

    $list = '<ul class="topnav">';
    for($i = 0; $i < count($sites); $i++){
        if(str_contains($sites[$i],$localName)){
            $list .= '<li><a class="active" href="">' . $site_names[$i] . '</a></li>';
        }else{
            $list .= '<li><a href="' . $sites[$i] . '">' . $site_names[$i] . '</a></li>';
        }
    }
    $list .= '</ul>';

    echo $list;
?>