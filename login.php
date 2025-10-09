<?php

//TODO add already logged in page link
const LOGGED_IN_PAGE = "dashboard.php";

//TODO added sign up link
const SIGN_UP_PAGE = "register.php";

// Initialize the session
session_start();


// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ". LOGGED_IN_PAGE);
    exit;
}

// Include config file
require_once "util/db.php";

$login_err = $email = $password = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){

    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $login_err = "Du musst eine E-Mail eingeben";
    } else{
        $email = trim($_POST["email"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $login_err = "Du musst ein Passwort eingeben";
    } else{
        $password = trim($_POST["password"]);
    }

    if(empty($login_err)){
        $loginCheck = SQLSelector::checkUserAndLogIn($email, $password);

        if($loginCheck !== true){
            $login_err = $loginCheck;
        }
    }
}
?>

<!Doctype html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link rel="stylesheet" href="style/default.css">
    <script>
        function validateForm() {
            const email = document.forms["loginForm"]["email"].value;
            const password = document.forms["loginForm"]["password"].value;

            if(email === ""){
                document.getElementById("reg_err").innerHTML='Du musst eine E-Mail eingeben';
                return false;
            }
            if (password === "") {
                document.getElementById("reg_err").innerHTML='Du musst ein Passwort eingeben';
                return false;
            }

            return true;
        }
    </script>
</head>

<body>

    <div style="text-align: center">

        <form name="loginForm" onsubmit="return validateForm()" method="post" required>
            <div class="from-group">
                <label for="email">E-Mail</label>
                <input id="email" type="email" name="email" value="<?php echo $email; ?>" autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Passwort</label>
                <input id="password" type="password" name="password" autocomplete="current-password">
                <!--<span class="invalid-feedback"><?php //echo $login_err; ?></span>!-->
            </div>

            <p id="reg_err"><?php echo  $login_err?></p>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Einloggen">

                <a href="/" class="btn btn-danger ml-3">Abbrechen</a>
            </div>



            <p>Du hast noch keinen Account? <a href="<?php echo SIGN_UP_PAGE . (isset($_GET["redirect"])?"?redirect=" . $_GET["redirect"]:"") ?>">Registrieren</a>.</p>
        </form>
    </div>
</body>
