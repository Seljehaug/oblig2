<?php
// Establish connection to the database
include_once("connect.php");

// Delete database if it already exists
$query = 'DROP DATABASE IF EXISTS online_newspaper_db';
if ($db->exec($query)===false){
	die('Query failed(1):' . $db->errorInfo()[2]);
}

// Create database
$query = 'CREATE DATABASE IF NOT EXISTS online_newspaper_db';
// Runs query. Returns false if some error has happened.
// exec returns number of rows affected by the query. If query does not actually affect any rows
// this can be 0. Must therefore check for false to see if something wrong happened with the query
if ($db->exec($query)===false){
	die('Query failed(1):' . $db->errorInfo()[2]);
}

// Select the database
$query = 'USE online_newspaper_db';
if ($db->exec($query)===false){
	die('Can not select db:' . $db->errorInfo()[2]);
}

// Delete user table if it alrady exists
$query = 'DROP TABLE IF EXISTS users';
if ($db->exec($query)===false){
	die('Query failed(1):' . $db->errorInfo()[2]);
}

// Create table for users
$query = 'CREATE TABLE IF NOT EXISTS users (
	user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	firstname VARCHAR(100),
	lastname VARCHAR(100),
	username VARCHAR(100),
	password VARCHAR(255))';
if ($db->exec($query)===false){
	die('Query failed(2):' . $db->errorInfo()[2]);
}

// Delete categories table if it already exists
$query = 'DROP TABLE IF EXISTS categories';
if ($db->exec($query)===false){
	die('Query failed(1):' . $db->errorInfo()[2]);
}

// Create table for categories
$query = 'CREATE TABLE IF NOT EXISTS categories (
	category_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	category VARCHAR(50))';
if ($db->exec($query)===false){
	die('Query failed(3):' . $db->errorInfo()[2]);
}

// Delete articles table if it already exists
$query = 'DROP TABLE IF EXISTS articles';
if ($db->exec($query)===false){
	die('Query failed(1):' . $db->errorInfo()[2]);
}

// Create table for articles
$query = 'CREATE TABLE IF NOT EXISTS articles (
	article_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	author_id INT NOT NULL,
	title VARCHAR(100),
	category_id INT NOT NULL,
	published TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	summary TEXT,
	full_text TEXT,
	img_url VARCHAR(100),
	FOREIGN KEY (author_id) REFERENCES users (user_id),
	FOREIGN KEY (category_id) REFERENCES categories (category_id))';
if ($db->exec($query)===false){
	die('Query failed(3):' . $db->errorInfo()[2]);
}

// Delete table for admins if it already exists
$query = 'DROP TABLE IF EXISTS admins';
if ($db->exec($query)===false){
	die('Query failed(1):' . $db->errorInfo()[2]);
}

// Create table for admins
$query = 'CREATE TABLE IF NOT EXISTS admins (
	admin_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	FOREIGN KEY (admin_id) REFERENCES users (user_id))';
if ($db->exec($query)===false){
	die('Query failed(4):' . $db->errorInfo()[2]);
}

// Array containing user data (firstname and lastname)
// All of these users have the same password: 'Password123'
$password = password_hash('Password123', PASSWORD_DEFAULT);
$users_array = array(
	array("Gunnar", "Grefsen", "gunnar_grefsen", $password),
	array("Ole", "Kristiansen", "ole_kristiansen", $password),
	array("Bjarne", "Bakken", "bjarne_bakken", $password),
	array("Helene", "Svendsen", "helene_svendsen", $password)
);

// Inserting user data, user_id is automatically added because of AUTO_INCREMENT
$sql = "INSERT INTO users (firstname, lastname, username, password) values (?,?,?,?)";
$query = $db->prepare($sql);

foreach($users_array as $user)
{
	$query->execute($user);
}

// Array containing categories
$categories_array = array("Entertainment", "Sports", "Politics");

// Inserting categories data, category_id is automatically added because of AUTO_INCREMENT
$sql = "INSERT INTO categories (category) values (?)";
$query = $db->prepare($sql);

for ($i=0; $i < count($categories_array); $i++) {
	$query->execute(array($categories_array[$i]));
}

