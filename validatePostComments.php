<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

$EID = $_SESSION["username"];
$tPID = $_GET["tPID"];
$tEID = $_GET["tEID"];
$date = $_GET['date'];
$time = $_GET['time'];
$tUpdateTime = $date ." ".$time;
$pSearchName = $_GET["pSearchName"];
$myComment = $_POST["myComment"];

if($myComment == '')
{
    header('location:comments.php?PID='.$tPID.'&EID='.$tEID.'&pSearchName='.$pSearchName.'&errorcode=1&updateTime='.$tUpdateTime);
    exit;
}
else
{
    date_default_timezone_set("America/New_York");
    $date = new DateTime(null);
    $posterTime = $date->format("Y-m-d H:i:s");

    include 'sqlconnection.php';
    // Create connection
    $s = new sql();
    $s->insertComment($tPID, $tEID, $tUpdateTime, $myComment, $posterTime, $EID);
    header('location:comments.php?PID='.$tPID.'&EID='.$tEID.'&pSearchName='.$pSearchName.'&errorcode=-1&updateTime='.$tUpdateTime);
    exit;
}

?>

<?php
$s->closeConn();
?>