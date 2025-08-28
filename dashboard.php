<?php

require_once "util/session_check.php";
require_once "util/db.php";

$tours = SQLSelector::getUserTours();
$toursData = "";
$totalDistance = 0;
foreach ($tours as $tour) {
    $totalDistance += $tour->distance;
    $toursData .= "<li>" . $tour->tourID . "," . $tour->date . "," . $tour->distance . "</li>";
}

$teamName = SQLSelector::getTeamName($_SESSION["teamID"]);

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if($_POST["type"] == "newTour" && !empty(trim($_POST["distance"]) && !empty(trim($_POST["date"])))){
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
</head>
<body>

    <div style="text-align: center;">
        <div id="userDisplay">
            <p><?php echo "User: " . $_SESSION["username"] . "#" . $_SESSION["id"] ?></p>
        </div>

        <div id="tourDisplay">
            <p><?php echo "Gesamte Kilometer " . $totalDistance ?></p>
            <ul>
                <?php echo $toursData; ?>
            </ul>
        </div>

        <div id="newTour">
            <form method="post" id="newTourForm">
                <input type="hidden" name="type" id="type" value="newTour" onsubmit="return checkSubmit()"/>

                <label for="date">Date</label>
                <input type="date" id="date" name="date"/>

                <label for="distance">Distance</label>
                <input type="number" id="distance" name="distance" min="0"/>

                <input type="submit" value="Add">
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

</body>
