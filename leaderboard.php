<?php

require_once "util/session_check.php";

require_once "util/db.php";

$teamID = -1;
$viewUsers = false;

if(isset($_GET["type"])){
    if($_GET["type"] === "team"){
        $teamID = $_SESSION["teamID"];
        $viewUsers = true;
    }else if($_GET["type"] === "all"){
        $viewUsers = true;
    }
}

$teamString = "";
$userString = "";

    $index = 0;
    $teamMembers = SQLSelector::getTeamMembersFull($teamID);
    foreach ($teamMembers as $teamMember) {
        $index++;
        $userString .= sprintf("<li> \n <span class=\"name\">%s</span> \n <span class=\"big\">#%s</span> \n <span class=\"small\">%skm</span> \n </li>", $teamMember->userName, $index, $teamMember->totalDistance);
    }

    $index = 0;
    $teams = SQLSelector::getTeamsFull();
    foreach ($teams as $team) {
        $index++;
        $teamString .= sprintf("<li> \n <span class=\"name\">%s</span> \n <span class=\"big\">#%s</span> \n <span class=\"small\">%skm</span> \n </li>", $team->teamName, $index, $team->teamTotalDistance);
    }

?>

<!Doctype html>
<head>
    <title>Leaderboard</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/style/default.css">
    <link rel="stylesheet" href="/style/team.css">
    <link rel="stylesheet" href="/style/list.css">
    <link rel="stylesheet" href="style/nav.css">
</head>
<body>
    <?php require_once "util/nav.php" ?>
    <ul class="stat-list" id="switch">
        <li >
            <span style="cursor: pointer"  class="name"><?php echo $viewUsers?"Username":"Team" ?></span>
            <span class="big">Rank</span>
        </li>
    </ul>

    <ul id="leaderboard" class="stat-list" style="margin-top: 0.75rem">
            <?php echo ($viewUsers?$userString:$teamString); ?>
    </ul>

    <script type="javascript">
        let viewUsers = <?php echo $viewUsers?"true":"false" ?>;
        let userString = <?php echo $userString ?>;
        let teamString = <?php echo $teamString ?>;

        document.getElementById("switch").addEventListener("click", function(e){
            console.log("Test");
            viewUsers = !viewUsers;
            if(viewUsers){
                document.getElementById("leaderboard").innerHTML = userString;
                //params.set("type","all")
            }else{
                document.getElementById("leaderboard").innerHTML = teamString;
                //params.set("type","global")
            }
        });
    </script>
</body>
