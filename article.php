<!DOCTYPE html>
<!-- Web Programming 2, Assignment 2
Name: Christoffer Seljehaug [470600]
File: article.php -->
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Online Newspaper - Article</title>
      <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
      <link rel='stylesheet' type='text/css' href='css/styles.css'>
   </head>

	<body id="article">
      <div class="container">
         <div class="row">
            <div class="col-md-6 col-md-offset-3 article_list">
               <a href="index.php"><h1 class="logo">Online Newspaper</h1></a>
					<?php
					// Establish connection to the database
					include_once("connect.php");

					// Select the database
					$query = 'USE online_newspaper_db';
					if ($db->exec($query)===false){
						die('Can not select db:' . $db->errorInfo()[2]);
					}

               // Retrieve the article_id
               $article_id = $_GET['id'];
               // Select the article with corresponding article_id
					$query = "SELECT a.title, a.full_text, a.img_url, a.published, u.username
						FROM articles a, users u
						WHERE a.article_id = ?
						AND a.author_id = u.user_id";
					$stmnt = $db->prepare ($query);
					if (!$stmnt->execute (array($article_id))){
						die('Query failed:' . $db->errorInfo()[2]);
					}
					$result = $stmnt->fetchObject();
					?>

					<div class="article_wrapper">
						<h2><?=htmlspecialchars_decode($result->title)?></h2>
						<img src="<?=htmlspecialchars_decode($result->img_url)?>">
						<?=htmlspecialchars_decode($result->full_text)?>
						<hr>
						<p>Written by: <span class="author"><?=htmlspecialchars_decode($result->username)?></span></p>
						<p>Published: <?=htmlspecialchars_decode($result->published)?></p>
					</div> <!-- End of article_wrapper -->

				</div> <!-- End of column -->
			</div> <!-- End of row -->
		</div> <!-- End of container -->
	</body>
</html>
