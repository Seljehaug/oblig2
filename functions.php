<?php
// Funcion for sanitizing input
function get_post($var){
	$var = stripslashes($_POST[$var]);
	$var = htmlentities($var);
	$var = strip_tags($var);
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

// Retrieves the articles stored in the database
// Returns an assoc array containing all data about each article 
function get_articles($db){
	$query = "SELECT a.*, u.*
		FROM articles a, users u
		WHERE a.author_id = u.user_id";

	$stmnt = $db->prepare($query);
	if (!$stmnt->execute(array()))
		die('Query failed:' . $db->errorInfo()[2]);

	$result = $stmnt->fetchAll(PDO::FETCH_OBJ);
	$article_list = array();
	foreach ($result as $article) {
		$article_list[] = array("article_id" => $article->article_id, "author_id" => $article->author_id, "title" => $article->title,
		"category_id" => $article->category_id, "published" => $article->published, "summary" => $article->summary,
		"full_text" => $article->full_text, "img_url" => $article->img_url);
	}
	return $article_list;
}

// Retrieves the admins stored in the database
// Returns an assoc array containing id and usernaem for all admins
function get_admins($db){
	$query = "SELECT u.*, a.*
		FROM users u, admins a
		WHERE u.user_id = a.admin_id";

	$stmnt = $db->prepare($query);
	if (!$stmnt->execute(array()))
		die('Query failed:' . $db->errorInfo()[2]);

	$result = $stmnt->fetchAll(PDO::FETCH_OBJ);
	$admin_list = array();
	foreach ($result as $admin) {
		$admin_list[] = array("user_id" => $admin->user_id, "username" => $admin->username);
	}
	return $admin_list;
}

// Retrieves the categories stored in the database and returns an assoc array with the category names
function get_categories($db){
	$query = "SELECT * FROM categories";
	$stmnt = $db->prepare ($query);
	if (!$stmnt->execute (array())){
		die('Query failed:' . $db->errorInfo()[2]);
	}
	$result = $stmnt->fetchAll(PDO::FETCH_OBJ);
	$category_list = array();
	foreach ($result as $category) {
		$category_list[] = array("category_id" => $category->category_id, "category" => $category->category);
	}
	return $category_list;
}
