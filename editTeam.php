<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

$EID = $_SESSION["username"];
$TID = $_GET["TID"];

include 'sqlconnection.php';
// Create connection
$s = new sql();


$res = $s->getTeamInfoByTID($TID);
if($row = $res->fetch_assoc())
{
    $teamName = $row['Name'];
	$description = $row['Description'];
	$managerID = $row['ManagerID'];
	$startTime = $row['StartTime'];
}


if(isset($_POST['teamName']))
{
	if($EID != $managerID)
		$errorMsg = 'Only manager can edit team Info';
	else
	{
		$new_teamName = trim($_POST['teamName']);
		$new_description = trim($_POST['description']);
		if($new_teamName == '')
			$errorMsg = "Input team name is empty!";
		elseif($new_description == '')
			$errorMsg = "Please add some description!";
		else
		{
			$s->UpdateTeamInfo($TID, $new_teamName, $new_description);
			$teamName = $new_teamName;
			$description = $new_description;
			$errorMsg = "Team Info Updated Successfully!";
		}
	}
	echo "<div class=\"alert\">";
    echo "<span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>";
    echo $errorMsg;
    echo "</div>";
}



if(isset($_GET['set']) && $EID === $managerID)
{
	$set = $_GET['set'];
	if($set !== $managerID)
	{
		$s->UpdateManagerOfTeam($TID, $set);
		echo "<div class=\"alert\">";
		echo "<span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>";
		echo $set." has been promoted as manager of team";
		echo "</div>";
		$managerID = $set;
	}
}

if(isset($_GET['errorcode']))
{
	$errorcode = $_GET['errorcode'];
	if($errorcode === '1')
		$errorMsg = 'Remove Unsuccessfully!';
	elseif($errorcode === '-1')
		if(isset($_GET['remove']))
			$errorMsg = $_GET['remove']." has been removed from team!";
		elseif(isset($_GET['add']))
			$errorMsg = $_GET['add']." has been added to team!";
	echo "<div class=\"alert\">";
    echo "<span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>";
    echo $errorMsg;
    echo "</div>";
}
?>


<html>
<body>
<a href = "myTeams.php">Back</a>&nbsp;&nbsp;
<a href = "myPage.php">My Page</a>&nbsp;&nbsp;
<a href = "logout.php" style="text-decoration:none;">Logout</a>
<h1>Manage My Team</h1>
<h2>Team Info</h2>
<form action=<?php echo "editTeam.php?TID=".$TID ?> method="post">
<ul class="form">
    <li>
        Team ID: <?php echo $TID;?>
    </li>
    <li>
        Team Name: <textarea name="teamName" cols="60" rows="1"><?php echo $teamName;?></textarea>
    </li>
    <p></p>
    <li>
        <label>Description: </label>
    </li>
    <li>
        <textarea name="description" cols="80" rows="10"><?php echo $description;?></textarea>
    </li>
    <p></p>
    <li>
        Manager ID   : <?php echo $managerID;?>
    </li>
    <p></p>
    <li>
        Start Time   : <?php echo $startTime;?>
    </li>
    <p></p>
    <input type="submit" value="Submit" />
    <input type="reset" value="Reset">
</ul>
</form>
</body>
</html>


<h2>Team Members</h2>
<table class="table" border = "1">
	<tr>
		<th style="width:20%;">Employee ID</th>
		<th style="width:20%;">Employee Name</th>
		<th style="width:15%;">Title</th>
        <th style="width:25%;">Email</th>
	</tr>
    <?php

	$employeeInTeam = $s->getUserInfoByTID($TID);
	$employeeInTeam->bind_result($tEID, $Name, $Title, $Email);

    while($employeeInTeam->fetch())
    {
        echo "<tr>";
        echo "<td align=\"center\" valign=\"middle\">$tEID</td>";
        echo "<td align=\"center\" valign=\"middle\">$Name</td>";
        echo "<td align=\"center\" valign=\"middle\">$Title</td>";
        echo "<td align=\"center\" valign=\"middle\">$Email</td>";
		if($managerID === $EID && $tEID !== $EID)
		{
			echo "<td align=\"center\" valign=\"middle\"><a href=\"validateEditTeam.php?TID=$TID&remove=$tEID&managerID=$managerID\">Remove</a></td>";
			echo "<td align=\"center\" valign=\"middle\"><a href=\"editTeam.php?TID=$TID&set=$tEID\">Set as Manager</a></td>";
		}
        echo "</tr>";
    }
	$employeeInTeam->close();
    ?>
