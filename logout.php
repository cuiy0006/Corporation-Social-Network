<?php
session_start();
if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Index</a>";
	exit;
}
session_destroy();
header("location:index.php?logout=true");
?>