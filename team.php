<?php

require_once "util/session_check.php";
require_once "util/db.php";

$teamString = "";
$totalDistance = 0;
$totalTours = 0;
$avgTourDistance = 0.0;
$teamMembers = array();
$displayString = "";
if($_SESSION["teamID"] !== -1) {
    $team = SQLSelector::getTeam($_SESSION["teamID"]);
    if($team !== null) {
        $team->countTotalDistance();
        $teamMembers = SQLSelector::getTeamMembers($_SESSION["teamID"]);
        $totalDistance = $team->teamTotalDistance;
        $totalTours = $team->teamTotalTours;
        if($totalTours != 0) $avgTourDistance = $team->teamTotalDistance / $team->teamTotalTours;


        foreach ($teamMembers as $teamMember) {
            $teamString .= sprintf("<li>\n <span class=\"name\">%s</span>\n</li>",$teamMember->userName . ($_SESSION["id"] === $teamMember->userID ? " (Du)":"") . ($teamMember->userID === $team->captainID?" (Captain)":""));
        }
    }
    $displayString = '<p>Dein Team hat ' . $totalDistance .' Kilometer <br> in ' . $totalTours . ' Touren gesamelt <br> mit einem Schnitt von ' .$avgTourDistance . ' km/Tour</p>
            <span>Team (' . count($teamMembers) . ' Mitglieder) <a href="leaderboard.php?type=team">Zur Teamrangliste</a></span>
            <ul class="stat-list" id="teamMembers">' . $teamString .' </ul>';
}else{
    $displayString = "Du bist nicht in einem Team";
}
?>
<!DOCTYPE html>
<head>
    <title>Teamübersicht</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/style/default.css">
    <link rel="stylesheet" href="/style/team.css">
    <link rel="stylesheet" href="style/list.css">
    <link rel="stylesheet" href="style/nav.css">
</head>
<body>
    <?php require_once "util/nav.php" ?>
    <div style="text-align: center;">
        <div id="teamOverview">
            <?php echo $displayString?>
        </div>
        <div id="noTeam" style="display: none">
            <p>Du bist nicht Mitglied eines Teams</p>
            <a href="teamAss.php">Team erstellen/beitreten</a>
        </div>
    </div>

    <script type="javascript">
        const hasTeam = <?php echo $_SESSION["teamID"] !== -1?"true":"false" ?>;

        if(!hasTeam){
            document.getElementById("teamOverview").style.display = "none";
            document.getElementById("noTeam").style.display = "";
        }
    </script>
</body>
