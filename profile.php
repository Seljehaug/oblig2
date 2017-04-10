<!DOCTYPE html>
<!-- Web Programming 2, Assignment 2
Name: Christoffer Seljehaug [470600]
File: profile.php -->
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Online Newspaper - User Registration</title>
      <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="js/slidepanels.js"></script>
      <link rel='stylesheet' type='text/css' href='css/styles.css'>
   </head>

	<body id="profile">
      <div class="container">
         <div class="row">
            <div class="col-md-6 col-md-offset-3">
               <a href="index.php"><h1 class="logo">Online Newspaper</h1></a>

					<?php
					session_start();
					require_once("connect.php");
					require_once("functions.php");

					// Select the database
					$query = 'USE online_newspaper_db';
					if ($db->exec($query)===false){
						die('Can not select db:' . $db->errorInfo()[2]);
					}

					// Retrieving session variables for the user
					$username = $_SESSION['username'];
					$firstname = $_SESSION['firstname'];
					$lastname = $_SESSION['lastname'];
					$user_id = $_SESSION['user_id'];
					?>

					<div class="info_text">
						<h2 class="greeting">Welcome, <?=$firstname . ' ' . $lastname?>!</h2>
						<p>Manage your profile and posted articles by using the tools below</p>
					</div>

					<?php
					echo '<pre>';
					var_dump($_SESSION);
					echo '</pre>';

					// Retrieve all information about the user that's stored in the database
					$query= "SELECT a.*, u.*
						FROM articles a, users u
						WHERE u.username = ?
						AND a.author_id = u.user_id";

					$stmnt = $db->prepare($query);
					if (!$stmnt->execute(array($username))){
						die('Query failed:' . $db->errorInfo()[2]);
					}

					$rows = $stmnt->fetchAll(PDO::FETCH_OBJ);

					?>
					<!-- Adding new article -->
					<h3 id="toggle_new_article">Add New Article</h3>
					<div class="new_article">
						<form class="new_article_form" action="profile.php" method="POST" enctype="multipart/form-data">

							<label for="new_title">Title*
								<input type="text" name="new_title" value="<?php if(isset($_POST['new_title'])){echo $_POST['new_title'];}?>">
							</label>
							<label for="new_category">Category*
								<select class="new_category_ddl" name="new_category">
									<option selected="selected">Entertainment</option>
									<option>Politics</option>
									<option>Sports</option>
								</select>
							</label>
							<label>Image:
								<input type="file" name="new_img">
							</label>
							<h5>Summary*</h5>
							<textarea class="summary_textarea" name="new_summary"><?php if(isset($_POST['new_summary'])){echo $_POST['new_summary'];}?></textarea>
							<h5>Full text*</h5>
							<p>Use normal html markup, and remember to escape quotation marks. <br/> (For example: &lt;p&gt;\"A quote\"&lt;/p&gt;)</p>
							<textarea class="article_textarea" name="new_full_text"><?php if(isset($_POST['new_full_text'])){echo $_POST['new_full_text'];}?></textarea>
							<input type="submit" name="new_article_submit" value="Upload">

						</form>

						<?php
						// User clicks upload for a new article
						if(isset($_POST['new_article_submit'])){
							$title = get_post('new_title');
							$summary = get_post('new_summary');
							$category = $_POST['new_category'];
							$full_text = $_POST['new_full_text'];
							// $user_id retreived SESSION variable earlier
							$author_id = $user_id;
							$img_url = "";


							// Check if file has been uploaded
							if (isset($_FILES["new_img"]["name"])) {
								$name = $_FILES["new_img"]["name"];
								$tmp_name = $_FILES['new_img']['tmp_name'];
								$error = $_FILES['new_img']['error'];
								$location = 'images/';

								// Get the file extension
								$extension = strtolower(substr($name, strpos($name, '.') +1));

								// Check if file exists
								if (!empty($name)) {

									// Check that file is jpg, jpeg, png or gif
									if($extension != "jpg" && $extension != "png" && $extension != "jpeg" && $extension != "gif" ) {
										echo "<p class='error'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
									}
									// File is ok
									else{
										// Move the file to the images folder
										move_uploaded_file($tmp_name, $location.$name);
										$img_url = $location.$name;
									}
								}
							}

							$msg = "";
							// CHECK FOR NO EMPTY FIELDS
							foreach($_POST as $var=>$value) {
								// IF EMPTY
								if(empty($_POST[$var])) {
									$msg .= "<p class='error'>Please fill out all required fields</p>";
								break;
								}
							}

							if($msg==""){
								// IF OK, RUN QUERY
								$article = array($author_id, $title, $category, $summary, $full_text, $img_url);

								$sql = "INSERT INTO articles (author_id, title, category, summary, full_text, img_url) values (?,?,?,?,?,?)";
								$query = $db->prepare($sql);

								$query->execute($article);
							}
							else{
								echo $msg;
							}
						}
						?>

					</div> <!-- End of new_article -->

					<!-- Article management -->
					<h3 id="toggle_article_list">Manage Your Articles</h3>
					<div class='article_list'>

					<?php
					// Display articles to the browser
					foreach ($rows as $row) { ?>
						<div class="article_wrapper">
							<h4><?=htmlspecialchars_decode($row->title)?></h4>
							<img src="<?=htmlspecialchars_decode($row->img_url)?>">
							<p class="summary"><?=htmlspecialchars_decode($row->summary)?></p>
							<div class="user_tools">
								<a href="#">Edit</a>
								<a href="#">Delete</a>
							</div>
							<hr>
						</div> <!-- End of article_wrapper -->
					<?php }
					?>
					</div> <!-- End of article_list -->

					<!-- Changee Password -->
					<h3 id="toggle_edit_account">Edit Account Data</h3>
					<div class="edit_account">
						<label for="change_firstname">Change First Name</label>
						<input type="text" name="change_firstname" placeholder="First Name" value="">
						<label for="current_pw">Current Password:
							<input type="password" name="current_pw">
						</label>
						<label for="new_pw">New Password:
							<input type="password" name="new_pw">
						</label>
						<input type="submit" name="change_pw_submit" value="Confirm">
					</div> <!-- End of change_pw -->

				</div> <!-- End of column -->
			</div> <!-- End of row -->
		</div> <!-- End of container -->

	</body>
</html>

<?php

?>
