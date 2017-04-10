<!DOCTYPE html>
<!-- Web Programming 2, Assignment 2
Name: Christoffer Seljehaug [470600]
File: register.php -->
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Online Newspaper - User Registration</title>
      <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
      <link rel='stylesheet' type='text/css' href='css/styles.css'>
   </head>

	<body id="register">
      <div class="container">
         <div class="row">
            <div class="col-md-6 col-md-offset-3">
               <a href="index.php"><h1 class="logo">Online Newspaper</h1></a>
					<p>Do you want to contribute to this website? By registering as a user, you will be able to post
					your own news articles!</p>

					<div class="registerFormWrapper">
						<form class="registerForm" action="register.php" method="POST">
							<input type="text" name="firstname" placeholder="First Name" value="<?php if(isset($_POST['firstname'])){echo $_POST['firstname'];}?>">
							<input type="text" name="lastname" placeholder="Last Name" value="<?php if(isset($_POST['lastname'])){echo $_POST['lastname'];}?>">
							<input type="text" name="username" placeholder="Username" value="<?php if(isset($_POST['username'])){echo $_POST['username'];}?>">
							<input type="password" name="password" placeholder="Password">
                     <input type="password" name="confirmed_password" placeholder="Confirm Password">
                     <!-- <input type="hidden" name="reg_confirmation" value="incomplete"> -->
							<input type="submit" name="submit" value="Register">
						</form>
					</div>

					<?php
					// Establish connection
					require_once("connect.php");

               require_once("functions.php");

					// Select the database
					$query = 'USE online_newspaper_db';
					if ($db->exec($query)===false){
						die('Can not select db:' . $db->errorInfo()[2]);
					}

					// When user clicks submit, retrieve sanitized input fields and insert into database
					if(isset($_POST['submit'])){
						$firstname = get_post('firstname');
						$lastname = get_post('lastname');
						$username = get_post('username');
                  $password = $_POST['password'];
                  $confirmed_password = $_POST['confirmed_password'];

                  $msg = "";

                  // Check that all fields have been filled in
                  foreach($_POST as $var=>$value) {
                     if(empty($_POST[$var])) {
                        $msg .= "<p class='error'>All Fields are required</p>";
                     break;
                     }
                  }

                  // Check username for length and characters
                  $msg .= validate_username($username);

                  // Check that username is not in database already
                  $query = "SELECT username FROM users WHERE username = ?";
                  $stmnt = $db->prepare($query);

                  if(!$stmnt->execute(array($username))){
                     die("Query Failed: " . $db->errorInfo()[2]);
                  }

                  // fetchALL returns empty array if there are zero results to fetch
                  $result = $stmnt->fetchAll();
                  if (!empty($result) ) {
                     $msg .= "<p class='error'>Username already taken</p>";
                  }

                  // Check for password length and structure
                  $msg .= validate_password($password);

                  // Check that passwords match
                  if($_POST['password'] != $_POST['confirmed_password']){
                     $msg .= "<p class='error'>Your passwords do not match</p>";
                  }


                  // CHECK THAT FIRST AND LAST NAME ONLY HAS LETTERS IN IT

                  // Display error messages
                  echo $msg;

                  // Hash/encrypt password before storing in the database later
                  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                  // $confirmed_password = password_hash($_POST['confirmed_password'], PASSWORD_DEFAULT);

                  // Make sure there are no errors, then register user
                  if($msg == ""){
                     $query = "INSERT INTO users (firstname, lastname, username, password) VALUES (?,?,?,?)";
                     $stmnt = $db->prepare($query);

                     if($stmnt->execute(array($firstname, $lastname, $username, $password)) == false){
                        echo "<p>User could not be inserted</p>";
                     }
                     else {
                        echo "<p>You have been successfully registered!</p>";

                        $query = "SELECT user_id FROM users WHERE username = ?";
                        $stmnt = $db->prepare($query);

                        if(!$stmnt->execute(array($username))){
                           die('Query failed:' . $db->errorInfo()[2]);
                        }
                        $result = $stmnt->fetch(PDO::FETCH_OBJ);
                        $user_id = $result->user_id;

                        // Send user to
                        // $_POST['reg'] = "success";
                        header("Location: login.php?id=$user_id&reg=success");
                     }
                  }

               }

					// Sanitize input
					// function get_post($var){
					// 	$var = stripslashes($_POST[$var]);
					// 	$var = htmlentities($var);
					// 	$var = strip_tags($var);
					// 	// Do not need this because of PDO prepared statement
					// 	// $var = $conn->real_escape_string($var);
					// 	return $var;
					// }

               // Validation of username. Checks that username is between 5 and 15 characters,
               // Also checks that characters only include letters, numbers and '_'
               // Returns appropriate message, empty if everything is fine.
               // function validate_username($username) {
               //    $msg = "";
               //    if (!preg_match("/^.{5,15}$/", $username)){
               //       $msg .= "<p class='error'>User name should be between 5-15 characters.</p>";
               //    }
               //    if (preg_match("/[^\w]/", $username)){
               //       $msg .= "<p class='error'>Username should only include letters, numbers and '_'.</p>";
               //    }
               //    return $msg;
               // }

               // // Validation of password. Checks that password contains a minimum of 5 characters.
               // // Also checks that password contains at least one uppercase and lowercase letter
               // // Returns appropriate message, empty if fine.
               // function validate_password($password) {
               // 	$msg = "";
               // 	if (!preg_match("/^.{5,}$/", $password))
               // 		$msg .= "<p class='error'>Password should be minimum of 5 characters.</p>";
               // 	if (!preg_match("/[a-z]/", $password))
               // 		$msg .= "<p class='error'>Password should include at least one lowercase letter.</p>";
               // 	if (!preg_match("/[A-Z]/", $password))
               // 		$msg .= "<p class='error'>Password should include at least one uppercase letter.</p>";
               // 	return $msg;
               // }
					?>

				</div> <!-- End of column -->
			</div> <!-- End of row -->
		</div> <!-- End of container -->
	</body>
</html>
