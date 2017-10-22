<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

$EID = $_SESSION["username"];
$tPID = $_GET["PID"];
$tEID = $_GET["EID"];
$tUpdateTime = $_GET["updateTime"];
$pSearchName = $_GET["pSearchName"];

$dt = new DateTime($tUpdateTime);

$date = $dt->format('Y-m-d');
$time = $dt->format('H:i:s');

if(isset($_GET['errorcode']))
{
    $errorcode = $_GET['errorcode'];
    if($errorcode === '1')
        $errormsg = 'Please add something in comments';
    else
        $errormsg = 'Post comment successfully';
    echo "<div class=\"alert\">";
    echo "<span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>";
    echo $errormsg;
    echo "</div>";

}
?>


<form action=<?php echo "validatePostComments.php?pSearchName=".$pSearchName."&tPID=".$tPID."&tEID=".$tEID."&date=".$date."&time=".$time ?> method="post">
<ul class="form">
	<li>
		<h1>Post a comment</h1>
	</li>
    <li>
        <textarea name="myComment" cols="80" rows="10"></textarea>
    </li>
    <li>
        <input type="submit" value="Submit" />
        <input type="reset" value="Reset">
    </li>
</ul>
</form>

<?php

include 'sqlconnection.php';
// Create connection
$s = new sql();
$res = $s->getComments($tPID, $tEID, $tUpdateTime);
$res->bind_result($content, $posterID, $posterName, $postTime);
while($res->fetch())
{
    echo "Poster Name: <a href=\"employeeInfo.php?EID=$posterID\" target=\"_blank\">$posterName</a>&nbsp;&nbsp;&nbsp;";
    echo "Post Time: $postTime";
    echo "<p></p>";
    echo "<li><textarea cols=\"100\" rows=\"3\" readonly =\"readonly\">$content</textarea></li>";
    echo "<p></p>";
}
$res->close();

if(isset($_GET['postUpdate']))
{
    if($_GET['postUpdate'] == 'TRUE')
    {
        echo "<div class=\"alert\">";
        echo "<span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>";
        echo "Successfully post an update!";
        echo "</div>";
    }
}

?>

<?php
$s->closeConn();
?>