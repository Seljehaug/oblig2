<!DOCTYPE html>
<!-- Web Programming 2, Assignment 2
Name: Christoffer Seljehaug [470600]
File: index.php -->
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Online Newspaper</title>
      <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
      <link rel='stylesheet' type='text/css' href='css/styles.css'>
   </head>

	<body id="index">
      <div class="container">
         <div class="row">
            <div class="col-xs-12 header">
               <a href="index.php"><h1 class="logo">Online Newspaper</h1></a>
               <a class="login_link" href="login.php">Login</a>
               <a class="register_link" href="register.php">Register</a>
               <?php
               session_start();

               // If user is logged in, show profile and logout links
               if(isset($_SESSION['isloggedin'])){?>
                  <a class="logout_link" href="logout.php">Log out</a>
                  <a class="profile_link" href="profile.php">View Profile</a>
                  <?php
               }?>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6 col-md-offset-3 article_list">

					<?php
					include_once("connect.php");
               print_r($_SESSION);

					// Select the database
					$query = 'USE online_newspaper_db';
					if ($db->exec($query)===false){
						die('Can not select db:' . $db->errorInfo()[2]);
					}

               // User is logged in
               if(isset($_SESSION['isloggedin'])){
                  echo "LOGGED IN";
                  // DO STUFF ONLY FOR LOGGED IN USERS
                  // ADD NEW ARTICLE
                  // EDIT
                  // DELETE
               }

					// Select all articles
					$sql= "SELECT * FROM articles";
					$stmt = $db->query($sql);
					$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

					// Display articles to the browser
					foreach ($rows as $row) { ?>
						<div class="article_wrapper">
							<h2><?=htmlspecialchars_decode($row->title)?></h2>
							<img src="<?=htmlspecialchars_decode($row->img_url)?>">
							<p class="summary"><?=htmlspecialchars($row->summary)?></p>
							<a class="read_more_button" href="article.php?id=<?=$row->article_id?>">Read more <span>&#187;</span></a>
						</div> <!-- End of article_wrapper -->
					<?php } ?>

				</div> <!-- End of column -->
			</div> <!-- End of row -->
		</div> <!-- End of container -->
	</body>
</html>

	<?php
	// close the connection
	$db = null;
	?>
