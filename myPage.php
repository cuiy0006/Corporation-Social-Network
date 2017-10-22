<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

$employeeName = $_SESSION["name"];
$level = $_SESSION["level"];
$canCreateTeam = FALSE;
$canCreateProj = FALSE;

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
    else if($row["Function"] == "create project")
    {
        if($level <= $row["Level"])
            $canCreateProj = TRUE;
    }
}
$s->closeConn();
?>

<body>
<p>
Welcome, <a href = "editProfile.php" style="text-decoration:none;"><?php echo $employeeName;?></a>!
&nbsp;
<a href = "myTeams.php">Teams</a>
&nbsp;
<a href = "myProjects.php">Projects</a>
&nbsp;
<a href = "logout.php" style="text-decoration:none;">Logout</a>
</p>


<h2>My Privileges</h2>
<table class="table" border = '1'>
    <?php
        echo "<tr>";
        echo "<td align=\"center\" valign=\"middle\">&nbsp;Create Team&nbsp;</td>";
        if($canCreateTeam)
            echo "<td align=\"center\" valign=\"middle\">&nbsp;TRUE&nbsp;</td>";
        else
            echo "<td align=\"center\" valign=\"middle\">&nbsp;FALSE&nbsp;</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=\"center\" valign=\"middle\">&nbsp;Create Project&nbsp;</td>";
        if($canCreateProj)
            echo "<td align=\"center\" valign=\"middle\">&nbsp;TRUE&nbsp;</td>";
        else
            echo "<td align=\"center\" valign=\"middle\">&nbsp;FALSE&nbsp;</td>";
        echo "</tr>";
    ?>

</table>
</body>