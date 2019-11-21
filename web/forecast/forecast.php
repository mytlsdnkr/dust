<?php
include '../include.php';
include ("../integrations/php/fusioncharts-wrapper/fusioncharts.php");
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
<title>예상 미세먼지 농도</title>
<script src="../js/fusioncharts.js"></script>
<script src="../js/themes/fusioncharts.theme.fusion.js"></script>
</head>
<body>


<div class="topnav">
  <a href="../main">Realtime</a>
  <a href="../api">External</a>
  <a href="../calender/average">Average</a>
  <a href="../calender/average_api">External average</a>
  <a href="../product/product">Product</a>
  <a class="active" href="forecast">Forecast</a>
</div>



<?php
$fp=fopen("result.txt","r") or die("파일을 열 수 없습니다!");

$fr=fread($fp,"result.txt");

while(!feof($fp))
	$a=fgets($fp);
fclose($fp);

$str=explode(',',$a);

$pm1_0Value=(int)$str[0];
$pm2_5Value=(int)$str[1];
$pm10_0Value=(int)$str[2];
$hour=(int)$str[3];





$PM1_0Chart=array (
		"chart"=> array(
			"caption"=> "예상 초미세먼지 농도(PM1.0)",
			"upperlimit"=> "500",
			"lowerlimit"=> "0",
			"usecolornameasvalue"=> "1",
			"placevaluesinside"=> "1",
			"valuefontsize"=> "20",
			"theme"=> "fusion"
			)
		);


$PM1_0DataObj=array("color"=>array(
			["minvalue"=>"0","maxValue"=>"15","label"=>"좋음({$pm1_0Value}μm)","code"=>"#2359C4"],
			["minvalue"=>"16","maxValue"=>"35","label"=>"보통({$pm1_0Value}μm)","code"=>"#01B56E"],
			["minvalue"=>"36","maxValue"=>"75","label"=>"나쁨({$pm1_0Value}μm)","code"=>"#F5C932"],
			["minvalue"=>"76","maxValue"=>"500","label"=>"매우나쁨({$pm1_0Value}μm)","code"=>"#DA3539"]

			));

$PM1_0Chart["ColorRange"]=$PM1_0DataObj;
$PM1_0Chart["value"]=$pm1_0Value;
$PM1_0EncodeData=json_encode($PM1_0Chart);
$PM1_0widget=new FusionCharts("bulb","1","300","300","PM1_0","json",$PM1_0EncodeData);
$PM1_0widget->render();

//PM2.5
$PM2_5Chart=array (
		"chart"=> array(
			"caption"=> "예상 초미세먼지 농도(PM2.5)",
			"upperlimit"=> "500",
			"lowerlimit"=> "0",
			"usecolornameasvalue"=> "1",
			"placevaluesinside"=> "1",
			"valuefontsize"=> "20",
			"theme"=> "fusion"
			)
		);

$PM2_5DataObj=array("color"=>array(
			["minvalue"=>"0","maxValue"=>"15","label"=>"좋음({$pm2_5Value}μm)","code"=>"#2359C4"],
			["minvalue"=>"16","maxValue"=>"35","label"=>"보통({$pm2_5Value}μm)","code"=>"#01B56E"],
			["minvalue"=>"36","maxValue"=>"75","label"=>"나쁨({$pm2_5Value}μm)","code"=>"#F5C932"],
			["minvalue"=>"76","maxValue"=>"500","label"=>"매우나쁨({$pm2_5Value}μm)","code"=>"#DA3539"]

			));

$PM2_5Chart["ColorRange"]=$PM2_5DataObj;
$PM2_5Chart["value"]=$pm2_5Value;
$PM2_5EncodeData=json_encode($PM2_5Chart);
$PM2_5widget=new FusionCharts("bulb","2","300","300","PM2_5","json",$PM2_5EncodeData);
$PM2_5widget->render();
//PM2.5
$PM10_0Chart=array (
		"chart"=> array(
			"caption"=> "예상 미세먼지 농도(PM10.0)",
			"upperlimit"=> "0",
			"lowerlimit"=> "500",
			"usecolornameasvalue"=> "1",
			"placevaluesinside"=> "1",
			"valuefontsize"=> "20",
			"theme"=> "fusion"
			)
		);

$PM10_0DataObj=array("color"=>array(
			["minvalue"=>"0","maxValue"=>"30","label"=>"좋음({$pm10_0Value}μm)","code"=>"#2359C4"],
			["minvalue"=>"31","maxValue"=>"80","label"=>"보통({$pm10_0Value}μm)","code"=>"#01B56E"],
			["minvalue"=>"81","maxValue"=>"150","label"=>"나쁨({$pm10_0Value}μm)","code"=>"#F5C932"],
			["minvalue"=>"151","maxValue"=>"500","label"=>"매우나쁨({$pm10_0Value}μm)","code"=>"#DA3539"]

			));

$PM10_0Chart["ColorRange"]=$PM10_0DataObj;
$PM10_0Chart["value"]=$pm10_0Value;
$PM10_0EncodeData=json_encode($PM10_0Chart);
$PM10_0widget=new FusionCharts("bulb","3","300","300","PM10_0","json",$PM10_0EncodeData);
$PM10_0widget->render();

pg_close($conn);
?>
<div style="text-align:center;">
<?php
echo $hour;
echo ":00";
echo " 시";
?>
</div>
<div id="PM1_0" style="text-align:center;">PM1_0</div>
<div id="PM2_5" style="text-align:center;">PM2_5</div>
<div id="PM10_0" style="text-align:center;">PM10_0</div>
<br>
<br>
<br>
<br>
</body>

</html>

