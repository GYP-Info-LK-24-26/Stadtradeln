<?php

require_once "util/session_check.php";

require_once "util/db.php";

$type = !isset($_GET["type"])?"global":$_GET["type"];
$teamID = !isset($_GET["teamID"])?-1:$_GET["teamID"];
//$viewType = !isset($_GET["view"])?"users":($_GET["view"] === "users"?"users":"teams");
$viewUsers = !isset($_GET["view"]) || $_GET["view"] !== "teams";

if($type === "global")$teamID = -1;

$teamString = "";
$index = 0;

if($viewUsers) {
    $teamMembers = SQLSelector::getTeamMembersFull($teamID);
    foreach ($teamMembers as $teamMember) {
        $index++;
        $teamString .= sprintf("<li> \n <span class=\"name\">%s</span> \n <span class=\"big\">#%s</span> \n <span class=\"small\">%skm</span> \n </li>", $teamMember->userName, $index, $teamMember->totalDistance);
    }
}else{
    $teams = SQLSelector::getTeamsFull();
    foreach ($teams as $team) {
        $index++;
        $teamString .= sprintf("<li> \n <span class=\"name\">%s</span> \n <span class=\"big\">#%s</span> \n <span class=\"small\">%skm</span> \n </li>", $team->teamName, $index, $team->teamTotalDistance);
    }
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
    <ul class="stat-list">
        <li>
            <span class="name"><?php echo $viewUsers?"Username":"Team" ?></span>
            <span class="big">Rank</span>
        </li>
        <?php echo $teamString; ?>
    </ul>
</body>
