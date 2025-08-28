<?php

require_once "util/session_check.php";

$createTeam = (isset($_GET["type"]) && $_GET["type"] === "create") || (isset($_POST["type"]) && $_POST["type"] === "create");

require_once "util/db.php";

$err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

        if ($_SESSION["teamID"] != -1) {
            $err = "You are already in a team.";
        } else if (empty(trim($_POST["team_name"]))) {
            $err = "Please enter a team name.";
        } else {
            if($createTeam) {
                $createTeamCheck = SQLSelector::createTeam(trim($_POST["team_name"]), $_SESSION["id"]);
                if ($createTeamCheck !== true) {
                    $err = $createTeamCheck;
                }
            }else{
                $joinTeamCheck = SQLSelector::changeTeam(trim($_POST["team_name"]));
                if ($joinTeamCheck !== true) {
                    $err = $joinTeamCheck;
                }
            }
        }

}

$teams = SQLSelector::getTeams();
$teamString = "";

foreach ($teams as $team) {
    $teamString .= "<li>" . $team->name . "</li>";
}

?>
<!Doctype html>
<head>
    <title></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/style/default.css">
    <link rel="stylesheet" href="/style/team.css">

</head>
<body>
    <div style="text-align: center;">
        <div id="selector">
            <label style="display: none" for="teamSearch">Search Team</label>
            <input type="text" id="teamSearch" placeholder="team" oninput="searchF()">
            <form method="post" id="joinTeam">
                <input type="hidden" id="team_name">
                <ul id="teamList" class="TeamList">
                    <?php echo $teamString; ?>
                </ul>
            </form>
        </div>

        <script>
            let createTeam = <?php echo $createTeam?"true":"false" ?>;

            function createTeamOverlay(){
                createTeam = true;
            }

            function cancelTeamCreate(){
                createTeam = false;
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

            document.getElementById("teamList").addEventListener("click", function(){
                document.getElementById("team_name").value = e.target.textContent.trim();
                document.getElementById("joinTeam").submit();
            })
        </script>
    </div>
</body>
