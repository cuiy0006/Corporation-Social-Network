<?php
session_start();

if (!isset($_SESSION["username"])) {
	echo "<h1>Wrong Page.</h1>";
	echo "<a href=\"index.php\">Return to Log In Page</a>";
	exit;
}

$PID = $_GET['PID'];
$EID = $_SESSION['username'];


include 'sqlconnection.php';
$s = new sql();

?>

<html>
<head>
<script type='text/javascript'>
    function validateMyForm() {
        // GET THE FILE INPUT.
        var fi = document.getElementById('myfile');

        // VALIDATE OR CHECK IF ANY FILE IS SELECTED.
        if (fi.files.length > 0) {

            if(fi.files.length > 9)
            {
                alert('File amount cannot be over 9.') 
                return false;   
            }
            // RUN A LOOP TO CHECK EACH SELECTED FILE.
            for (var i = 0; i <= fi.files.length - 1; i++) {

                var fname = fi.files.item(i).name;      // THE NAME OF THE FILE.
                var fsize = fi.files.item(i).size;      // THE SIZE OF THE FILE.

                var ext = fname.split('.').pop();
                if(ext != 'jpg' && ext != 'mp4')
                {
                    alert('File type must be mp4 or jpg.') 
                    return false;
                }
                if(fsize > 2097152)
                {
                    alert('File size cannot be over 2MB.') 
                    return false;
                }
            }
        }
        else { 
            alert('Please select a file.') 
            return false;
        }
        return true;
    }
</script>
</head>


<body>


<a href = "myProjects.php?PID=$PID">Back</a>&nbsp;
<h2>Post a Update for <?php echo $PID?></h2>
    <form method="post" enctype="multipart/form-data">
        Content: 
        <p></p>
        <textarea id="text1" name="text1" cols="100" rows="20"></textarea>
        <p></p>
        <input type="file" name="my_file[]" id="myfile" accept=".mp4,.jpg" multiple>
        <input type="submit" name="submit" value="Submit" onclick="return validateMyForm();">
    </form>
    <p id="fp"></p>


    <?php
        if(isset($_POST['submit']))
        {

            $user_ip = "69.203.133.198"; //$_SERVER["REMOTE_ADDR"];

            $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
            $User_Latitude = $geo["geoplugin_latitude"];
            $User_Longitude = $geo["geoplugin_longitude"];

            $url = sprintf("https://maps.googleapis.com/maps/api/geocode/json?latlng=%s,%s", $User_Latitude, $User_Longitude);
            $content = file_get_contents($url); // get json content
            $metadata = json_decode($content, true); //json decoder
            if(count($metadata['results']) > 0)
            {
                $result = $metadata['results'][0];
                $User_location=$result['formatted_address']; // Address into normalize format.
            }

            $text = $_POST['text1'];
            $location = $User_location;
            date_default_timezone_set("America/New_York");
            $date = new DateTime(null);
            $updateTime = $date->format("Y-m-d H:i:s");

            $s->insertUpdate($PID,$EID,$updateTime,$location,$text);

            if (isset($_FILES['my_file'])) {
                $myFile = $_FILES['my_file'];
                $fileCount = count($myFile["name"]);

                for ($i = 0; $i < $fileCount; $i++) {
                    $tmpName = $myFile["tmp_name"][$i];
                    $fileType = $myFile['type'][$i];
                    $rtype = $fileType === 'image/jpeg'? 'image' : 'video';

                    //$type = pathinfo($tmpName, PATHINFO_EXTENSION);
                    $data = file_get_contents($tmpName);
                    //$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    // $fp = fopen($tmpName, 'r');
                    // $content = fread($fp, filesize($tmpName));
                    // $content = addslashes($content);
                    // fclose($fp);
                    $s->insertResources($PID, $EID, $updateTime, $rtype, $data);
                    /*?>
                        <p>File #<?= $i+1 ?>:</p>
                        <p>
                            Name: <?= $myFile["name"][$i] ?><br>
                            Temporary file: <?= $myFile["tmp_name"][$i] ?><br>
                            Type: <?= $myFile["type"][$i] ?><br>
                            Size: <?= $myFile["size"][$i] ?><br>
                            Error: <?= $myFile["error"][$i] ?><br>
                        </p>
                    <?php*/
                }
            }
            header('location:myProjects.php?postUpdate=TRUE');

        }
    ?>

</body>
</html>

<?php
$s->closeConn();
?>