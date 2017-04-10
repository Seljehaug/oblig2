<?php
// Funcion for sanitizing input
function get_post($var){
	$var = stripslashes($_POST[$var]);
	$var = htmlentities($var);
	$var = strip_tags($var);
	// Do not need this because of PDO prepared statement
	// $var = $conn->real_escape_string($var);
	return $var;
}

// Validation of username. Checks that username is between 5 and 15 characters,
// Also checks that characters only include letters, numbers and '_'
// Returns appropriate message, empty if everything is fine.
function validate_username($username) {
	$msg = "";
	if (!preg_match("/^.{5,15}$/", $username)){
		$msg .= "<p class='error'>User name should be between 5-15 characters.</p>";
	}
	if (preg_match("/[^\w]/", $username)){
		$msg .= "<p class='error'>Username should only include letters, numbers and '_'.</p>";
	}
	return $msg;
}

// Validation of password. Checks that password contains a minimum of 5 characters.
// Also checks that password contains at least one uppercase and lowercase letter
// Returns appropriate message, empty if fine.
function validate_password($password) {
	$msg = "";
	if (!preg_match("/^.{5,}$/", $password))
		$msg .= "<p class='error'>Password should be minimum of 5 characters.</p>";
	if (!preg_match("/[a-z]/", $password))
		$msg .= "<p class='error'>Password should include at least one lowercase letter.</p>";
	if (!preg_match("/[A-Z]/", $password))
		$msg .= "<p class='error'>Password should include at least one uppercase letter.</p>";
	return $msg;
}
