<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

$EID = $_GET['EID'];

include 'sqlconnection.php';
// Create connection
$s = new sql();
$res = $s->getUserInfoByEID($EID);
if($row = $res->fetch_assoc())
{
    $Name = $row['Name'];
    $Title = $row['Title'];
    $Email = $row['Email'];

}

?>

<html>
<body>
<a href = "myPage.php">My Page</a>&nbsp;
<h1><?php echo $Name ?>'s Profile</h1>
<ul class="form">
    <li>
        Employee ID: <?php echo $EID;?>
    </li>
    <p></p>
    <li>
        Name    : <?php echo $Name;?>
    </li>
    <p></p>
    <li>
        Title   : <?php echo $Title;?>
    </li>
    <p></p>
    <li>
        Level   : <?php echo $Email;?>
    </li>
    <p></p>
</ul>
</body>
</html>