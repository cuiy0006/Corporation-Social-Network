<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

$EID = $_SESSION["username"];


if(isset($_GET['errorcode']))
{
    $error = $_GET['errorcode'];
    if($error == 1)
        $errorMsg = "Project ID can not be empty";
    elseif($error == 2)
        $errorMsg = "Project Name can not be empty";
    elseif($error == 3)
        $errorMsg = "Please add some information for description";
    elseif($error == 4)
        $errorMsg = "Project ID already exists";
    else
        $errorMsg = $_GET['PID']." is Created Successfully";
    

    echo "<div class=\"alert\">";
    echo "<span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>";
    echo $errorMsg;
    echo "</div>";
}
?>


<html>
<body>
<p>
<a href = "myProjects.php">Back</a>&nbsp;
&nbsp;
<a href = "myPage.php">My Page</a>&nbsp;
&nbsp;
<a href = "logout.php" style="text-decoration:none;">Logout</a>
</p>

<form action="validateCreateProject.php" method="post">
<ul class="form">
	<li>
		<h1>Create New Project</h1>
	</li>
    <li>
    	<label>Project ID: </label>
    	<input type="text" name="PID" placeholder="Project ID" />
    </li>
    <li>
        <label>Project Name: </label>
        <input type="text" name="Name" placeholder="Project Name" />
    </li>
    <li>
        <label>Manager ID: <?php echo $EID ?></label>
    </li>
    <li>
        <label>Description: </label>
    </li>
    <li>
        <textarea name="Description" cols="80" rows="10"></textarea>
    </li>
    <li>
        <input type="submit" value="Submit" />
        <input type="reset" value="Reset">
    </li>
</ul>
</form>
</body>
</html>

