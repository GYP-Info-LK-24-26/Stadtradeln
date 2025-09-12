<?php

class SQLSelector
{

    public static function isEmailRegistered(string $email): bool|string{
        require_once "dbConfig.php";
        $link = getConnection();

        $sql = "SELECT id FROM users WHERE email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                return mysqli_stmt_num_rows($stmt) > 0;
            } else {
                return "Something went wrong. Please try again later.";
            }
        } else {
            return "Something went wrong. Please try again later.";
        }
    }

    public static function insertAndLogIn(ComplexUserData $userData): bool|string{
        require_once "dbConfig.php";
        $link = getConnection();

        $sql = "INSERT INTO users (username,passHash,firstName,lastName,email,teamID) VALUES (?,?,?,?,?,-1)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_password, $param_firstName, $param_lastName, $param_email);
            $param_username = $userData->userName;
            $param_password = password_hash($userData->password.$userData->email, PASSWORD_DEFAULT);
            $param_firstName = $userData->firstName;
            $param_lastName = $userData->lastName;
            $param_email = $userData->email;

            if (mysqli_stmt_execute($stmt)) {
                self::logIn(-1,$param_username,mysqli_insert_id($link));
                return true;
            } else {
                return "Something went wrong. Please try again later.";
            }
        } else {
            return "Something went wrong. Please try again later.";
        }
    }

    public static function checkUserAndLogIn(string $email,string $password): bool|string{
        include_once "util/dbConfig.php";
        $link = getConnection();

        $sql = "SELECT id,teamID,username,passHash FROM users WHERE email = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $id, $teamID, $username, $passHash);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password.$email, $passHash)){
                            self::logIn($teamID, $username,$id);
                            return true;
                        }else{
                            return "Wrong password.";
                        }
                    }else{
                        return "Something went wrong. Please try again later.";
                    }
                }else{
                    return "No account found with that email.";
                }
            }else{
                return "Something went wrong. Please try again later.";
            }
        }else{
            return "Something went wrong. Please try again later.";
        }
    }

    private static function logIn(int $teamID,string $username,int $id): void{
        $_SESSION["loggedin"] = true;
        $_SESSION["teamID"] = $teamID;
        $_SESSION["username"] = $username;
        $_SESSION["id"] = $id;
        $_SESSION["last_activity"] = time();

        if(isset($_GET["redirect"])) header("Location: " . $_GET["redirect"]);
        else header("location: ". LOGGED_IN_PAGE);
    }

    public static function createTeam(string $teamName): bool|string{
        require_once "dbConfig.php";
        $link = getConnection();

        $sql = "INSERT INTO teams (teamName, captainID) VALUES (?,?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $teamName, $_SESSION["id"]);

            if (mysqli_stmt_execute($stmt)) {
                return self::changeTeam($teamName);
            } else {
                return "Something went wrong. Please try again later.";
            }
        } else {
            return "Something went wrong. Please try again later.";
        }
    }

    public static function changeTeam(string $teamName): bool|string{
        require_once "dbConfig.php";
        $link = getConnection();

        $teamID = self::getTeamId($teamName);
        if($teamID === false)return "This team doesn't exist.";
        else if(is_string($teamID)){
            return $teamID;
        }

        $sql = "UPDATE users SET teamID = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $teamID, $_SESSION["id"]);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION["teamID"] = $teamID;
                return true;
            }
        }

        return "Something went wrong. Please try again later.";
    }

    public static function insertTour(float $distance,string $date): bool|string{
        require_once "dbConfig.php";
        $link = getConnection();

        $sql = "INSERT INTO tours (userID,distance,date) VALUES (?,?,?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            str_replace($distance,',','.');
            mysqli_stmt_bind_param($stmt, "ids", $_SESSION["id"],$distance, $date);
            if (mysqli_stmt_execute($stmt)) {
                return true;
            }
        }

        return "Something went wrong. Please try again later.";
    }

    public static function alterTour(int $tourID,int $distance,$date): bool|string{
        require_once "dbConfig.php";
        $link = getConnection();

        $sql = "UPDATE tours SET distance = ?,date = ? WHERE tourID = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iii", $distance, $date, $tourID);
            if (mysqli_stmt_execute($stmt)) {
                return true;
            }
        }

        return "Something went wrong. Please try again later.";
    }

    public static function getUserTours(): ArrayObject{
        return self::getTours($_SESSION["id"]);
    }

    public static function getTeamName(int $teamID): ?string{
        include_once "util/dbConfig.php";
        $link = getConnection();

        $sql = "SELECT teamName FROM teams WHERE teamID = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $teamID);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $teamName);
                    if(mysqli_stmt_fetch($stmt)){
                        return $teamName;
                    }else{
                        return "Something went wrong. Please try again later.";
                    }
                }else{
                    return "No account found with that email.";
                }
            }else{
                return "Something went wrong. Please try again later.";
            }
        }else{
            return "Something went wrong. Please try again later.";
        }
    }

    public static function isTeamExistent(string $teamName): bool|string{
        $teamName = self::getTeamId($teamName);
        if(is_numeric($teamName)){
            return true;
        }else if(!$teamName)return false;
        return $teamName;
    }

    public static function getTeamId(string $teamName): int|false|string{
        include_once "util/dbConfig.php";
        $link = getConnection();

        $sql = "SELECT teamID FROM teams WHERE teamName = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $teamName);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $teamId);
                    if(mysqli_stmt_fetch($stmt)){
                        return $teamId;
                    }else{
                        return "Something went wrong. Please try again later.";
                    }
                }else{
                    return false;
                }
            }else{
                return "Something went wrong. Please try again later.";
            }
        }else{
            return "Something went wrong. Please try again later.";
        }
    }

    public static function getTours(int $userID): ArrayObject{
        require_once "dbConfig.php";
        $link = getConnection();

        $tours = new ArrayObject();

        $sql = "SELECT tourID, distance, date FROM tours WHERE userID = ? ORDER BY date DESC";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $userID);
            if (mysqli_stmt_execute($stmt)) {
                $results = mysqli_stmt_get_result($stmt);
                while ($row = $results->fetch_assoc()) {
                    $tours->append(new Tour($_SESSION["id"],$row["tourID"], $row["date"], $row["distance"]));
                }
            }
        }

        return $tours;
    }

    /*public static function countUserDistance(int $userID): int{
        $tours = self::getTours($userID);
        $distance = 0;
        foreach ($tours as $tour) {
            $distance += $tour->distance;
        }

        return $distance;
    }*/

    public static function getUserToursForTeam(int $team): ArrayObject{
        require_once "dbConfig.php";
        $link = getConnection();

        $tours = new ArrayObject();

        $sql = "SELECT distance,date,userID,tourID FROM tours INNER JOIN users ON tours.userID = users.id WHERE users.teamID = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $team);
            if (mysqli_stmt_execute($stmt)) {
                $results = mysqli_stmt_get_result($stmt);
                while ($row = $results->fetch_assoc()) {
                    $tours->append(new Tour($row["userID"],$row["tourID"], $row["date"], $row["distance"]));
                }
            }
        }

        return $tours;
    }

    /*public static function countTeamDistance(int $teamID): int{
        $tours = self::getUserToursForTeam($teamID);
        $distance = 0;
        foreach ($tours as $tour) {
            $distance += $tour->distance;
        }
        return $distance;
    }*/

    public static function getTeamMembers(int $teamID): ArrayObject{
        require_once "dbConfig.php";
        $link = getConnection();

        $teams = new ArrayObject();
        $sql = "SELECT id,username FROM users";
        if($teamID !== -1)$sql .= " WHERE teamID = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            if($teamID !== -1) mysqli_stmt_bind_param($stmt, "i", $teamID);
            if (mysqli_stmt_execute($stmt)) {
                $results = mysqli_stmt_get_result($stmt);
                while ($row = $results->fetch_assoc()) {
                    $teams->append(new UserData($row["id"], $row["username"],$teamID,0));
                }
            }
        }

        return $teams;
    }

    public static function getTeamMembersFull(int $teamID): ArrayObject{
        require_once "dbConfig.php";
        $link = getConnection();

        $teams = new ArrayObject();
        $sql = "SELECT  users.id,  users.username,  COALESCE(SUM(tours.distance),0) AS totalDistance FROM users LEFT JOIN tours ON users.id = tours.userID ";
        if($teamID !== -1) $sql .= "WHERE teamID = ? ";
        $sql .= "GROUP BY users.id, users.username ORDER BY totalDistance DESC";

        if ($stmt = mysqli_prepare($link, $sql)) {
            if($teamID !== -1) mysqli_stmt_bind_param($stmt, "i", $teamID);
            if (mysqli_stmt_execute($stmt)) {
                $results = mysqli_stmt_get_result($stmt);
                while ($row = $results->fetch_assoc()) {
                    $teams->append(new UserData($row["id"], $row["username"],$teamID,$row["totalDistance"]));
                }
            }
        }

        return $teams;
    }

    public static function getTeams(): ArrayObject{
        require_once "dbConfig.php";
        $link = getConnection();

        $teams = new ArrayObject();
        $sql = "SELECT teamID, teamName, captainID FROM teams";

        if ($stmt = mysqli_prepare($link, $sql)) {
            ///mysqli_stmt_bind_param($stmt, "i", $teamID);
            if (mysqli_stmt_execute($stmt)) {
                $results = mysqli_stmt_get_result($stmt);
                while ($row = $results->fetch_assoc()) {
                    $teams->append(new TeamData($row["teamID"], $row["teamName"],count(self::getTeamMembers($row["teamID"])),$row["captainID"]));
                }
            }
        }

        return $teams;
    }

    public static function getTeamsFull(): ArrayObject{
        require_once "dbConfig.php";
        $link = getConnection();

        $teams = new ArrayObject();
        $sql = "SELECT  teams.teamID,  teams.teamName,   COALESCE(SUM(tours.distance),0) AS totalDistance,COUNT(DISTINCT users.id) AS memberCount,teams.captainID FROM users LEFT JOIN tours ON users.id = tours.userID INNER JOIN teams ON users.teamID = teams.teamID GROUP BY teams.teamID, teams.teamName ORDER BY totalDistance DESC;";

        if ($stmt = mysqli_prepare($link, $sql)) {
            ///mysqli_stmt_bind_param($stmt, "i", $teamID);
            if (mysqli_stmt_execute($stmt)) {
                $results = mysqli_stmt_get_result($stmt);
                while ($row = $results->fetch_assoc()) {
                    $team = new TeamData($row["teamID"], $row["teamName"],$row["memberCount"],$row["captainID"]);
                    $team->teamTotalDistance = $row["totalDistance"];
                    $teams->append($team);
                }
            }
        }

        return $teams;
    }

    public static function getTeam($teamID): ?TeamData{
        include_once "util/dbConfig.php";
        $link = getConnection();

        $sql = "SELECT teamName, captainID FROM teams WHERE teamID = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $teamID);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $teamName,$captainID);
                    if(mysqli_stmt_fetch($stmt)){
                        return new TeamData($teamID,$teamName,count(self::getTeamMembers($teamID)),$captainID);
                    }
                }
            }
        }

        return null;
    }
}