<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

include 'sqlconnection.php';

$EID = $_SESSION["username"];
$PID = trim($_POST["PID"]);
$Name = trim($_POST["Name"]);
$Description = trim($_POST["Description"]);
$ManagerID = $EID;
date_default_timezone_set("America/New_York");
$date = new DateTime(null);
$StartTime = $date->format("Y-m-d H:i:s");

if($PID == '')
{
    header('location:CreateProject.php?errorcode=1');
    exit;
}
if($Name == '')
{
    header('location:CreateProject.php?errorcode=2');
    exit;
}
if($Description == '')
{
    header('location:CreateProject.php?errorcode=3');
    exit;
}

// Create connection
$s = new sql();
$isExist = $s->ExistProjectID($PID);
if($isExist)
{
    header('location:CreateProject.php?errorcode=4');
    $s->closeConn();
    exit;
}
else
{
    $s->InsertProjectInfo($PID, $Name, $Description, $ManagerID, $StartTime);
    header('location:CreateProject.php?errorcode=-1&PID='.$PID);
    $s->closeConn();
    exit;
}



?>