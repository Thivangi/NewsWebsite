<?php
/*
Main page for a *guest user* (a user that is not logged in). This is where the guest user can view stories
*/
require 'database.php';
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
		<a href="login.php">Login</a>
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
	

		?>
	</div>
</body>

</html>