</table>



<?php
if($managerID === $EID)
{
	echo "<h2>Candidates</h2>";
	$searchName = "";
	if(isset($_POST['searchName']))
	{
		$searchName = $_POST['searchName'];
	}
	echo "<form action=\"editTeam.php?TID=$TID\" method=\"post\">";
	echo "<label>Candidates Name:</label> &nbsp;";
	echo "<input type=\"text\" name=\"searchName\" value=".$searchName.">";
	echo "<input type=\"submit\" value=\"Search\" />";
	echo "<input type=\"reset\" value=\"Reset\">";
	echo "</form>";
	?>

	<table class="table" border = "1">
		<tr>
			<th style="width:20%;">Employee ID</th>
			<th style="width:20%;">Employee Name</th>
			<th style="width:15%;">Title</th>
			<th style="width:25%;">Email</th>
			
		</tr>
		<?php

		if($managerID === $EID)
		{
			$employeeNotInTeam = $s ->getUserInfoByNotInTeam($TID, $searchName);
			$employeeNotInTeam->bind_result($tEID, $Name, $Title, $Email);

			while($employeeNotInTeam->fetch())
			{
				echo "<tr>";
				echo "<td align=\"center\" valign=\"middle\">$tEID</td>";
				echo "<td align=\"center\" valign=\"middle\">$Name</td>";
				echo "<td align=\"center\" valign=\"middle\">$Title</td>";
				echo "<td align=\"center\" valign=\"middle\">$Email</td>";
				if($tEID !== $EID)
					echo "<td align=\"center\" valign=\"middle\"><a href=\"validateEditTeam.php?TID=$TID&add=$tEID&managerID=$managerID\">Add</a></td>";
				echo "</tr>";
			}
			$employeeNotInTeam->close();
		}
	}
    ?>
</table>


<h2>Enrolled Projects</h2>
<?php
$searchName2 = "";
if(isset($_POST['searchName2']))
{
    $searchName2 = $_POST['searchName2'];
}
echo "<form action=\"editTeam.php?TID=$TID\" method=\"post\">";
echo "<label>Project Name:</label> &nbsp;";
echo "<input type=\"text\" name=\"searchName2\" value=".$searchName2.">";
echo "<input type=\"submit\" value=\"Search\" />";
echo "<input type=\"reset\" value=\"Reset\">";
echo "</form>";
?>
<table class="table" border = "1">
	<tr>
		<th style="width:15%;">Project ID</th>
		<th style="width:15%;">Project Name</th>
		<th style="width:30%;">Description</th>
        <th style="width:10%;">Manager Name</th>
        <th style="width:10%;">Start Time</th>
		<th style="width:10%;">End Time</th>  
	</tr>
    <?php
    $res = $s->getProjectInfoByTID($TID, $searchName2);
    $res->bind_result($PID, $Name, $Description, $ManagerName, $ManagerID, $StartTime, $EndTime);
    while($row = $res->fetch())
    {
        echo "<tr>";
        echo "<td align=\"center\" valign=\"middle\"><a href=\"editProject.php?PID=$PID&ManageTeam=$TID\">$PID</a></td>";
        echo "<td align=\"center\" valign=\"middle\">$Name</td>";
        echo "<td align=\"center\" valign=\"middle\">$Description</td>";
        echo "<td align=\"center\" valign=\"middle\"><a href=\"employeeInfo.php?EID=$ManagerID\" target=\"_blank\">$ManagerName</a></td>";
        echo "<td align=\"center\" valign=\"middle\">$StartTime</td>";
        echo "<td align=\"center\" valign=\"middle\">$EndTime</td>";
        echo "</tr>";
    }
	$res->close();
    ?>
</table>
<?php

$s->closeConn();

?>