<?php
include 'include.php';
include ("integrations/php/fusioncharts-wrapper/fusioncharts.php");
?>
<html>
<link rel="stylesheet" href="maincss.css">
<head>
<meta charset="utf-8">
<title>현재 미세먼지 농도</title>
 <script src="js/fusioncharts.js"></script>
 <script src="js/themes/fusioncharts.theme.fusion.js"></script>
</head>
<body>
<div >
<ul id="main-menu" style="width:100vh;" >
<li><a href="main.php">실시간 농도
<li><a href="#">미세먼지</a>
<ul id="sub-menu">
<li><a href="#">최대 수치</a></li>
<li><a href="#">최소 수치</a></li>
<li><a href="#">평균</a></li>
</ul>
</li>
<li><a href="#">초미세먼지</a>
<ul id="sub-menu1">
<li><a href="#">최대 수치</a></li>
<li><a href="#">최소 수치</a></li>
<li><a href="#">평균</a></li>
</ul>
</li>
</ul>
</li>
</div>
<br>
<br>
<br>


<?php

$query="select * from dust order by timestamp desc limit 1";

$result=pg_query($query) or die('Query failed:' . pg_last_error());
$row=pg_fetch_row($result);

//PM1.0
$PM1_0Chart=array (
	"chart"=> array(
		"caption"=> "현재 초미세먼지 농도(PM1.0)",
		"upperlimit"=> "0",
		"lowerlimit"=> "500",
		"usecolornameasvalue"=> "1",
		"placevaluesinside"=> "1",
		"valuefontsize"=> "20",
		"theme"=> "fusion"
	)
);

$PM1_0DataObj=array("color"=>array(
	["minvalue"=>"0","maxValue"=>"15","label"=>"좋음($row[1]μm)","code"=>"#2359C4"],
	["minvalue"=>"16","maxValue"=>"35","label"=>"보통($row[1]μm)","code"=>"#01B56E"],
	["minvalue"=>"36","maxValue"=>"75","label"=>"나쁨($row[1]μm)","code"=>"#F5C932"],
	["minvalue"=>"76","maxValue"=>"500","label"=>"매우나쁨($row[1]μm)","code"=>"#DA3539"]

));

$PM1_0Chart["ColorRange"]=$PM1_0DataObj;
$PM1_0Chart["value"]=$row[1];
$PM1_0EncodeData=json_encode($PM1_0Chart);
$PM1_0widget=new FusionCharts("bulb","1","300","300","PM1_0","json",$PM1_0EncodeData);
$PM1_0widget->render();

//PM1.0
//
//
//PM2.5
$PM2_5Chart=array (
	"chart"=> array(
		"caption"=> "현재 초미세먼지 농도(PM2.5)",
		"upperlimit"=> "0",
		"lowerlimit"=> "500",
		"usecolornameasvalue"=> "1",
		"placevaluesinside"=> "1",
		"valuefontsize"=> "20",
		"theme"=> "fusion"
	)
);

$PM2_5DataObj=array("color"=>array(
	["minvalue"=>"0","maxValue"=>"15","label"=>"좋음($row[2]μm)","code"=>"#2359C4"],
	["minvalue"=>"16","maxValue"=>"35","label"=>"보통($row[2]μm)","code"=>"#01B56E"],
	["minvalue"=>"36","maxValue"=>"75","label"=>"나쁨($row[2]μm)","code"=>"#F5C932"],
	["minvalue"=>"76","maxValue"=>"500","label"=>"매우나쁨($row[2]μm)","code"=>"#DA3539"]

));

$PM2_5Chart["ColorRange"]=$PM2_5DataObj;
$PM2_5Chart["value"]=$row[2];
$PM2_5EncodeData=json_encode($PM2_5Chart);
$PM2_5widget=new FusionCharts("bulb","2","300","300","PM2_5","json",$PM2_5EncodeData);
$PM2_5widget->render();
//PM2.5
$PM10_0Chart=array (
	"chart"=> array(
		"caption"=> "현재 미세먼지 농도(PM10.0)",
		"upperlimit"=> "0",
		"lowerlimit"=> "500",
		"usecolornameasvalue"=> "1",
		"placevaluesinside"=> "1",
		"valuefontsize"=> "20",
		"theme"=> "fusion"
	)
);

$PM10_0DataObj=array("color"=>array(
	["minvalue"=>"0","maxValue"=>"30","label"=>"좋음($row[3]μm)","code"=>"#2359C4"],
	["minvalue"=>"31","maxValue"=>"80","label"=>"보통($row[3]μm)","code"=>"#01B56E"],
	["minvalue"=>"81","maxValue"=>"150","label"=>"나쁨($row[3]μm)","code"=>"#F5C932"],
	["minvalue"=>"151","maxValue"=>"500","label"=>"매우나쁨($row[3]μm)","code"=>"#DA3539"]

));

$PM10_0Chart["ColorRange"]=$PM10_0DataObj;
$PM10_0Chart["value"]=$row[2];
$PM10_0EncodeData=json_encode($PM10_0Chart);
$PM10_0widget=new FusionCharts("bulb","3","300","300","PM10_0","json",$PM10_0EncodeData);
$PM10_0widget->render();
pg_close($conn);
?>
<div id="PM1_0" style="text-align:center;">PM1_0</div>
<div id="PM2_5" style="text-align:center;">PM2_5</div>
<div id="PM10_0" style="text-align:center;">PM10_0</div>
<div style="text-align:right;">
<?php
echo "마지막 측정 시각(";
echo date("Y-m-d h:i:s",$row[0]);
echo ")";
?>
</div>
<?php
echo '<script>setTimeout("window.location.reload()",10000);</script>';
?>
</body>

	</html>

