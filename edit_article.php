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

	<body id="edit_article">
      <div class="container">
         <div class="row">
            <div class="col-md-6 col-md-offset-3 article_list">
               <a href="index.php"><h1 class="logo">Online Newspaper</h1></a>
					<?php
               session_start();
               echo "<pre>";
               print_r($_SESSION);
               // print_r($_GET);
               echo "</pre>";

					// Establish connection to the database
					require_once("connect.php");
					require_once("functions.php");

					// Retrieve the article_id
               $article_id = $_GET['id'];
					$query = "SELECT c.*, u.*, a.*
						FROM categories c, users u, articles a
						WHERE c.category_id = a.category_id
						AND a.article_id = ?
						AND a.author_id = u.user_id";
					$stmnt = $db->prepare ($query);
					if (!$stmnt->execute (array($article_id))){
						die('Query failed:' . $db->errorInfo()[2]);
					}

               // Store result values as variables
					$result = $stmnt->fetchObject();
					$author_id = $result->author_id;
					$title = $result->title;
					$category_id = $result->category_id;
					$current_category = $result->category;
					$summary = $result->summary;
					$full_text = $result->full_text;
					$img_url = $result->img_url;

					// Retrieves category_list, See functions.php
					$category_list = get_categories($db);
					// echo "<pre>";
					// print_r($category_list);
					// echo "</pre>";
					echo $current_category;

					echo "<a href='profile.php'>Back to Profile</a>";

					// User clicks confirm article
					if(isset($_POST['submit'])){
						// Get the updated values that the user added
						$title = get_post('title');
						$summary = get_post('summary');
						$full_text = $_POST['full_text'];
						$category = $_POST['category'];
						// Loop through assoc array for categories and get
						// category_id that corresponds to the chosen category
						for ($i=0; $i < count($category_list); $i++) {
							if($category_list[$i]['category'] == $category){
								$category_id = $category_list[$i]['category_id'];
							}
						}

						// Check if file (image) has been uploaded
						if (isset($_FILES["img"]["name"])) {
							$name = $_FILES["img"]["name"];
							$tmp_name = $_FILES['img']['tmp_name'];
							$error = $_FILES['img']['error'];
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
						// Check if any fields are empty
						foreach($_POST as $var=>$value) {
							// If empty, give error message
							if(empty($_POST[$var])) {
								if($var != "msg") {
									$msg .= "<p class='error'>Please fill out all fields</p>";
								}
							break;
							}
						}

						// If there are no errors, insert article to database
						if($msg==""){
							$article = array($author_id, $title, $category_id, $summary, $full_text, $img_url, $article_id);
							$sql = "UPDATE articles SET author_id=?, title=?, category_id=?, summary=?, full_text=?, img_url=? WHERE article_id=?";
							$query = $db->prepare($sql);
							$query->execute($article);
							$msg .= "<p class='success'>Success: Article updated.</p>";
						}
						echo $msg;
					}

					// if(isset($_POST['delete_article'])){
				   //    $query= "DELETE FROM articles
				   //       WHERE article_id = ?";
               //
				   //    $stmnt = $db->prepare($query);
				   //    if (!$stmnt->execute(array($article_id))){
				   //       die('Query failed:' . $db->errorInfo()[2]);
				   //    }
				   //    header("Location: profile.php");
				   //    die();
					// }

					// Edit content. Default: use value from database. If new value is set, replace the old one ?>
					<div class="article_wrapper">
						<h4>EDIT ARTICLE</h4>
						<form class="edit_article_form" action="edit_article.php?id=<?=$article_id?>" method="POST" enctype="multipart/form-data">
							<label for="title">Title:</label>
							<input type="text" name="title" value="<?php if(isset($_POST['title'])){echo $_POST['title'];}else{echo htmlspecialchars_decode($title);}?>">
							<label for="category">Category:
							<select class="edit_category_ddl" name="category">
								<?php
								// Make sure the selected option is the original category
								echo "<option selected='selected'>$current_category</option>";
								foreach ($category_list as $category_array => $category) {
									if($category['category'] != $current_category){
										echo "<option>" . $category['category'] . "</option>";
									}
								}
								?>
							</select>
							</label>
							<label for="summary">Summary:</label>
							<textarea name="summary"><?php if(isset($_POST['summary'])){echo $_POST['summary'];}else{echo htmlspecialchars_decode($summary);}?></textarea>
							<label for="full_text">Full text:</label>
							<textarea class="full_text" name="full_text"><?php if(isset($_POST['full_text'])){echo $_POST['full_text'];}else{echo htmlspecialchars_decode($full_text);}?></textarea>
							<label for="img">Image:
								<input type="file" name="img">
							</label>
							<img src="<?=htmlspecialchars_decode($img_url)?>">
							<input type="hidden" name="msg">
							<input type="submit" name="submit" value="Confirm">
						</form>

					</div> <!-- End of article_wrapper -->
				</div> <!-- End of column -->
			</div> <!-- End of row -->
		</div> <!-- End of container -->
	</body>
</html>
