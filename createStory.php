<?php

/*
Page where a user can create and submit a story under their username. 
*/
session_start();
// make sure the user is logged in
if (!isset($_SESSION['loggedInUser'])) {
    $_SESSION['error'] = "You must be logged in to access that page. Please log in.";
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Bungee+Shade|Roboto&display=swap" rel="stylesheet">

    <title>Create A Story</title>
</head>

<body>
    <h1><a href='main.php'>WUSTL News</a></h1>
    <h2>Create a story</h2>
    <form method="POST">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
        <label>Give your story a title:</label>
        <input type="text" name="title">
        <br>
        <label> Enter the contents of your story:</label>
        <br>
        <textarea name="storyContent" cols="50" rows="20"></textarea>
        <input type="submit" name="submit">
    </form>

    <?php
    if (isset($_POST['submit'])) {
        //check to see if title is valid
        if (strlen((string) $_POST['title']) <= 2 or strlen((string) $_POST['title'] > 255)) {
            echo "<p class='error'>Invalid title, either too many or too few characters.</p>";
            exit;
        }
        //check to see if there is text inside of the story content
        if (strlen((string) $_POST['storyContent']) < 5) {
            echo "<p class='error'>Not enough story content, please add more text.</p>";
            exit;
        }

        require 'database.php';
        //Prevent CSRF Attacks
        if (!hash_equals($_SESSION['token'], $_POST['token'])) {
            die("Request forgery detected");
        }
        //Create variables from form submission
        $title = (string) $_POST['title'];
        $content = (string) $_POST['storyContent'];
        


        //Update news.stories table with variables
        $stmt = $mysqli->prepare("insert into stories (username, title, storyContent) values (?, ?, ?)");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('sss', $_SESSION['loggedInUser'], $title, $content);
        $stmt->execute();
        $storyId = mysqli_insert_id($mysqli);
        $stmt->close();

        //Insert link into table
        $link = (string) "http://ec2-18-225-6-166.us-east-2.compute.amazonaws.com/~austintolani/module3/viewStory.php?storyId=".$storyId;
        $stmt = $mysqli->prepare("update stories set link=? where storyId=?");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('si', $link,$storyId);
        $stmt->execute();
        $stmt->close();




        //Generate link to see submitted story
        printf("<p class='success'> Story successfully submitted. <a href='viewStory.php?storyId=%s'>Click here to view.</a>", $storyId);
    }
    ?>
</body>

</html>