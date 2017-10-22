<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

if (isset($_GET["errorcode"])) {
    $errorcode = $_GET["errorcode"];

    if($errorcode == 0)
        $errorMsg = "Incorrect Password";
    elseif($errorcode == 1)
        $errorMsg = "Your Password Must Contain At Least 8 Characters!";
    elseif($errorcode == 2)
        $errorMsg = "Your Password Must Contain At Least 1 Number!";
    elseif($errorcode == 3)
        $errorMsg = "Your Password Must Contain At Least 1 Capital Letter!";
    elseif($errorcode == 4)
        $errorMsg = "Your Password Must Contain At Least 1 Lowercase Letter!";
    elseif($errorcode == -1)
        $errorMsg = "Update Successfully!";

    echo "<div class=\"alert\">";
    echo "<span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>";
    echo $errorMsg;
    echo "</div>";
}
?>


<html>
<body>
<a href = "myPage.php">Back</a>&nbsp;
<h1>My Profile</h1>
<form action="validateEditProfile.php" method="post">
<ul class="form">
    <li>
        Username: <?php echo $_SESSION["username"];?>
    </li>
    <p></p>
    <li>
        Name    : <?php echo $_SESSION["name"];?>
    </li>
    <p></p>
    <li>
        Title   : <?php echo $_SESSION["title"];?>
    </li>
    <p></p>
    <li>
        Level   : <?php echo $_SESSION["level"];?>
    </li>
    <p></p>
    <li>
        Email   : <input type="email" name="email" placeholder=<?php echo $_SESSION["email"];?> />
    </li>
    <p></p>
    <li>
        Current Password: <input type="password" name="currpwd" placeholder="Password";/>
    </li>
    <p></p>
    <li>
        New Password    : <input type="password" name="newpwd" placeholder="Password";/>
    </li>
    <p></p>
    <input type="submit" value="Submit" />
    <input type="reset" value="Reset">
</ul>
</form>
</body>
</html>