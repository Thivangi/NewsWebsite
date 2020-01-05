<?php
/*
Database connection to be used in other files. 
*/
$mysqli = new mysqli('localhost', 'news_user', 'n3wspa55', 'news');
if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>
