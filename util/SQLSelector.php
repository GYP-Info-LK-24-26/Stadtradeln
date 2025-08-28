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
                if (mysqli_stmt_num_rows($stmt) == 0) {
                    return true;
                }else{
                    return false;
                }
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
            $param_username = $userData->password;
            $param_firstName = $userData->firstName;
            $param_lastName = $userData->lastName;
            $param_email = $userData->email;

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION["loggedin"] = true;
                $_SESSION["teamID"] = -1;
                $_SESSION["username"] = $param_username;
                $_SESSION["id"] = mysqli_insert_id($link);

                header("location: ". LOGGED_IN_PAGE);
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
                            $_SESSION["loggedin"] = true;
                            $_SESSION["teamID"] = $teamID;
                            $_SESSION["username"] = $username;
                            $_SESSION["id"] = $id;

                            header("location: ". LOGGED_IN_PAGE);
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

    public static function createTeam(string $teamName): bool|string{
        require_once "dbConfig.php";
        $link = getConnection();

        $sql = "INSERT INTO teams (teamName, captainID) VALUES (?,?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $teamName, $_SESSION["teamID"]);

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

        $sql = "UPDATE users SET teamID = (SELECT teamID FROM teams WHERE teamName = ?) WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $teamName, $_SESSION["id"]);
            if (mysqli_stmt_execute($stmt)) {
                $sql = "SELECT teamID FROM users WHERE id = ?";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_bind_result($stmt, $teamID);
                        if (mysqli_stmt_fetch($stmt)) {
                            $_SESSION["teamID"] = $teamID;
                            return true;
                        }
                    }
                }
            }
        }

        return "Something went wrong. Please try again later.";
    }

    public static function insertTour(int $distance,string $date): bool|string{
        require_once "dbConfig.php";
        $link = getConnection();

        $sql = "INSERT INTO tours (userID,distance,date) VALUES (?,?,?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iis", $_SESSION["id"], $distance, $date);
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

    public static function getTeamId(string $teamName): int{
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
                    return "No account found with that email.";
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

        $sql = "SELECT tourID, distance, date FROM tours WHERE userID = ?";
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

    public static function countUserDistance(int $userID): int{
        $tours = self::getTours($userID);
        $distance = 0;
        foreach ($tours as $tour) {
            $distance += $tour->distance;
        }

        return $distance;
    }

    public static function getUserToursForTeam(int $team): ArrayObject{
        require_once "dbConfig.php";
        $link = getConnection();

        $tours = new ArrayObject();

        $sql = "SELECT distance,date FROM tours INNER JOIN users ON tours.userID = users.id WHERE users.teamID = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $team);
            if (mysqli_stmt_execute($stmt)) {
                $results = mysqli_stmt_get_result($stmt);
                while ($row = $results->fetch_assoc()) {
                    $tours->append(new Tour($_SESSION["id"],$row["tourID"], $row["date"], $row["distance"]));
                }
            }
        }

        return $tours;
    }

    public static function countTeamDistance(int $teamID): int{
        $tours = self::getUserToursForTeam($teamID);
        $distance = 0;
        foreach ($tours as $tour) {
            $distance += $tour->distance;
        }
        return $distance;
    }

    public static function getTeamMembers(int $teamID): ArrayObject{
        require_once "dbConfig.php";
        $link = getConnection();

        $teams = new ArrayObject();

        $sql = "SELECT id,username FROM users WHERE teamID = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $teamID);
            if (mysqli_stmt_execute($stmt)) {
                $results = mysqli_stmt_get_result($stmt);
                while ($row = $results->fetch_assoc()) {
                    $teams->append(new UserData($row["id"], $row["username"],$row["teamID"]));
                }
            }
        }

        return $teams;
    }

    public static function getTeams(): ArrayObject{
        require_once "dbConfig.php";
        $link = getConnection();

        $teams = new ArrayObject();
        $sql = "SELECT teamID, teamName FROM teams";

        if ($stmt = mysqli_prepare($link, $sql)) {
            ///mysqli_stmt_bind_param($stmt, "i", $teamID);
            if (mysqli_stmt_execute($stmt)) {
                $results = mysqli_stmt_get_result($stmt);
                while ($row = $results->fetch_assoc()) {
                    $teams->append(new TeamData($row["teamID"], $row["teamName"],count(self::getTeamMembers($row["teamID"]))));
                }
            }
        }

        return $teams;
    }
}