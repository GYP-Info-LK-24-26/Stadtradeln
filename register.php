<?php
const TosLink = "";
const LoginLink = "login.php";
const LOGGED_IN_PAGE = "dashboard.php";

session_start();

//require_once "util/session_check.php";

// Include config file
require_once "util/db.php";

$login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $submitted_values = array_map("trim",$_POST);

    if(empty($submitted_values["username"])){
        $login_err = "Username is required";
    }else if(empty($submitted_values["password"])){
        $login_err = "Password is required";
    }else if(empty($submitted_values["email"])){
        $login_err = "Email is required";
    }else if(empty($submitted_values["first_name"])){
        $login_err = "First name is required";
    }else if(empty($submitted_values["last_name"])){
        $login_err = "Last name is required";
    }

    if(empty($login_err)) {
        $emailCheck = SQLSelector::isEmailRegistered($submitted_values["email"]);
        if($emailCheck !== false){
            $login_err = "Email is already registered";
        }else if($login_err !== true){
            $login_err = $emailCheck;
        }

        if (empty($login_err)) {
            $userData = new ComplexUserData(-1,trim(htmlspecialchars($submitted_values["username"])),-1,0);
            $userData->firstName = $submitted_values["first_name"];
            $userData->lastName = $submitted_values["last_name"];
            $userData->password = $submitted_values["password"];
            $userData->email = $submitted_values["email"];

            $insertCheck = SQLSelector::insertAndLogIn($userData);
            if($insertCheck !== true){
                $login_err = $insertCheck;
            }
        }
    }
}
?>

<!Doctype html>
<head>
    <title>Register</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/default.css">
    <script>
        async function validateForm() {
            const email = document.forms["register"]["email"].value;
            const username = document.forms["register"]["username"].value;
            const firstName = document.forms["register"]["first_name"].value;
            const lastName = document.forms["register"]["last_name"].value;
            const password = document.forms["register"]["password"].value;
            const password_confirm = document.forms["register"]["confirm_password"].value;

            if(email == null || email === "" || username == null || username === "" || password == null || password === ""
                || password_confirm == null || password_confirm === "" || firstName == null || firstName === "" || lastName == null || lastName === ""){
                document.getElementById("err").innerHTML='You have to fill out every field';
                return false;
            }

            if(password !== password_confirm){
                document.getElementById("reg_err").innerHTML = 'Password and Password Confirm do not match';
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div style="text-align: center;">

        <form id="register_form" name="register" onsubmit="return validateForm()" method="post" required>
            <div class="form-group">
                <label for="email" >E-mail</label>
                <input type="email" name="email" id="email" class="form-control" value="" autocomplete="username">
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" class="form-control" value="" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="form-control" value="" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" value="" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" id="password_confirm" name="confirm_password" class="form-control" value="" autocomplete="off">
            </div>

            <p id="reg_err"><?php echo  $login_err?></p>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Register">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
                <input type="button" onclick="window.location.href = '/'" class="btn btn-danger ml-3" value="Cancel">
            </div>
            <p>By pressing submit you agree to the <a href=<?php echo TosLink ?>> Terms of usage</a>.</p>
            <p>Already have an account? <a href=<?php echo LoginLink ?>>Login here</a>.</p>
            <!--<button onclick="validateForm()" type="button">Test</button>!-->
        </form>


    </div>
</body>