<?php
/*
PHP Script for deleting a comment
*/
session_start();
require 'database.php';

//Prevent CSRF Attacks
if(!hash_equals($_SESSION['token'], $_POST['token'])){
	die("Request forgery detected");
}

$stmt = $mysqli->prepare("delete from comments where commentId=? AND username =? AND storyId=? ");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('isi', $_POST['commentId'],$_SESSION['loggedInUser'],$_SESSION['lastViewed']);
$stmt->execute();
$stmt -> close();
//Redirect to previous page using the session lastViewed variable
header('location: viewStory.php?storyId='.$_SESSION['lastViewed']);
?>