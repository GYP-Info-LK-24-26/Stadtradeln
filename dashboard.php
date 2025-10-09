<?php

require_once "util/session_check.php";
require_once "util/db.php";

$tours = SQLSelector::getUserTours();
$toursData = "";
$totalDistance = 0;
foreach ($tours as $tour) {
    $totalDistance += $tour->distance;
    $toursData .= sprintf("<li>\n <span class=\"small-right\">%s km</span> \n <span class=\"small\">%s</span> \n</li>",$tour->distance,$tour->date);
}

$teamName = SQLSelector::getTeamName($_SESSION["teamID"]);

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if($_POST["type"] === "newTour" && !empty(trim($_POST["distance"]) && !empty(trim($_POST["date"])))){
        SQLSelector::insertTour(trim($_POST["distance"]),trim($_POST["date"]));
        header("Location: fuckPHP/tourInserter.php");
    }
}
?>

<!DOCTYPE html>
<head>
    <title>Dashbord</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style/default.css">
    <link rel="stylesheet" href="style/popup.css">
    <link rel="stylesheet" href="style/list.css">
    <link rel="stylesheet" href="style/nav.css">
</head>
<body>
    <?php require_once "util/nav.php" ?>
    <div style="text-align: center;">
        <div id="userDisplay">
            <p><?php echo "User: " . $_SESSION["username"] . "#" . $_SESSION["id"] ?></p>
            <?php echo $_SESSION["teamID"] == -1?'<span>Du bist nicht in einem Team: <a href="teamAss.php">Team erstellen/beitreten</a></span>':"" ?>
        </div>

        <div id="tourDisplay">
            <p><?php echo "Gesamte Kilometer " . $totalDistance ?></p>
            <button class="button" onclick="tourAdd()">Tour hinzufügen</button>
            <ul class="stat-list">
                <?php echo $toursData; ?>
            </ul>

            <script>
                function tourAdd(){
                    document.getElementById("popup").style.display = "block";
                }
            </script>
        </div>

        <div class="popup-overlay" id="popup">
            <div class="popup" id="newTour">
                <span class="close" id="closePopup" onclick="tourRem()">&times;</span>
                <script>
                    function tourRem(){
                        document.getElementById("popup").style.display = "none";
                    }
                </script>
                <form method="post" id="newTourForm" onsubmit="return checkSubmit()">
                    <input type="hidden" name="type" id="type" value="newTour"/>

                    <label for="date">Datum</label>
                    <input type="date" id="date" name="date"/><br>

                    <label for="distance">Distanz</label>
                    <input type="number" step="0.1" id="distance" name="distance" min="0"/><br>

                    <input type="submit" value="Hinzufügen">
                </form>

                <script type="javascript">
                    function checkSubmit(){
                        const date = document.forms["newTourForm"]["date"];
                        const distance = document.forms["newTourForm"]["distance"];
                        const type = document.forms["newTourForm"]["type"];

                        return !(date === null || date === "" || distance === null || distance === "" || type !== "newTour");
                    }
                </script>
            </div>
        </div>
    </div>

</body>
