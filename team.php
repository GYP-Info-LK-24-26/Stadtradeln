<?php

require_once "util/session_check.php";
require_once "util/db.php";

$teamString = "";
$totalDistance = 0;
$totalTours = 0;
$avgTourDistance = 0.0;
$teamMembers = array();
if($_SESSION["teamID"] !== -1) {
    $team = SQLSelector::getTeam($_SESSION["teamID"]);
    if($team !== null) {
        $team->countTotalDistance();
        $teamMembers = SQLSelector::getTeamMembers($_SESSION["teamID"]);
        $totalDistance = $team->teamTotalDistance;
        $totalTours = $team->teamTotalTours;
        if($totalTours != 0) $avgTourDistance = $team->teamTotalDistance / $team->teamTotalTours;


        foreach ($teamMembers as $teamMember) {
            $teamString .= sprintf("<li>\n <span class=\"name\">%s</span>\n</li>",$teamMember->userName);
        }
    }
}
?>
<!DOCTYPE html>
<head>
    <title>Team Overview</title>
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
            <p>Ihr Team hat <?php echo $totalDistance?> kilometer <br> in <?php echo $totalTours ?> Touren gesamelt <br> mit einem schnitt von <?php echo $avgTourDistance ?> km/tour</p>
            <p>Team: (<?php echo count($teamMembers)?> Mitglieder)</p>
            <ul class="stat-list" id="teamMembers"><?php echo $teamString; ?></ul>
        </div>
        <div id="noTeam" style="display: none">
            <p>Sie sind nicht mitglied eines teams</p>
            <a href="teamAss.php">Team Beitreten/Erstellen</a>
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