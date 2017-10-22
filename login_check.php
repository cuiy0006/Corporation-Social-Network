<html>
<body>

<?php
include 'sqlconnection.php';
// Create connection
$s = new sql();

$username = $_POST['username'];
$password = $_POST['password'];

$res = $s->getUserInfo($username, $password);
if($row = $res->fetch_assoc())
{
    session_start();
    $_SESSION["username"] = $row['EID'];
    $_SESSION['name'] = $row['Name'];
    $_SESSION['title'] = $row['Title'];
    $_SESSION['level'] = $row['Level'];
    $_SESSION['email'] = $row['Email'];
    header("location:myPage.php");
}
else
{
    header("location:index.php?retry=true");
}

$s->closeConn();
?>
</body>
</html>