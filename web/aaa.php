<?php
include 'include.php';
include ("integrations/php/fusioncharts-wrapper/fusioncharts.php");
?>

<?php

//header("Content-Type:application/json");

$query="select * from dust order by timestamp desc limit 1";

$result=pg_query($query) or die('Query failed:' . pg_last_error());
$row=pg_fetch_row($result);


$target=array();
$temp=new stdClass();
$temp->timestamp=date("Y-m-d h:i:s",$row[0]);
$temp->pm10_0=$row[1];
$temp->pm2_5=$row[2];
$temp->pm1_0=$row[3];

$target[]=$temp;
unset($temp);

echo json_encode($target);
?>