// Array containing article data (author_id, title, category_id, published, summary, full_text, img_url),
// published is originally added automatically because of DEFAULT CURRENT TIMESTAMP in the table creation query,
// but I added some specific dates because of the sorting of articles later on.
// article_id is added automatically because of AUTO_INCREMENT
$articles_array = array(
	array(1, "Ian McKellen Explains Why He Passed on Playing Albus Dumbledore", 1, date('2017-04-14 17:30:44'), "Ian McKellen is widely known as Gandalf in The Lord of the Rings series, but he could have played another wise, magical mentor – Albus Dumbledore of Harry Potter fame.",
	"<p>The history of Hollywood is littered with projects that never materialized and roles that were originally meant for other actors. As such, it can be fun to think back about how different things might have been if a good role had a better actor or a
	powerful performer didn’t bring their weight to a weaker character. Sometimes, remnants survive and allow us a glimpse into these alternate realities, but for the most part we just have to make due with anecdotes and stories from the actors and crew who
	have insight into the decisions.</p>
	<p>As a massive franchise, the Harry Potter series had a lot of roles to fill. And while its clout eventually meant that selling prestigious performers on a kids movie about magic was an easy feat, that was always the case early on. Even then,
	some actors will have their hearts set on one role and have no interest in the part they get offered. We recently learned that Jason Isaacs originally read for Gilderoy Lockhart, before being asked to try out for Lucius Malfoy. Not wanting to
	play another villain, he almost turned down the role before every parent he knew called to guilt him into doing it so their kids could visit the set. Meanwhile, it was bad blood that prevented McKellen from coming on board.</p>
	<p>Variety got ahold of an interview the actor recently did where he opened up about how he almost played Dumbledore before ultimately passing on the role. As it happens, the original Dumbledore from the first two films, Richard Harris, made some
	disparaging remarks about McKellen and some of his peers shortly before his death. When asked to consider following in his footsteps, McKellen felt it a bit uncouth.</p>
	<blockquote>“When they called me up and said would I be interested in being in the ‘Harry Potter’ films, they didn’t say in what part. I worked out what they were thinking,
	and I couldn’t … I couldn’t take over the part from an actor who I’d known didn’t approve of me.”</blockquote>
	<p>Eventually, Michael Gambon took over the role for the remainder of the character’s life in the series. Though in McKellen’s mind, it’s almost as if he still took the part.</p>", "images/gandalf.jpg"),

	array(2, "Steven Gerrard officially announces retirement from football", 2, date('2017-04-12 18:24:20'), "Former Liverpool and England captain Steven Gerrard has officially announced his retirement from professional football.",
	"<p>Gerrard was Liverpool's longest-ever serving captain and made 710 appearances for his boyhood club, inspiring them to to their famous Champions League final victory over AC Milan in 2005 while also helping the
	Reds win the FA Cup twice (2001, 2006), the League Cup on three occasions (2001, 2003, 2012) and the UEFA Cup once (2001) during an 18-year association with the club.</p> <p>At international level, Gerrard won 114 caps
	for England and is his country's fourth most capped player of all time behind Peter Shilton, Wayne Rooney and David Beckham. Over a 14-year period, he played in six major tournaments and captained England at the
	2010 and 2014 World Cups as well as the 2012 European Championships. After moving to LA Galaxy in 2015, the 36-year-old has now decided to call time on his career following their defeat in the MLS play-offs
	earlier this month, losing on penalties to Colorado Rapids. Gerrard will now take time to consider his options before deciding on his next career move, having admitted this week that he spoke to MK Dons about
	their vacant managerial position, but felt the role had come too soon for him.</p>", "images/steven_gerrard.jpg"),

	array(3, "President Trump To Move White House to Las Vegas", 3, date('2017-04-13 17:11:42'), "Donald Trump has made plans to officially move the White House from Washington, D.C. to Las Vegas, where he owns property and says that the taxes are \"much more manageable.\"",
	"<p>\"Moving the White House to Las Vegas will save tax payers an estimated $20 million a year, as the land values are much lower in the desert,\" said Trump. \"I am working with the best planners, the best men out there, to move the White House safely and securely.\"</p>
	<p>There is no word on whether Las Vegas will become our nation’s capital after the White House is relocated, but Trump did say that he wouldn’t personally have any problem with that.</p>
	<p>\"There’s a lot of money in Las Vegas, and a lot of beautiful women. We’d be lucky to have Las Vegas be our nation’s capital. It’s a beautiful, fun, fast-paced city, and everyone who goes there loves it. I love it. I own plenty of property there. It’s great. Plus, what
	happens there stays there, so we could get away with a lot more there than we could here in D.C.\"</p>", "images/white_house.jpeg"),

	array(2, "Atlanta Falcons Say Patriots Cheated Their Way To Super Bowl Victory", 2, date('2017-04-13 19:03:21'), "A representative for the Atlanta Falcons says that the team has made an official complaint with the NFL, stating that the New England Patriots cheated during the second half of the Super Bowl, causing the Falcons to lose.",
	"<p>In official documents signed by Atlanta Falcons owner Arthur Blank and endorsed by head coach Dan Quinn, the team alleges that the New England Patriots cheated by having, \"huge, over-inflated balls.\"</p>
	<p>\"During the first half of the game, the Patriots clearly were using their normal balls. Hell, they might have been using slightly under-inflated balls, honestly,\" said coach Dan Quinn.
	\"I don’t know exactly what happened after the 3rd quarter, but when they came back out on the field, that team was definitely playing with an entirely new set of balls – and their balls were huge, and way larger than before.\"</p>
	<p>The NFL is not taking the accusation lightly, as the Patriots have known to play with their balls on previous occasions, with team quarterback and GOAT Tom Brady even being suspended for several games for knowingly playing with deflated balls.</p>
	<p>\"We are looking at the Patriots balls very closely, as we cannot and will not take any accusation lightly of the Patriots playing with either small or large balls,\" said NFL Commissioner Roger Goodell.
	\"I have personally taken up the task of looking at Tom Brady’s balls, and will report my findings at a later conference.\"</p>
	<p>In the mean time, football fans across New England are overjoyed at their team bringing home their 5th Super Bowl win. \"It’s a great time to be alive,\" said Patriots Super Fan Mark Chilsom. \"I don’t care a lick about balls, to be honest.
	That was the greatest game I’ve ever seen played, with a record-setting comeback. If it was because Tom Brady and the team came out to play with huge balls in the 4th, well the so be it.\"</p>", "images/superbowl.jpg"),

	array(4, "Man Killed After Mistakenly Thinking Red Bull Energy Drink Would Give Him Literal Wings", 1, date('2017-04-10 12:30:00'), "A 23-year-old man, Jacob Andrews, was killed yesterday after he jumped out of the window of his 6th floor apartment in Carlson,
	Idaho. According to friends, Andrews had drank an entire case of the energy drink Red Bull, and mistakenly thought that the beverage would give him actual, literal wings.",
	"<p>\"We’d been drinking vodka red bulls for like, at least 5 or 6 hours,\" said Andrews’ friend, Miles Teller. \"After awhile, Jacob began talking about how he could fly, the commercials promised him wings, that he could jump out the
	window and he’d be okay. We tried to explain that it was just a commercial, they weren’t being literal. He was always a stupid drunk.\"</p>
	<p>Andrews apparently downed one last can of the drink, and leaped through the window. \"Funny thing is, Jacob didn’t even open the window – he smashed right through the glass, too\" said Police Chief Marcus Wiggum. \"Even if the 6 story
	drop didn’t kill him, he was pretty messed up from smashing through the giant, double-paned glass.\"</p>", "images/redbull.jpg")
);

// Inserting article data, article_id is automatically added because of AUTO_INCREMENT
$sql = "INSERT INTO articles (author_id, title, category_id, published, summary, full_text, img_url) values (?,?,?,?,?,?,?)";
$query = $db->prepare($sql);

foreach($articles_array as $article)
{
	$query->execute($article);
}

// Array containing admins. Containing admin_id referencing user_id in user
$admins_array = array(
	array(1),
	array(2)
);
$sql = "INSERT INTO admins (admin_id) values (?)";
$query = $db->prepare($sql);

foreach ($admins_array as $admin) {
	$query->execute($admin);
}
?>
