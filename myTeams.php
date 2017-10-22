<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

$EID = $_SESSION["username"];
$level = $_SESSION["level"];
$canCreateTeam = FALSE;

include 'sqlconnection.php';
// Create connection
$s = new sql();
$res = $s->getPriviledge();
while($row = $res->fetch_assoc())
{
    if($row["Function"] == "create team")
    {
        if($level <= $row["Level"])
            $canCreateTeam = TRUE;
    }
}
?>

<html>
<body>
<p>
<a href = "myPage.php">My Page</a>&nbsp;
&nbsp;
<a href = "logout.php" style="text-decoration:none;">Logout</a>
</p>
</body>
</html>

<h1>My Teams</h1>
<?php
$TName = "";
if(isset($_POST['TName']))
{
    $TName = $_POST['TName'];
}
echo "<form action=\"myTeams.php\" method=\"post\">";
echo "<label>Team Name</label> &nbsp;";
echo "<input type=\"text\" name=\"TName\" value=".$TName.">";
echo "<input type=\"submit\" value=\"Search\" />";
echo "<input type=\"reset\" value=\"Reset\">";
echo "</form>";
?>

<table class="table" border = "1">
	<tr>
		<th style="width:15%;">Team ID</th>
		<th style="width:15%;">Team Name</th>
		<th style="width:40%;">Description</th>
        <th style="width:10%;">ManagerName</th>
		<th style="width:10%;">Start Time</th>    
	</tr>
    <?php
    $res = $s->getTeamInfoByEID($EID, $TName);
    while($row = $res->fetch_assoc())
    {
        $TID = $row["TID"];
        $Name = $row["Name"];
        $Description = $row["Description"];
        $ManagerName = $row["ManagerName"];
        $ManagerID = $row["ManagerID"];
        $StartTime = $row["StartTime"];

        echo "<tr>";
        echo "<td align=\"center\" valign=\"middle\"><a href=\"editTeam.php?TID=$TID\">$TID</a></td>";
        echo "<td align=\"center\" valign=\"middle\">$Name</td>";
        echo "<td align=\"center\" valign=\"middle\">$Description</td>";
        echo "<td align=\"center\" valign=\"middle\"><a href=\"employeeInfo.php?EID=$ManagerID\" target=\"_blank\">$ManagerName</a></td>";
        echo "<td align=\"center\" valign=\"middle\">$StartTime</td>";
        echo "</tr>";
    }
    ?>
</table>

<?php
if($canCreateTeam)
{
    echo "<form action=\"CreateTeam.php\" method =\"post\">";
    echo "<input type=\"submit\" value=\"Create New\">";
    echo "</form>";
}

$s->closeConn();
?>