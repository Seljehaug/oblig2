<?php
/* PROGRAMMING FOR WEB II
 LAB 7 / TASK 2 ANSWER */

// connection data
// typically should go
// to a separete file
$db_host = 'localhost';
$db_database = '';
$db_user = 'root';
$db_pass = '';

try{
  $db = new PDO("mysql:host=$db_host;dbname=$db_database;charset=utf8",
                  $db_user, $db_pass);

} catch(PDOException  $e) {
  die ("Error: ".$e->getMessage());
}

// create the database
$query = 'CREATE DATABASE IF NOT EXISTS online_newspaper_db';
if ($db->exec($query)===false)
  die('Query failed:' . $db->errorInfo()[2]);

// // create the user and grant creditentials
// $query = "GRANT ALL ON imt3851_db.* TO 'imt3851'@'localhost' IDENTIFIED BY 'imt3851'";
// if ($db->exec($query)===false)
//   die('Query failed:' . $db->errorInfo()[2]);

// select the db
if ($db->exec("USE online_newspaper_db")===false)
  die('Can not select db:' . $db->errorInfo()[2]);

// create the books table
$query = 'CREATE TABLE IF NOT EXISTS books (
	isbn INT PRIMARY KEY,
  	title VARCHAR(100),
  	publisher VARCHAR(50),
  	pages TINYINT(4),
 	abstract TEXT(1000))';
if ($db->exec($query)===false)
  die('Query failed:' . $db->errorInfo()[2]);

// // create the customers table
// $query = 'CREATE TABLE IF NOT EXISTS customers (
// 	personal_id INT PRIMARY KEY,
//   	name VARCHAR(30),
//   	surname VARCHAR(50),
//   	address VARCHAR(100))';
// if ($db->exec($query)===false)
//   die('Query failed:' . $db->errorInfo()[2]);
//
// // create the orders table
// $query = 'CREATE TABLE IF NOT EXISTS orders (
// 	order_id INT AUTO_INCREMENT PRIMARY KEY,
//   	personal_id INT,
//   	isbn INT,
//   	quantity SMALLINT)';
// if ($db->exec($query)===false)
//   die('Query failed:' . $db->errorInfo()[2]);

echo "Database and tables are succesfully created!";

// // insert into books table
// $query = "INSERT INTO books(isbn, title, publisher, pages, abstract) VALUES('1234', 'First book', 'Pub A', '300', 'This is the first book!')";
// if ($db->exec($query)===false)
//   die('Query failed:' . $db->errorInfo()[2]);
//
// // insert into books table
// $query = "INSERT INTO books(isbn, title, publisher, pages, abstract) VALUES('1235', 'Second book', 'Pub B', '400', 'This is the second book!')";
// if ($db->exec($query)===false)
//   die('Query failed:' . $db->errorInfo()[2]);
//
// // insert into books table
// $query = "INSERT INTO books(isbn, title, publisher, pages, abstract) VALUES('1236', 'Third book', 'Pub C', '300', 'This is the third book!')";
// if ($db->exec($query)===false)
//   die('Query failed:' . $db->errorInfo()[2]);

// close the connection
$db = null;
?>
