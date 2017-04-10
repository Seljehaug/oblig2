<?php
/* PROGRAMMING FOR WEB II
 LAB 8 / TASK 2 ANSWER */

session_start();
// check if user logged in and make sure his session is not compromised
if (!isset($_SESSION['isloggedin']) OR $_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] OR $_SESSION['ua'] != $_SERVER['HTTP_USER_AGENT'])
	header('Location: login.php');

// set the cookie
if (isset($_POST['set']))
	setcookie('color', $_POST['color'], time() + (86400 * 7));

// log out
if (isset($_POST['logout'])) {
	// Unset all of the session variables.
	$_SESSION = array();
	// delete the session cookie
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	}
	// Finally, destroy the session.
	session_destroy();
	// direct user to login
	header('Location: login.php');
}
// get color preference from cookie
$color = isset($_COOKIE['color']) ? $_COOKIE['color'] : "#FFFFFF";
?>
<html>
	<head>
		<title>Profile page</title>
	</head>
	<body bgcolor="<?php echo $color; ?>">
		<p>
			Hi <?php echo $_SESSION['name'] . " " . $_SESSION['surname'] ?>!
			You are logged in as <?php echo $_SESSION['username'] ?>.
		</p>
		<p>
			What color do you prefer for the page?
			<form method="post" action="profile.php">
				<input type="color" name="color">
				<input type="submit" name="set" value="Set">
			</form>
		</p>
		<p>
			Do you want to log out?
			<form method="post" action="profile.php">
				<input type="submit" name="logout" value="Log out">
			</form>
		</p>
	</body>
</html>
