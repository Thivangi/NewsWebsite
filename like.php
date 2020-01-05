<?php
/*
This is the php script that will run when a user likes or dislikes a story. 
*/
session_start();
require 'database.php';

//Prevent CSRF Attacks
if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}
//Get relevant variables
$storyId = $_SESSION['lastViewed'];
$username = $_SESSION['loggedInUser'];


//If a user likes a post
if (isset($_POST['like'])){

    //Insert a new like into the likes table
    $stmt = $mysqli->prepare("insert into likes (storyId, username) values (?, ?)");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('ss', $storyId, $username);
    $stmt->execute();
    $stmt->close();
    //Redirect to story page
    header('Location: viewStory.php?storyId='.$storyId);
    exit;
}

//If a user unlikes a post
if (isset($_POST['unlike'])){
    //Delete like from likes table
    $stmt = $mysqli->prepare("delete from likes where storyId=? and username=?");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->bind_param('is', $storyId, $username);
    $stmt->execute();
    $stmt->close();
    //Redirect to story page
    header('Location: viewStory.php?storyId='.$storyId);
    exit;

}

echo "something went wrong.";
?>