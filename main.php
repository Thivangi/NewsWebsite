<?php
/*
Main page for a *logged in user*. This is where the logged in user will be able to view stories, create stories and logout.
*/
session_start();
require 'database.php';
//Make sure the user is logged in
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
	<title>WUSTL News</title>
</head>

<body>
	<h1><a href='main.php'>WUSTL News</a></h1>
	<div class='center'>
		<a href="createStory.php">Create a Story</a> |
		<a href="logout.php">Logout</a>
	</div>

	<h2>Trending Articles</h2>
	<div class="stories">
		<?php

		//Get list of 5 most likede artticles and display them in a list
		$stmt = $mysqli->prepare("SELECT COUNT(*), likes.storyId,stories.title from likes JOIN stories on (likes.storyId=stories.storyId) GROUP BY likes.storyId ORDER BY likes.storyId ASC LIMIT 5");
		if (!$stmt) {
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->execute();
		$stmt->bind_result($numLikes, $storyId, $title);
		while ($stmt->fetch()) {
			printf(
				"\t<p><a href='viewStory.php?storyId=%s'>%s</a>(%s likes)</p>\n",
				htmlspecialchars($storyId),
				htmlspecialchars($title),
				htmlentities($numLikes)
			);
		}
		?>
	</div>
	<h2>Recently Posted Articles</h2>
	<div class="stories">
		<?php

		//query all articles, order by when they were created
		$stmt = $mysqli->prepare("select storyId, username, title from stories ORDER BY createdTime DESC");
		if (!$stmt) {
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->execute();
		$stmt->bind_result($storyId, $author, $title);
		// create links for all articles, sorted by date
		while ($stmt->fetch()) {
			printf(
				"\t<p><a href='viewStory.php?storyId=%s'>%s</a></p>\n",
				htmlspecialchars($storyId),
				htmlspecialchars($title)
			);
		}
		echo "</div>";

		echo "<h2>Your Articles</h2>";
		echo "<div class='stories'>";

		//reset query
		$stmt->execute();
		$stmt->bind_result($storyId, $author, $title);
		// create links for stories written by the user logged in. 
		while ($stmt->fetch()) {
			if ((string) $author == (string) $_SESSION['loggedInUser']) {
				printf(
					"\t<p><a href='viewStory.php?storyId=%s'>%s</a></p>\n",
					htmlspecialchars($storyId),
					htmlspecialchars($title)
				);
			}
		}
		$stmt->close();

		?>
	</div>
</body>

</html>