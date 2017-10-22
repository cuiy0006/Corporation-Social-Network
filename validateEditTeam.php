<?php

session_start();
if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}


$EID = $_SESSION["username"];
$TID = $_GET["TID"];
$managerID = $_GET['managerID'];

include 'sqlconnection.php';
// Create connection
$s = new sql();

if(isset($_GET['remove']) && $EID===$managerID)
{
	$remove = $_GET['remove'];
	if($remove !== $managerID)
	{
		$s->RemoveEIDfromTeam($remove, $TID);
        $s->closeConn();
		header('location:editTeam.php?TID='.$TID.'&errorcode=-1&remove='.$remove);
        exit;
	}
}

if(isset($_GET['add']) && $EID===$managerID)
{
	$add = $_GET['add'];
	if($add !== $managerID)
	{
		date_default_timezone_set("America/New_York");
		$date = new DateTime(null);
		$JoinTime = $date->format("Y-m-d H:i:s");
		$s->InsertEIDintoTeam($TID, $add, $JoinTime);
        $s->closeConn();
		header('location:editTeam.php?TID='.$TID.'&errorcode=-1&add='.$add);
        exit;
	}
}

$s->closeConn();
header('location:editTeam.php?TID=$TID&errorcode=1&remove=$remove');
?>