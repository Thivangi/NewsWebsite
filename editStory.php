<!-- 
    Allow a user to edit a story. 
 -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Editor</title>
</head>

<body>
    <h1>Edit your story</h1>
    <form method="POST">
        <?php
        session_start();
        require 'database.php';
        // Get data for the story that is being edited
        $stmt = $mysqli->prepare("select username, title, storyContent,createdTime from stories where storyId =?");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $_SESSION['lastViewed']);
        $stmt->execute();
        $stmt->bind_result($author, $title, $content, $createdTime);
        $stmt->fetch();
        //Populate form fills with the existing contents of the story
        echo "<label>Edit your story title:</label>\n";
        printf("<input type='text' name='title' value=\"%s\" size='30'><br>\n", htmlentities($title));
        echo "<label> Edit the contents of your story:</label><br>\n";
        printf("<textarea name='storyContent' cols='50' rows='20'>%s</textarea>", htmlentities($content));
        $stmt->close();
        ?>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
        <input type="submit" name="submit">
    </form>
    <?php

    //check to see if form is submitted
    if (isset($_POST['submit'])) {
        //check to see if title is valid
        if (strlen((string) $_POST['title']) <= 5 or strlen((string) $_POST['title'] > 255)) {
            echo "<p class='error'>Invalid title, either too many or too few characters.</p>";
            exit;
        }
        //check to see if there is text inside of the story content
        if (strlen((string) $_POST['storyContent']) < 5) {
            echo "<p class='error'>Not enough story content, please add more text.</p>";
            exit;
        }

        //Prevent CSRF Attacks
        if (!hash_equals($_SESSION['token'], $_POST['token'])) {
            die("Request forgery detected");
        }

        //Create variables from form submission
        $title = (string) $_POST['title'];
        $content = (string) $_POST['storyContent'];

        //Update news.stories table with variables
        $stmt = $mysqli->prepare('update stories set title=?,storyContent=? where storyId=?');
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('ssi', $title, $content, $_SESSION['lastViewed']);
        $stmt->execute();
        $stmt->close();

        // Redirect to story page once complete. 
        header('location: viewStory.php?storyId=' . $_SESSION['lastViewed']);
    }

    ?>
</body>

</html>