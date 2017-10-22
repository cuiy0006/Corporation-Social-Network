<?php

session_start();
if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}


$EID = $_SESSION["username"];
$managerID = $_GET['managerID'];

include 'sqlconnection.php';
// Create connection
$s = new sql();

if(isset($_GET['close']))
{
    $close = $_GET['close'];
    if($managerID == $EID)
    {
        date_default_timezone_set("America/New_York");
		$date = new DateTime(null);
		$EndTime = $date->format("Y-m-d H:i:s");
        $s->closeProject($close, $EndTime);
        $s->closeConn();
        header('location:myProjects.php');
        exit;
    }
}


if(isset($_GET['remove']) && $EID===$managerID)
{
	$remove = $_GET['remove'];
    $PID = $_GET['PID'];
	if($remove !== $managerID)
	{
		$s->RemoveTIDfromProject($remove, $PID);
        $s->closeConn();
		header('location:editProject.php?PID='.$PID.'&errorcode=-1&remove='.$remove);
        exit;
	}
}

if(isset($_GET['add']) && $EID===$managerID)
{
	$add = $_GET['add'];
    $PID = $_GET['PID'];
	if($add !== $managerID)
	{
		date_default_timezone_set("America/New_York");
		$date = new DateTime(null);
		$JoinTime = $date->format("Y-m-d H:i:s");
		$s->InsertTIDintoProject($PID, $add, $JoinTime);
        $s->closeConn();
		header('location:editProject.php?PID='.$PID.'&errorcode=-1&add='.$add);
        exit;
	}
}

$s->closeConn();
header('location:editProject.php?PID=$PID&errorcode=1&remove=$remove');
?>