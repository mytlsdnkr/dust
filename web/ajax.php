<?php
include 'include.php';
include ("integrations/php/fusioncharts-wrapper/fusioncharts.php");
?>

<?php

header("Content-Type:text/json");

$query="select * from dust order by timestamp desc limit 1";

$result=pg_query($query) or die('Query failed:' . pg_last_error());
$row=pg_fetch_row($result);


$timestamp=$row[0]*1000;
$pm10_0=$row[1];
$pm2_5=$row[2];
$pm1_0=$row[3];

$target=array($timestamp,$pm10_0,$pm2_5,$pm1_0);

echo json_encode($target);
?>
