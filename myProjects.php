<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

$EID = $_SESSION["username"];
$level = $_SESSION["level"];

include 'sqlconnection.php';
// Create connection
$s = new sql();
$res = $s->getPriviledge();
$canCreateProject = FALSE;
while($row = $res->fetch_assoc())
{
    if($row["Function"] == "create project")
    {
        if($level <= $row["Level"])
            $canCreateProject = TRUE;
    }
}

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

<html>
<body>
<p>
<a href = "myPage.php">My Page</a>&nbsp;
&nbsp;
<a href = "logout.php" style="text-decoration:none;">Logout</a>
</p>
</body>
</html>

<h1>My Project Management</h1>
<h2>My Projects</h2>
<?php
$searchName = "";
if(isset($_POST['searchName']))
{
    $searchName = $_POST['searchName'];
}
echo "<form action=\"myProjects.php\" method=\"post\">";
echo "<label>Project Name:</label> &nbsp;";
echo "<input type=\"text\" name=\"searchName\" value=".$searchName.">";
echo "<input type=\"submit\" value=\"Search\" />";
echo "<input type=\"reset\" value=\"Reset\">";
echo "</form>";
?>
<table class="table" border = "1">
	<tr>
		<th style="width:10%;">Project ID</th>
        <th style="width:10%;"></th>
		<th style="width:15%;">Project Name</th>
		<th style="width:30%;">Description</th>
        <th style="width:10%;">Manager Name</th>
        <th style="width:10%;">Start Time</th>
		<th style="width:10%;">End Time</th>  
        <th style="width:10%;"></th> 
	</tr>
    <?php
    $res = $s->getProjectInfoByEID($EID, $searchName);
    $res->bind_result($PID, $Name, $Description, $ManagerName, $ManagerID, $StartTime, $EndTime);
    while($row = $res->fetch())
    {
        echo "<tr>";
        echo "<td align=\"center\" valign=\"middle\"><a href=\"editProject.php?PID=$PID\">$PID</a></td>";
        echo "<td align=\"center\" valign=\"middle\"><a href=\"postUpdate.php?PID=$PID\">Post Update</a></td>";
        echo "<td align=\"center\" valign=\"middle\">$Name</td>";
        echo "<td align=\"center\" valign=\"middle\">$Description</td>";
        echo "<td align=\"center\" valign=\"middle\"><a href=\"employeeInfo.php?EID=$ManagerID\" target=\"_blank\">$ManagerName</a></td>";
        echo "<td align=\"center\" valign=\"middle\">$StartTime</td>";
        echo "<td align=\"center\" valign=\"middle\">$EndTime</td>";
        if($EID === $ManagerID && $EndTime === null)
            echo "<td align=\"center\" valign=\"middle\"><a href=\"validateEditProject.php?close=$PID&managerID=$ManagerID\">Close Project</a></td>";
        echo "</tr>";
    }
    $res->close();
    ?>
</table>


<?php
if($canCreateProject)
{
    echo "<form action=\"CreateProject.php\" method =\"post\">";
    echo "<input type=\"submit\" value=\"Create New\">";
    echo "</form>";
}

?>

<?php
$pSearchName = "";
if(isset($_POST['pSearchName']))
{
    $pSearchName = $_POST['pSearchName'];
}
echo "<form action=\"myProjects.php\" method=\"post\">";
echo "<label>Project Name:</label> &nbsp;";
echo "<input type=\"text\" name=\"pSearchName\" value=".$pSearchName.">";
echo "<input type=\"submit\" value=\"Search\" />";
echo "<input type=\"reset\" value=\"Reset\">";
echo "</form>";


$res = $s->getUpdateByEID($EID, $pSearchName);
$res->bind_result($tPID, $tpName, $tEID, $teName, $updateTime, $description, $location);
while($row = $res->fetch())
{
    echo "<div style=\"border: thick solid black\">";
    echo "<li>Project ID: $tPID</li>";
    echo "<li>Project Name: $tpName</li>";
    echo "<li>Poster Name: <a href=\"employeeInfo.php?EID=$tEID\" target=\"_blank\">$teName</a></li>";
    echo "<li>Update Time: $updateTime</li>";
    echo "<li>Location: $location</li>";
    echo "<li><textarea cols=\"80\" rows=\"10\" readonly =\"readonly\">$description</textarea></li>";
    echo "<a href=\"comments.php?PID=$tPID&EID=$tEID&pSearchName=$pSearchName&updateTime=$updateTime\" target=\"_blank\" ><font size=\"12\">Comments</font> </a>";
    echo "<p></p>";
    $s2 = new sql();
    $sub = $s2->getResources($tPID, $tEID, $updateTime, 'image');
    $sub->bind_result($content);

    $s3 = new sql();
    $sub1 = $s3->getResources($tPID, $tEID, $updateTime, 'video');
    $sub1->bind_result($vcontent);

    while($sub->fetch())
    {
        $imageData = base64_encode($content);
        $src = 'data: ;base64,'.$imageData;
        echo '<img src="' . $src . '" style=\"width:200px;height:200px;\"  >';
        echo '&nbsp;';
    }

    echo "<p></p>";
    while($sub1->fetch())
    {
        $videoData = base64_encode($vcontent);//$content);
        $vsrc = 'data:video/mp4;base64,'.$videoData;
        ?>
            <video width='500' height='300' controls="controls" preload='metadata' poster="">
                <source src=<?php echo $vsrc?> type="video/mp4" codecs="avc1.4D401E, mp4a.40.2"/> 
            </video>
        <?php
    }

    $sub1->close();
    $sub->close();
    $s2->closeConn();
    $s3->closeConn();

    echo "</div>";
    echo "<p></p>";
    echo "<p></p>";
}
$res->close();
?>


<?php

$s->closeConn();

?>