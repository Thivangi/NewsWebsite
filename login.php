<?php
/*
Login page for news website. Users can log in or alternatively create an account. 

*/
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Bungee+Shade|Roboto&display=swap" rel="stylesheet">
    <title>WUSTL News</title>
</head>

<body>
    <div class="center">
        <h1>WUSTL NEWS</h1>
        <i>The best website to view and post news about Washington University in St. Louis. </i>

        <br>
        <br>
        <form method="POST">
            <label>Enter your username:</label>
            <input type="text" name="username">
            <label>Enter your password:</label>
            <input type="password" name="password">
            <input type="submit" name="submit" value="Login">
        </form>

        <p><a href="createUser.php">Create an account</a> | <a href="public.php">Continue as Guest</a></p>
    </div>
    <?php

    require 'database.php';

    if (isset($_POST['submit'])) {

        //Authenticate username and password
        $stmt = $mysqli->prepare("SELECT COUNT(*),password from users WHERE username=?");

        // Bind the parameter
        $stmt->bind_param('s', $user);
        $user = $_POST['username'];
        $stmt->execute();

        // Bind the results
        $stmt->bind_result($cnt, $pwd_hash);
        $stmt->fetch();

        $pwd_guess = $_POST['password'];
        // Compare the submitted password to the actual password hash
        if ($cnt == 1 && password_verify($pwd_guess, $pwd_hash)) {
            // Login succeeded!
            $_SESSION['loggedInUser'] = $user;
            // Generate session token
            $_SESSION['token'] = bin2hex(random_bytes(32));
            header("Location: main.php");
        } else {
            // Login failed; redirect back to the login screen
            $_SESSION['error'] = "Incorrect username or password provided.";
            header("Location: login.php");
            exit;
        }
    }

    // Display any errors if the user is redirected to this page with an error. 
    if (isset($_SESSION['error'])) {
        printf("<p class='error'>%s</p>", $_SESSION['error']);
    }

    ?>



</body>

</html>

<?php
// Remove errors so that user doesn't see any errors on initial login. 
unset($_SESSION['error']);
?>