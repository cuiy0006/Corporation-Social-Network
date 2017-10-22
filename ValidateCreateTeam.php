<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

include 'sqlconnection.php';

$EID = $_SESSION["username"];
$TID = trim($_POST["TID"]);
$Name = trim($_POST["Name"]);
$Description = trim($_POST["Description"]);
$ManagerID = $EID;
date_default_timezone_set("America/New_York");
$date = new DateTime(null);
$StartTime = $date->format("Y-m-d H:i:s");

if($TID == '')
{
    header('location:CreateTeam.php?errorcode=1');
    exit;
}
if($Name == '')
{
    header('location:CreateTeam.php?errorcode=2');
    exit;
}
if($Description == '')
{
    header('location:CreateTeam.php?errorcode=3');
    exit;
}

// Create connection
$s = new sql();
$isExist = $s->ExistTeamID($TID);
if($isExist)
{
    header('location:CreateTeam.php?errorcode=4');
    $s->closeConn();
    exit;
}
else
{
    $s->InsertTeamInfo($TID, $Name, $Description, $ManagerID, $StartTime);
    header('location:CreateTeam.php?errorcode=-1&TID='.$TID);
    $s->closeConn();
    exit;
}



?>