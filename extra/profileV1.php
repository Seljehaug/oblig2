<!DOCTYPE html>
<!-- Web Programming 2, Assignment 2
Name: Christoffer Seljehaug [470600]
File: profile.php -->
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Online Newspaper - Profile</title>
      <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<!-- <script src="js/slidepanels.js"></script> -->
      <link rel='stylesheet' type='text/css' href='css/styles.css'>
   </head>

   <?php
   session_start();
   require_once("connect.php");
   require_once("functions.php");

   // Retrieving session variables for the user
   $username = $_SESSION['username'];
   $firstname = $_SESSION['firstname'];
   $lastname = $_SESSION['lastname'];
   $user_id = $_SESSION['user_id'];

   // Variable used to give feedback to user
   $msg = "";

   echo "<pre>";
   // var_dump($_SESSION);
   print_r($_POST);
   echo "</pre>";

   // Retrieve all information about the user that's stored in the database
   $query= "SELECT a.*, u.*
      FROM articles a, users u
      WHERE u.username = ?
      AND a.author_id = u.user_id";

   $stmnt = $db->prepare($query);
   if (!$stmnt->execute(array($username))){
      die('Query failed:' . $db->errorInfo()[2]);
   }

   // Contains all of the user's pubished articles
   $article_list = $stmnt->fetchAll(PDO::FETCH_OBJ);

   // Retrieves category_list, See functions.php
   $category_list = get_categories($db);

   // User clicks upload for a new article
   if(isset($_POST['new_article_submit'])){
      $title = get_post('new_title');
      $summary = get_post('new_summary');
      $category = $_POST['new_category'];
      $full_text = $_POST['new_full_text'];
      // $user_id retreived SESSION variable earlier
      $author_id = $user_id;
      $img_url = "";

      // Loop through assoc array for categories and get
      // category_id that corresponds to the chosen category
      for ($i=0; $i < count($category_list); $i++) {
         if($category_list[$i]['category'] == $category){
            $category_id = $category_list[$i]['category_id'];
         }
      }

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

      // Check if any of the required fields are empty
      foreach($_POST as $var=>$value) {
         // If empty, give error message
         if(empty($_POST[$var])) {
            $msg .= "<p class='error'>Please fill out all required fields</p>";
         break;
         }
      }

      // If there are no errors, insert article to database
      if($msg==""){
         $article = array($author_id, $title, $category_id, $summary, $full_text, $img_url);
         $sql = "INSERT INTO articles (author_id, title, category_id, summary, full_text, img_url) values (?,?,?,?,?,?)";
         $query = $db->prepare($sql);
         $query->execute($article);
         $msg = "<p class='success'>Success: Article added";
         header("Location: profile.php");
         die();
      }
   }

   // If user clicks on delete article
   else if(isset($_POST['delete'])){
      echo "HEEELOOO".$article_id;
      $query= "DELETE FROM articles
         WHERE article_id = ?";

      $stmnt = $db->prepare($query);
      if (!$stmnt->execute(array($article_id))){
         die('Query failed:' . $db->errorInfo()[2]);
      }
      header("Location: profile.php");
      die();
      // echo "<p class'success'>Success: Article deleted</p>";
   }

   // If user clicks on modify user details
   else if(isset($P_POST['edit_details'])){
      echo "Hello World";
   }
   ?>
	<body id="profile">
      <div class="container">
         <div class="row">
            <a href="index.php"><h1 class="logo">Online Newspaper</h1></a>
            <div class="cold-md-12 info_text">
               <h2 class="greeting">Welcome, <?=$firstname . ' ' . $lastname?>!</h2>
               <p>Manage your profile and posted articles by using the tools below</p>
            </div> <!-- End of info_text -->
         </div> <!-- End of row -->

         <div class="row">
            <div class="col-md-12">

               <!-- Add new article -->
               <div class="col-md-6 new_article">
                  <!-- Adding new article -->
   					<h3>Add New Article</h3>
                  <p><?=$msg?></p>
						<form class="new_article_form" action="profile.php" method="POST" enctype="multipart/form-data">
							<label for="new_title">Title*
								<input type="text" name="new_title" value="<?php if(isset($_POST['new_title'])){echo $_POST['new_title'];}?>">
							</label>
                     <label for="category">Category:
   							<select class="new_category_ddl" name="new_category">
   								<?php
   								foreach ($category_list as $category_array => $category) {
										echo "<option>" . $category['category'] . "</option>";
   								}
   								?>
   							</select>
							</label>
							<label for="new_img">Image:<input type="file" name="new_img"></label>
							<h5>Summary*</h5>
							<textarea class="summary_textarea" name="new_summary"><?php if(isset($_POST['new_summary'])){echo $_POST['new_summary'];}?></textarea>
							<h5>Full text*</h5>
							<p>Use normal html markup</p>
							<textarea class="article_textarea" name="new_full_text"><?php if(isset($_POST['new_full_text'])){echo $_POST['new_full_text'];}?></textarea>
							<input type="submit" name="new_article_submit" value="Upload">
						</form>
   				</div> <!-- End of new_article -->

               <!-- Article list -->
               <div class="col-md-6 article_management">
                  <!-- ARTICLE MANAGEMENT -->
   					<h3>Your articles</h3>
   					<div class='article_list'>

   					<?php
                  // Need this when deleting article
                  $article_id = "";
                  // Display articles to the browser
   					foreach ($article_list as $article) { ?>
   						<div class="article_wrapper">
   							<h4><?=htmlspecialchars_decode($article->title)?></h4>
   							<img src="<?=htmlspecialchars_decode($article->img_url)?>">
   							<p class="summary"><?=htmlspecialchars_decode($article->summary)?></p>
                        <p><?=$article->article_id?></p>
   							<div class="user_tools">
   								<a href="edit_article.php?id=<?=$article->article_id?>">Edit</a>
                            <!-- onsubmit="return confirm('Are you sure you want to delete this article?');" -->
                           <form class="delete_article" action="profile.php" method="POST">
                              <?php $article_id = $article->article_id?>
                              <input type="submit" name="delete" value="Delete">
                           </form>
   							</div>
   							<hr>
   						</div> <!-- End of article_wrapper -->
   					<?php
                  }
                  ?>
                  </div> <!-- End of article_list -->
               </div> <!-- End of article_management -->
            </div> <!-- End of column md-12 -->
         </div> <!-- End of row -->

         <div class="row">
            <div class="col-md-6 edit_account">
               <!-- Change Password -->
   				<h3>Edit Account Data</h3>
               <form class="edit_details_form" action="profile.php" method="POST">
   					<label for="change_firstname">Change first name to</label>
   					<input type="text" name="change_firstname" placeholder="First name" value="">
                  <label for="change_lastname">Change last name to</label>
   					<input type="text" name="change_lastname" placeholder="Last name" value="">
                  <label for="change_username">Change username to:</label>
   					<input type="text" name="change_username" placeholder="New username" value="">
   					<label for="current_pw">Current Password:
   						<input type="password" name="current_pw">
   					</label>
   					<label for="new_pw">New Password:
   						<input type="password" name="new_pw">
   					</label>
   					<input type="submit" name="edit_details" value="Confirm">
               </form>
				</div> <!-- End of edit_account -->
         </div> <!-- End of row -->

      </div> <!-- End of container -->
	</body>
</html>
