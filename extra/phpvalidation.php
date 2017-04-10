<?php
$username = $password = "";
$fail = "";

// check if form is submitted
if (isset($_POST["submit"])) {
	$fail = validate_username($_POST["username"]);
	$fail .= validate_password($_POST["password"]);
}

// validate username
function validate_username($username) {
	$err = "";
	if (!preg_match("/^.{3,8}$/", $username))
		$err .= "User name should be between 3-8 characters.<br>";
	if (preg_match("/[^\w]/", $username))
		$err .= "Username should only include letters, numbers and '_'.<br>";
	return $err;
}

// validate password
function validate_password($password) {
	$err = "";
	if (!preg_match("/^.{5,}$/", $password))
		$err .= 'Password should be minimum of 5 characters.<br>';
	if (!preg_match("/[a-z]/", $password))
		$err = 'Password should include at least one lowercase letter.<br>';
	if (!preg_match("/[A-Z]/", $password))
		$err .= 'Password should include at least one uppercase letter.<br>';
	return $err;
}
?>
<html>
	<head>
		<script>
			// validate each user input
			// function validate(form) {
			// 	fail = validate_username(form.username.value);
			// 	fail += validate_password(form.password.value);
			//
			// 	if (fail == '') {
			// 		return true;
			// 	} else {
			// 		alert(fail);
			// 		return false;
			// 	}
			// }
			//
			// // validate username
			// function validate_username(username) {
			// 	err = "";
			// 	if (!/^.{3,8}$/.test(username))
			// 		err += "User name should be between 3-8 characters.\n";
			// 	if (/[^\w]/.test(username))
			// 		err += "Username should only include letters, numbers and '_'.<br>";
			//
			// 	return err;
			// }
			//
			// // validate password
			// function validate_password(password) {
			// 	err = "";
			// 	if (!/^.{5,}$/.test(password))
			// 		err += 'Password should be minimum of 5 characters.\n';
			// 	if (!/[a-z]/.test(password))
			// 		err += 'Password should include at least one lowercase letter.\n';
			// 	if (!/[A-Z]/.test(password))
			// 		err += 'Password should include at least one uppercase letter.\n';
			//
			// 	return err;
			// }
		</script>
	</head>
	<body>
		<?php
		echo $fail;
		?>
		<form method="post" action="phpvalidation.php" onsubmit="return validate(this);">
			Username:
			<input type="text" name="username">
			Password:
			<input type="text" name="password">
			<input type="submit" name="submit" value="Submit">
		</form>
	</body>
</html>
