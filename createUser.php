<!-- 
Page where a user can create a new account with the site. 
 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Bungee+Shade|Roboto&display=swap" rel="stylesheet">
    <title>Create an Account</title>
</head>

<body>
    <div>
        <h1>WUSTL NEWS</h1>
        <h2>Create an account</h2>
        <form method='POST'>
            <label>Please enter your new username:</label>
            <input type="text" name="newUsername">
            <label>Please enter your new password:</label>
            <input type="password" name="newPassword">
            <input type="submit" name="submit" value="Create Account">

            <?php
            require 'database.php';

            if (isset($_POST['submit'])) { //check to see if submit button is pressed
                //make sure submitted username a valid username
                if (!preg_match('/^(?=.{5,30}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/', (string) $_POST['newUsername'])) {
                    echo "<div class = 'error'>Username invalid. A username must be between 5 and 30 characters and not contain any invalid characters. Please try again.</div>";
                    exit;
                }
                // make sure submitted password is a valid password
                if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).{6,13}$/', (string) $_POST['newPassword'])) {
                    echo "<div class = 'error'> Password invalid. A password must have one lower case letter, one upper case letter, one digit, 6-13 length, and no spaces.";
                    exit;
                }

                //Set variables
                $username = $_POST['newUsername'];
                $password = password_hash($_POST['newPassword'], PASSWORD_DEFAULT); // use salted hash to store password

                // insert new row in users table
                $stmt = $mysqli->prepare("insert into users (username,password) values (?,?)");
                if (!$stmt) {
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->bind_param('ss', $username, $password);
                $stmt->execute();
                $stmt->close();

                //Start session and set logged in user session variable to the new username
                session_start();
                $_SESSION['loggedInUser'] = $username;
                // Generate session token
                $_SESSION['token'] = bin2hex(random_bytes(32));

                //Allow user to log in
                echo "<p class='success'>Account succesfully created <a href='main.php'>click here</a> to log in.</p>";
            }

            ?>
        </form>
    </div>
</body>

</html>