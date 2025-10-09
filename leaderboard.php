<?php

require_once "util/session_check.php";

require_once "util/db.php";

$teamID = -1;
$viewUsers = false;

/*      View types:
 *      team: show team member ranking
 *      all: show all member ranking
 *      global/unset: show teams
 */

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
    $teamMembers = SQLSelector::getTeamMembersFull($teamID, $_GET["page"] ?? 0);
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
    <ul class="stat-list" id="switch" style="cursor: pointer"  onclick="changeType()">
        <li >
            <span class="name"><?php echo $viewUsers?"Benutzername":"Team" ?></span>
            <span class="big">Rang</span>
        </li>
    </ul>

    <ul id="leaderboard" class="stat-list" style="margin-top: 0.75rem">
            <?php echo ($viewUsers?$userString:$teamString); ?>
    </ul>

    <script>
        let viewUsers = <?php echo $viewUsers?"true":"false" ?>;

        function changeType(){
            let url = new URL(window.location.href);
            if(!url.searchParams.has("type") || url.searchParams.get("type") === "global") {
                url.searchParams.set("type", "team");
            }else{
                if(url.searchParams.get("type") === "team"){
                    url.searchParams.set("type", "all");
                }else{
                    url.searchParams.set("type", "global");
                }
            }

            window.location.replace(url);
        }

    </script>
</body>
