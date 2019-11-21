<?php
include 'include.php';
include ("integrations/php/fusioncharts-wrapper/fusioncharts.php");
?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
}

.topnav {
  overflow: hidden;
  background-color: #333;
}

.topnav a {
  float: left;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}

.topnav a:hover {
  background-color: #ddd;
  color: black;
}

.topnav a.active {
  background-color: #4CAF50;
  color: white;
}
</style>

<meta charset="utf-8">
<title>현재 미세먼지 농도</title>
<script src="js/fusioncharts.js"></script>
<script src="js/themes/fusioncharts.theme.fusion.js"></script>
</head>
<body>


<div class="topnav">
  <a href="main">Realtime</a>
  <a class="active" href="api">External</a>
  <a href="calender/average">Average</a>
  <a href="calender/average_api">External average</a>
  <a href="product/product">Product</a>
  <a href="forecast/product">Forecast</a>
</div>



<?php

$query="select * from api order by timestamp desc limit 1";

$result=pg_query($query) or die('Query failed:' . pg_last_error());
$row=pg_fetch_row($result);

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
			["minvalue"=>"0","maxValue"=>"30","label"=>"좋음($row[1]μm)","code"=>"#2359C4"],
			["minvalue"=>"31","maxValue"=>"80","label"=>"보통($row[1]μm)","code"=>"#01B56E"],
			["minvalue"=>"81","maxValue"=>"150","label"=>"나쁨($row[1]μm)","code"=>"#F5C932"],
			["minvalue"=>"151","maxValue"=>"500","label"=>"매우나쁨($row[1]μm)","code"=>"#DA3539"]

			));

$PM10_0Chart["ColorRange"]=$PM10_0DataObj;
$PM10_0Chart["value"]=$row[1];
$PM10_0EncodeData=json_encode($PM10_0Chart);
$PM10_0widget=new FusionCharts("bulb","3","300","300","PM10_0","json",$PM10_0EncodeData);
$PM10_0widget->render();

pg_close($conn);
?>
<div id="PM2_5" style="text-align:center;">PM2_5</div>
<br>
<br>
<br>
<div id="PM10_0" style="text-align:center;">PM10_0</div>
<br>
<br>
<br>
<br>
<div style="text-align:center;">
<?php
echo "마지막 측정 시각(";
echo date("Y-m-d H:i:s",$row[0]);
echo ")";
?>
</div>
</body>

</html>

