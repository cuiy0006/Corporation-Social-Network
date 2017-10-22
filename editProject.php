<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

$EID = $_SESSION["username"];
$PID = $_GET["PID"];

include 'sqlconnection.php';
// Create connection
$s = new sql();


$res = $s->getProjectInfoByPID($PID);
if($row = $res->fetch_assoc())
{
    $projectName = $row['Name'];
	$description = $row['Description'];
	$managerID = $row['ManagerID'];
	$startTime = $row['StartTime'];
    $endTime = $row['EndTime'];
}


if(isset($_POST['projectName']))
{
	if($EID != $managerID)
		$errorMsg = 'Only manager can edit team Info';
	else
	{
		$new_projectName = trim($_POST['projectName']);
		$new_description = trim($_POST['description']);
		if($new_projectName == '')
			$errorMsg = "Input team name is empty!";
		elseif($new_description == '')
			$errorMsg = "Please add some description!";
		else
		{
			$s->UpdateProjectInfo($PID, $new_projectName, $new_description);
			$projectName = $new_projectName;
			$description = $new_description;
			$errorMsg = "Project Info Updated Successfully!";
		}
	}
	echo "<div class=\"alert\">";
    echo "<span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>";
    echo $errorMsg;
    echo "</div>";
}


if(isset($_GET['errorcode']))
{
	$errorcode = $_GET['errorcode'];
	if($errorcode === '1')
		$errorMsg = 'Remove Unsuccessfully!';
	elseif($errorcode === '-1')
		if(isset($_GET['remove']))
			$errorMsg = $_GET['remove']." has been removed from project!";
		elseif(isset($_GET['add']))
			$errorMsg = $_GET['add']." has been added to project!";
	echo "<div class=\"alert\">";
    echo "<span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>";
    echo $errorMsg;
    echo "</div>";
}
?>


<html>
<body>
<?php 
if(isset($_GET['ManageTeam']))
{
    $ManageTeam = $_GET['ManageTeam'];
    echo "<a href = \"editTeam.php?TID=$ManageTeam\">Back</a>";
}
else
{
    echo "<a href = \"myProjects.php\">Back</a>";
}
?>
&nbsp;&nbsp;
<a href = "myPage.php">My Page</a>&nbsp;&nbsp;
<a href = "logout.php" style="text-decoration:none;">Logout</a>
<h1>Manage My Project</h1>
<h2>Project Info</h2>
<form action=<?php echo "editProject.php?PID=".$PID ?> method="post">
<ul class="form">
    <li>
        Project ID: <?php echo $PID;?>
    </li>
    <li>
        Project Name: <textarea name="projectName" cols="60" rows="1"><?php echo $projectName;?></textarea>
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
    <li>
        End Time   : <?php echo $endTime;?>
        <?php
            if($EID === $managerID && $endTime === null)
                echo "<a href=\"validateEditProject.php?close=$PID&managerID=$managerID\">Close Project</a>";
        ?>
    </li>
    <p></p>
    <input type="submit" value="Submit" />
    <input type="reset" value="Reset">
</ul>
</form>
</body>
</html>


<h2>Enrolled Teams</h2>
<table class="table" border = "1">
	<tr>
		<th style="width:15%;">Team ID</th>
		<th style="width:15%;">Team Name</th>
		<th style="width:30%;">Description</th>
        <th style="width:15%;">ManagerID</th>
        <th style="width:10%;">Start Time</th>
		<th style="width:10%;"></th>
	</tr>
    <?php

	$teamInProject = $s->getTeamInfoByPID($PID);
	$teamInProject->bind_result($tTID, $tName, $tDescription, $tManager, $tStartTime);

    while($teamInProject->fetch())
    {
        echo "<tr>";
        echo "<td align=\"center\" valign=\"middle\">$tTID</td>";
        echo "<td align=\"center\" valign=\"middle\">$tName</td>";
        echo "<td align=\"center\" valign=\"middle\">$tDescription</td>";
        echo "<td align=\"center\" valign=\"middle\">$tManager</td>";
        echo "<td align=\"center\" valign=\"middle\">$tStartTime</td>";
		if($managerID === $EID)
		{
			echo "<td align=\"center\" valign=\"middle\"><a href=\"validateEditProject.php?PID=$PID&remove=$tTID&managerID=$managerID\">Remove</a></td>";
		}
        echo "</tr>";
    }
	$teamInProject->close();
    ?>
</table>




<?php
if($managerID === $EID)
{
    echo "<h2>Candidate Teams</h2>";
    $searchName = "";
    if(isset($_POST['searchName']))
    {
        $searchName = $_POST['searchName'];
    }
    echo "<form action=\"editProject.php?PID=$PID\" method=\"post\">";
    echo "<label>Candidate Team Name:</label> &nbsp;";
    echo "<input type=\"text\" name=\"searchName\" value=".$searchName.">";
    echo "<input type=\"submit\" value=\"Search\" />";
    echo "<input type=\"reset\" value=\"Reset\">";
    echo "</form>";
    ?>

    <table class="table" border = "1">
        <tr>
            <th style="width:15%;">Team ID</th>
            <th style="width:15%;">Team Name</th>
            <th style="width:30%;">Description</th>
            <th style="width:15%;">ManagerID</th>
            <th style="width:10%;">Start Time</th>
            <th style="width:10%;"></th>
        </tr
        <?php

        if($managerID === $EID)
        {
            $teamNotInProject = $s ->getTeamInfoByNotInProject($PID, $searchName);
            $teamNotInProject->bind_result($tTID, $tName, $tDescription, $tManager, $tStartTime);

            while($teamNotInProject->fetch())
            {
                echo "<tr>";
                echo "<td align=\"center\" valign=\"middle\">$tTID</td>";
                echo "<td align=\"center\" valign=\"middle\">$tName</td>";
                echo "<td align=\"center\" valign=\"middle\">$tDescription</td>";
                echo "<td align=\"center\" valign=\"middle\">$tManager</td>";
                echo "<td align=\"center\" valign=\"middle\">$tStartTime</td>";
                echo "<td align=\"center\" valign=\"middle\"><a href=\"validateEditProject.php?PID=$PID&add=$tTID&managerID=$managerID\">Add</a></td>";
                echo "</tr>";
            }
            $teamNotInProject->close();
        }
    }   
    ?>
</table>

<?php

$s->closeConn();

?>