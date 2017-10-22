<?php
include 'sqlconnection.php';

$s = new sql();


$res = $s->getData();
$res->bind_result($rtype, $content);
$i = 0;

while($row = $res->fetch())
{
    $i = $i + 1;
    if($rtype == 'image')
    {
        $imageData = base64_encode($content);
        $src = 'data: ;base64,'.$imageData;
        echo '<img src="' . $src . '">';
    }
    if($rtype === 'video')
    {
        $videoData = base64_encode(file_get_contents("d:\small.mp4"));//$content);
        $vsrc = 'data:video/mp4;base64,'.$videoData;
        ?>
            <video width='500' height='300' controls="controls" preload='metadata' poster="">
                <source src=<?php echo $vsrc?> type="video/mp4" codecs="avc1.4D401E, mp4a.40.2"/> 
            </video>
        <?php
    }
}
$res->close();

$s->closeConn();
// $path = 'd:\r1.jpg';
// $type = pathinfo($path, PATHINFO_EXTENSION);
// $data = file_get_contents($path);
// echo $data;
?>




