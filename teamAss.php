<?php

require_once "util/session_check.php";

$createTeam = (isset($_GET["type"]) && $_GET["type"] === "create") || (isset($_POST["type"]) && $_POST["type"] === "create");

require_once "util/db.php";

$err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

        if ($_SESSION["teamID"] != -1) {
            $err = "Du bist schon in einem Team";
        }else if(!isset($_POST["team_name"])){
            $err = "Interner Fehler";
        } else if (empty(trim($_POST["team_name"]))) {
            $err = "Du musst einen Teamnamen eingeben";
        }else {
            if($_SESSION["teamID"] != -1){
                $err = "Du bist schon in einem Team";
            }else{
                $teamExists = SQLSelector::isTeamExistent(trim($_POST["team_name"]));
                if(is_string($teamExists)) {
                    $err = $teamExists;
                }
                if($createTeam) {
                    if ($teamExists) {
                        $err = "Es gibt bereits ein Team mit diesem Namen";
                    } else {
                        $createTeamCheck = SQLSelector::createTeam(trim(htmlspecialchars($_POST["team_name"])));
                        if ($createTeamCheck !== true) {
                            $err = $createTeamCheck;
                        }
                        if($createTeamCheck === true){
                            header("Location: " . "dashboard.php");
                        }
                    }

                }else{
                    if(!$teamExists){
                        $err = "Dieses Team existiert nicht";
                    }else {
                        $joinTeamCheck = SQLSelector::changeTeam(trim($_POST["team_name"]));
                        if ($joinTeamCheck !== true) {
                            $err = $joinTeamCheck;
                        }
                        if($joinTeamCheck === true){
                            header("Location: " . "dashboard.php");
                        }
                    }
                }
            }
        }

}

$teams = SQLSelector::getTeamsFull();
$teamString = "";

foreach ($teams as $team) {
    $teamString .= sprintf("<li>\n <span class=\"name\">%s</span> \n <span class=\"big\">%s members</span> \n <span class=\"small\">%skm</span> \n</li>",$team->teamName, $team->teamMemberCount, $team->teamTotalDistance);
}

?>
<!Doctype html>
<head>
    <title>Join/Create Team</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/style/default.css">
    <link rel="stylesheet" href="/style/team.css">
    <link rel="stylesheet" href="style/list.css">
    <link rel="stylesheet" href="style/popup.css">
    <link rel="stylesheet" href="style/nav.css">
</head>
<body>
    <?php require_once "util/nav.php" ?>
    <div style="text-align: center">
        <div id="selector">
            <span style="cursor: pointer" onclick="createTeamOverlay()">Team Erstellen</span><br>
            <label style="display: none" for="teamSearch">Team Suchen</label>
            <input type="text" id="teamSearch" placeholder="team" oninput="searchF()">
            <p><?php echo $err ?></p>
            <form method="post" id="joinTeam">
                <input type="hidden" name="team_name" id="team_name"/>
                <ul style="text-align: left" id="teamList" class="stat-list click-list">
                    <?php echo $teamString; ?>
                </ul>
                <!--<input type="submit" value="Join">!-->
            </form>
        </div>

        <div class="popup-overlay" id="overlay">
            <div class="popup" id="creator">
                <span class="close" style="text-align: right" id="closeBTN" onclick="cancelTeamCreate()">Schließen</span><br>
                <form method="post" name="createTeam" id="createTeam">
                    <input type="hidden" name="type" id="type" value="create"/>

                    <label for="team_name">Name</label><br>
                    <input type="text" id="team_name" name="team_name" placeholder="Team Name"/><br>

                    <input class="button" type="submit" value="Erstellen">
                    </form>
            </div>
        </div>
        <script>
            let createTeam = <?php echo $createTeam?"true":"false" ?>;
            updateCreateOverlay();

            function createTeamOverlay(){
                createTeam = true;
                updateCreateOverlay();
            }

            function cancelTeamCreate(){
                createTeam = false;
                updateCreateOverlay();
            }

            function updateCreateOverlay(){
                document.getElementById("overlay").style.display = createTeam?"block":"none";
            }

            function searchF(){
                const list = document.getElementsByTagName("li");
                const term = document.getElementById("teamSearch").value;
                for (let i = 0; i < list.length; i++) {
                    if(list[i].textContent.indexOf(term) > -1) {
                        list[i].style.display = "";
                    }else{
                        list[i].style.display = "none";
                    }
                }
            }

            document.getElementById("teamList").addEventListener("click", function(e){
                document.getElementById("team_name").value = e.target.childNodes[1].textContent.trim();
                document.getElementById("joinTeam").submit();
            });
        </script>
    </div>
</body>
