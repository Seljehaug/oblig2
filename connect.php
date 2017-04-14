<?php
// connection data
$db_host = 'localhost';
$db_database = '';
$db_user = 'root';
$db_pass = '';

// Create connection
try {
	$db = new PDO("mysql:host=$db_host;dbname=$db_database;charset=utf8",$db_user, $db_pass);
} catch(PDOException  $e) {
	die ("Could not connect: ".$e->getMessage());
}

// Select the database
$query = 'USE online_newspaper_db';
if ($db->exec($query)===false){
	die('Can not select db:' . $db->errorInfo()[2]);
}
