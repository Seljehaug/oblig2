<!DOCTYPE html>
<!-- Web Programming 2, Assignment 2
Name: Christoffer Seljehaug [470600]
File: login.php -->
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Online Newspaper - Login</title>
      <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
      <link rel='stylesheet' type='text/css' href='css/styles.css'>
   </head>

	<body id="login">
      <div class="container">
         <div class="row">
            <div class="col-md-6 col-md-offset-3">
               <a href="index.php"><h1 class="logo">Online Newspaper</h1></a>

               <?php
               session_start();
               // Establish connection
               require_once("connect.php");
               require_once("functions.php");

               echo "<pre>";
               print_r($_SESSION);
               echo "</pre>";

               // If user is redirected from register page, show confirmation message
               if(isset($_GET['reg'])){
               	if( $_GET['reg'] == 'success') {
               		echo "<p>Thank you registering, you can now log in.</p>";
               	}
               }
               // If not a first-time user:
               else {
                  echo "<p>Enter your username and password in order to log in and post your own articles.</p>";
               }

               echo "<a href='index.php'>GO TO INDEX</a>";

               // User is not logged in and clicks the login button
               if (!isset($_SESSION['isloggedin']) && isset($_POST['login'])) {
                  $username = get_post('username');
                  $password = get_post('password');

                  if(empty($username) || empty($password)){
                     echo "<p class='error'>Please enter your username and password</p>";
                  }
                  else {
                     // Check database for users matching input username
                     $query = "SELECT * FROM users WHERE username=?";
                     $stmnt = $db->prepare ($query);
                     if (!$stmnt->execute(array($username)))
                        die('Query failed:' . $db->errorInfo()[2]);

                     // All usernames are supposed to be unique. Can therefore use fetch()
                     // to get the last (the only) row matching the query
                     $result = $stmnt->fetch(PDO::FETCH_OBJ);

                     // If there is in fact a user with that username in the database
                     if (count($result) != 0) {
                        // Check that input password matches the users password stored in the database
                        if(password_verify($password, $result->password)){
                           // Set session parameters
                           $_SESSION['firstname'] = $result->firstname;
                           $_SESSION['lastname'] = $result->lastname;
                           $_SESSION['username'] = $username;
                           $_SESSION['user_id'] = $result->user_id;
                           $_SESSION['isloggedin'] = true;

                           // Retrieve admin list, see functions.php
                           $admin_list = get_admins($db);
                           // If user is an admin, set Session variable for admin
                           foreach ($admin_list as $admin) {
                              if($admin['user_id'] == $result->user_id){
                                 $_SESSION['admin'] = true;
                              }
                           }
                           // Redirect user to index.php
                           header("Location: index.php");
                        }
                        // Bad password
                        else {
                           echo "<p class='error'>Wrong username or password</p>";
                        }
                     }
                     // If there is no user with that username
                     else {
                        echo "<p class='error'>Wrong username or password</p>";
                     }
                  }
               }
               ?>

					<div class="loginFormWrapper">
						<form class="loginForm" action="login.php" method="POST">
							<input type="text" name="username" placeholder="Username" value="<?php if(isset($_POST['username'])){ echo $_POST['username']; } ?>">
							<input type="password" name="password" placeholder="Password">
							<input type="submit" name="login" value="Log in">
						</form>
					</div>

				</div> <!-- End of column -->
			</div> <!-- End of row -->
		</div> <!-- End of container -->
	</body>
</html>
