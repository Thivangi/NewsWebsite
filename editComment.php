<!-- 
    Allow a user to edit a comment. 
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
    <h1>Edit your comment</h1>
    <form method="POST">
        <?php
        session_start();
        // echo ($_POST['commentId']);
        // $saveCommentId = $_POST['commentId'];
        // echo ($saveCommentId);


        require 'database.php';
        // Get data for the comment that is being edited
        $stmt = $mysqli->prepare("select username, comment from comments where commentId =?");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->bind_param('i', $_POST['commentId']);

        $stmt->execute();
        $stmt->bind_result($username, $comment);
        $stmt->fetch();
        //Populate form fills with the existing contents of the comment
    
        echo "<label> Edit the contents of your comment:</label><br>\n";
        printf("<textarea name='commentContent' cols='50' rows='20'>%s</textarea>", htmlentities($comment));
        $stmt->close();
        ?>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
        <input type="hidden" name="commentId2" value ="<?php echo $_POST['commentId']; ?>" />
        <input type="submit" name="submit2">
    </form>
    <?php
    //check to see if form is submitted
    if (isset($_POST['submit2'])) {

        //check to see if there is text inside of the comment content
        if (strlen((string) $_POST['commentContent']) < 1) {
            echo "<p class='error'>Not enough text, please add more text.</p>";
            exit;
        }

        //Prevent CSRF Attacks
        if (!hash_equals($_SESSION['token'], $_POST['token'])) {
            die("Request forgery detected");
        }

        //Create variables from form submission
        $commentContent = (string) $_POST['commentContent'];

        //Update news.comments table with variables
        $stmt = $mysqli->prepare('update comments set comment=? where commentId=?');
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        
        $stmt->bind_param('si', $commentContent,$_POST['commentId2']);
        $stmt->execute();
        $stmt->close();

        // Redirect to story page once complete. 
        header('location: viewStory.php?storyId=' . $_SESSION['lastViewed']);
    }

    ?>
</body>

</html>