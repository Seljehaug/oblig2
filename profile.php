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

   echo "<pre>";
   print_r($_SESSION);
   echo "</pre>";

   // Retrieving session variables for the user
   $username = $_SESSION['username'];
   $firstname = $_SESSION['firstname'];
   $lastname = $_SESSION['lastname'];
   $user_id = $_SESSION['user_id'];

   // Variable used to give feedback to user
   $msg = "";

   // Retrieve all information about the user that's stored in the database
   $query= "SELECT a.*, u.*
      FROM articles a, users u
      WHERE u.user_id = ?
      AND a.author_id = u.user_id";

   $user_data = $db->prepare($query);
   if (!$user_data->execute(array($user_id))){
      die('Query failed:' . $db->errorInfo()[2]);
   }

   // Contains all of the user's pubished articles
   $article_list = $user_data->fetchAll(PDO::FETCH_OBJ);

   // Retrieves category_list, See functions.php
   $category_list = get_categories($db);

   // User clicks upload for a new article
   if(isset($_POST['new_article_submit'])){
      $title = get_post('new_title');
      $summary = get_post('new_summary');
      $category = $_POST['new_category'];
      $full_text = $_POST['new_full_text'];
      $author_id = $user_id;
      $img_url = "";

      // Loop through assoc array for categories and get category_id that corresponds to the chosen category
      for ($i=0; $i < count($category_list); $i++) {
         if($category_list[$i]['category'] == $category){
            $category_id = $category_list[$i]['category_id'];
         }
      }

      // Check if user uploaded a (img) file
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
            else{ // Move the file to the images folder
               move_uploaded_file($tmp_name, $location.$name);
               $img_url = $location.$name;
            }
         }
      }

      // Check if any of the required fields are empty
      foreach($_POST as $var=>$value) {
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
         // Refresh page (not optimal since msg will not be shown on update)
         header("Location: profile.php");
         die();
      }
   }

   // If user clicks on delete article
   if(isset($_POST['delete_article'])){
      // Get the article id
      if(isset($_POST['delete_article_id'])){
         $article_id = $_POST['delete_article_id'];
      }
      // Delete article
      $query= "DELETE FROM articles
         WHERE article_id = ?";
      $stmnt = $db->prepare($query);
      if (!$stmnt->execute(array($article_id))){
         die('Query failed:' . $db->errorInfo()[2]);
      }
      $msg = "<p class='success'>Success: Article deleted";
      // Refresh page (not optimal since msg will not be shown on update)
      header("Location: profile.php");
      die();
   }

   // If user has confirmed changes to account details
   if(isset($_POST['edit_account'])){
      // Get sanitized input values
      $edit_firstname = get_post('edit_firstname');
      $edit_lastname = get_post('edit_lastname');
      $current_password = get_post('current_password');
      $new_password = get_post('new_password');
      $confirmed_password = get_post('confirmed_password');
      $msg = "";

      // Get user
      $query = "SELECT * FROM users WHERE user_id = ?";
      $stmnt = $db->prepare($query);
      if(!$stmnt->execute(array($user_id))){
         die("Query Failed: " . $db->errorInfo()[2]);
      }
      $user = $stmnt->fetch(PDO::FETCH_OBJ);

      // Check that all fields have been filled in
      foreach($_POST as $var=>$value) {
         if(empty($_POST[$var])) {
            $msg .= "<p class='error'>All Fields are required</p>";
         break;
         }
      }

      // Check input for current password up against the database
      if(!password_verify($current_password, $user->password)){
         $msg .= "<p class='error'>Your current password input is incorrect</p>";
      }

      // Check new passwords length and structure
      $msg .= validate_password($new_password);

      // Check that the new password and confirmed new password match
      if($new_password != $confirmed_password){
         $msg .= "<p class='error'>New password and confirmed password do not match</p>";
      }

      // Hash the new password before inserting into database
      $new_password = password_hash($new_password, PASSWORD_DEFAULT);

      // If everything is ok, update the user information stored in users table in db
      if($msg==""){
         $user = array($user_id, $edit_firstname, $edit_lastname, $new_password, $user_id);
         $sql = "UPDATE users SET user_id=?, firstname=?, lastname=?, password=? WHERE user_id=?";
         $query = $db->prepare($sql);
         $query->execute($user);
         // Update session parameters
         $_SESSION['firstname'] = $edit_firstname;
         $_SESSION['lastname'] = $edit_lastname;
         $msg .= "<p class='success'>Success: User details updated.</p>";
         // Refresh page (not optimal since msg will not be shown on update)
         header("Location: profile.php");
         die();
      }
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
            <div class="col-md-6">
               <!-- ADD NEW ARTICLE -->
               <div class="new_article">
                  <!-- Adding new article -->
   					<h3>Add New Article</h3>
                  <p><?php if (isset($_POST['new_article_submit'])){echo $msg;}?></p>
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

               <!-- EDIT ACCOUNT -->
               <div class="edit_account">
                  <!-- Change Password -->
                  <h3>Edit Account Details</h3>
                  <p>Edit your account details by changing the fields below:</p>
                  <p><?php if (isset($_POST['edit_account'])){echo $msg;}?></p>
                  <form action="profile.php" method="post">
                     <label for="edit_firstname">First name:
                        <input type="text" name="edit_firstname" placeholder="First name" value="<?php if(isset($_POST['edit_firstname'])){echo $_POST['edit_firstname'];}else{echo $firstname;}?>">
                     </label>
                     <label for="edit_lastname">Last name:
                        <input type="text" name="edit_lastname" placeholder="Last name" value="<?php if(isset($_POST['edit_lastname'])){echo $_POST['edit_lastname'];}else{echo $lastname;}?>">
                     </label>
                     <label for="current_password">Current Password:
                        <input type="password" name="current_password">
                     </label>
                     <label for="new_password">New Password:
                        <input type="password" name="new_password">
                     </label>
                     <label for="confirmed_password">Confirm New Password:
                        <input type="password" name="confirmed_password">
                     </label>
                     <input type="submit" name="edit_account" value="Confirm">
                  </form>
               </div> <!-- End of edit_account -->
            </div> <!-- End of col-md-6 -->

            <div class="col-md-6">
               <!-- ARTICLE MANAGEMENT -->
               <div class="article_management">
   					<h3>Your articles</h3>
                  <p><?php if (isset($_POST['delete'])){echo $msg;}?></p>
   					<div class='article_list'>

   					<?php
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
                           <form class="delete_article" action="profile.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');">
                              <?php $article_id = $article->article_id?>
                              <input type="hidden" name="delete_article_id" value="<?=$article_id?>">
                              <input type="submit" name="delete_article" value="Delete">
                           </form>
   							</div>
   							<hr>
   						</div> <!-- End of article_wrapper -->
   					<?php
                  }
						?>
                  </div> <!-- End of article_list -->
               </div> <!-- End of article_management -->
            </div> <!-- End of column md-6 -->

         </div> <!-- End of row -->
      </div> <!-- End of container -->
	</body>
</html>
