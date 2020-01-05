<?php

/*
PHP script to be run when a user opts to delete a story
*/
session_start();
require 'database.php';


//First, delete all comments and likes associated with the story
$stmt = $mysqli->prepare("delete from comments where storyId=?");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('i', $_SESSION['lastViewed']);
$stmt->execute();
$stmt -> close();
$stmt = $mysqli->prepare("delete from likes where storyId=?");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('i', $_SESSION['lastViewed']);
$stmt->execute();
$stmt -> close();


//Then delete the story itself.
//Prepare and execute query
$stmt = $mysqli->prepare("delete from stories where storyId=?");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('i', $_SESSION['lastViewed']);
$stmt->execute();
$stmt -> close();

//Let user know the story has been deleted. 
echo "<p>Succesfully deleted.<a href='main.php'>Click here to return home.</a>";
?>