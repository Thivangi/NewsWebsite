<?php
/*
This script logs out the user and redirects them to the login page. 
*/
session_start();

session_destroy();
header("Location: login.php");

?>