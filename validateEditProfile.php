<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

include 'sqlconnection.php';
// Create connection
$s = new sql();

$username = $_SESSION["username"];
$password = $_POST["currpwd"];
$newpassword = trim($_POST["newpwd"]);
if($newpassword == '')
    $newpassword = $password;

$email = trim($_POST["email"]);
if($email == "")
    $email = $_SESSION["email"];

$res = $s->getUserInfo($username, $password);

if($row = $res->fetch_assoc())
{
    
    if (strlen($newpassword) <= '8') {
        header("location:editProfile.php?errorcode=1"); //"Your Password Must Contain At Least 8 Characters!";
        $s->closeConn();
        exit;
    }
    elseif(!preg_match("#[0-9]+#",$newpassword)) {
        header("location:editProfile.php?errorcode=2"); //"Your Password Must Contain At Least 1 Number!";
        $s->closeConn();
        exit;
    }
    elseif(!preg_match("#[A-Z]+#",$newpassword)) {
        header("location:editProfile.php?errorcode=3"); //"Your Password Must Contain At Least 1 Capital Letter!";
        $s->closeConn();
        exit;
    }
    elseif(!preg_match("#[a-z]+#",$newpassword)) {
        header("location:editProfile.php?errorcode=4"); //"Your Password Must Contain At Least 1 Lowercase Letter!";
        $s->closeConn();
        exit;
    }

    $s->UpdateUserInfo($username, $newpassword, $email);
    if($email !== $_SESSION["email"])
        $_SESSION['email'] = $email;
    header("location:editProfile.php?errorcode=-1");
    $s->closeConn();
    exit;
}
else
{
    header("location:editProfile.php?errorcode=0");
    $s->closeConn();
    exit;
}
?>
