<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="style.css">
<title>Employees of a large corporation social network</title>
</head>
<body>
<div class="content">
<?php
session_start();
if (isset($_GET["retry"])) {
	echo "<div class=\"alert\">";
	echo "<span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>";
	echo "Username doesn't exist or incorrect Password. Please retry.";
	echo "</div>";
} else if (isset($_GET["logout"])) {
	echo "<div class=\"success\">";
	echo "<span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>";
	echo "Successfully logged out.";
	echo "</div>";
} else if (isset($_SESSION["username"])){
	header("location:myPage.php");
	exit;
}
?>
<form action="login_check.php" method="post">
<ul class="form">
	<li>
		<h1>Employees of a large corporation social network</h1>
	</li>
    <li>
    	<label>Username <span class="required"></span></label>
    	<input type="text" name="username" 
    		<?php 
    			if (isset($_SESSION["username"])) {
    				$usr = $_SESSION["username"];
    				echo "value=\"$usr\"";
    			}
    		?> 
    		class="field-long" placeholder="Username" />
    </li>
    <li>
        <label>Password </label>
        <input type="password" name="password" class="field-long" placeholder="Password" />
    </li>
    <li>
        <input type="submit" value="Submit" />
        <input type="reset" value="Reset">
    </li>
</ul>
</form>
</div>
</body>
</html>