<?php
session_start();
/*
PHP file that runs every time a user submits a comment. Inserts it into the database. 
*/

//Prevent users from posting blank comments
if (strlen($_POST['comment'])<1){
    header('location: viewStory.php?storyId='.$_SESSION['lastViewed']);
    exit;
}

//Prevent CSRF Attacks
if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}
require 'database.php';

//Set variables 
$user = $_SESSION['loggedInUser'];
$storyId = $_SESSION['lastViewed'];
$comment = (string) $_POST['comment'];

//Prepare and execute insert
$stmt = $mysqli->prepare("insert into comments (storyId, username, comment) values (?, ?, ?)");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('sss', $storyId, $user, $comment);
$stmt->execute();
$stmt->close();

//Redirect to previous page using the session lastViewed variable
header('location: viewStory.php?storyId='.$_SESSION['lastViewed']);
exit;
