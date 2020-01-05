<?php
/*
This is the page that shows an individual story. Which story to show is identified by the storyId variable in the url. 
*/
session_start();

// make sure a story id is specified
if (!isset($_GET['storyId'])) {
    header('Location: notFound.html');
    exit;
}
// Set session variable to id of last viewed story (for use in addComment.php)
$_SESSION['lastViewed'] = $_GET['storyId'];

require 'database.php';

// Get data for story
$stmt = $mysqli->prepare("select username, title, storyContent,createdTime from stories where storyId =?");
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->bind_param('i', $_GET['storyId']);
$stmt->execute();
$stmt->bind_result($author, $title, $content, $createdTime);
$stmt->fetch();

// If nothing is returned by the query, redirect to notFound.html
if ($content == NULL) {
    header('Location: notFound.html');
    exit;
}
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Bungee+Shade|Roboto&display=swap" rel="stylesheet">
    <?php
    printf('<title>%s</title>', $title);
    ?>
</head>

<body>
    <h1><a href='main.php'>WUSTL News</a></h1>
    <?php
    //Write data to web page
    printf("<h2>%s</h2>\n", $title);
    printf("<p>Posted by <strong>%s</strong> on  %s</p>\n", $author, $createdTime);
    //allow author to edit their own story
    if (isset($_SESSION['loggedInUser']) and (string) $author == (string) $_SESSION['loggedInUser']) {
        echo "<a href='editStory.php'> Edit this story</a> | ";
        echo "<a href='deleteStory.php'>Delete This Story</a><br>";
    }

    // Allow logged in users to like the story
    if (isset($_SESSION['loggedInUser'])) {
        //Check to see if user has already liked the story
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM likes where storyId =? AND username=?");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('is', $_GET['storyId'], $_SESSION['loggedInUser']);
        $stmt->execute();
        $stmt->bind_result($userLiked);
        $stmt->fetch();
        $stmt->close();

        //Get number of likes for the story
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM likes where storyId =?");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $_GET['storyId']);
        $stmt->execute();
        $stmt->bind_result($numlikes);
        $stmt->fetch();
        $stmt->close();

        echo "$numlikes Likes";


        if ($userLiked == 1) {
            echo '<form action="like.php" method="POST">
        <input type="submit" name="unlike" value="Unlike This Story">
        <input type="hidden" name="token" value="' . $_SESSION['token'] . '" />
    </form>';
        } else {
            echo '<form action="like.php" method="POST">
        <input type="submit" name="like" value="Like This Story">
        <input type="hidden" name="token" value="' . $_SESSION['token'] . '" />
    </form>';
        }
    }


    printf("<p>%s<p>\n", $content);
    echo "<h3> Comments</h3>";
    // Get data for comments
    $stmt = $mysqli->prepare("select commentId, username, comment from comments where storyId =? ORDER BY createdTime ASC");
    if (!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param('i', $_GET['storyId']);
    $stmt->execute();
    $stmt->bind_result($commentId, $commentUser, $comment);



    while ($stmt->fetch()) {

        //Delete functionality for the user's authored comments

        $deleteComment = '';
        $editComment = '';

        if (isset($_SESSION['loggedInUser']) and (string) $_SESSION['loggedInUser'] == (string) $commentUser) {
            $deleteComment = '
        <form action="deleteComment.php" method="POST">
            <input type="hidden" name="commentId" value ="' . $commentId . '">
            <input type="hidden" name="token" value="' . $_SESSION['token'] . '" />
            <input type="submit" name="submit" value="delete">
        </form>';
            $editComment = '
            <form action="editComment.php" method="POST">
            <input type="hidden" name="commentId" value ="' . $commentId . '">
            <input type="hidden" name="token" value="' . $_SESSION['token'] . '" />
            <input type="submit" name="submit" value="edit">
        </form>';
        }

        printf(
            "\t<p><strong>%s</strong>: %s</p>\n%s\n",
            htmlspecialchars($commentUser),
            htmlspecialchars($comment),
            $deleteComment
        );
        printf($editComment);
    }
    $stmt->close();

// Allow logged in users to add comments
    if (isset($_SESSION['loggedInUser'])) {
        echo "<form action='addComment.php' method='POST'>
    <label><strong>Add Comment:</strong></label>
    <br>
    <textarea name='comment' cols='80' rows='5'></textarea>
    <input type='hidden' name='token' value='" . $_SESSION['token'] . "' />
    <input type='submit' name ='submit'>
</form> ";
    }

    ?>

</body>

</html>