
<?php
include '../include.php';
include ("../integrations/php/fusioncharts-wrapper/fusioncharts.php");
?>
<html>
<head>

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
<title>평균 미세먼지 변화</title>
<script src="../js/fusioncharts.js"></script>
<script src="../js/themes/fusioncharts.theme.fusion.js"></script>
</head>
<body>

<div class="topnav">
  <a href="../main">Realtime</a>
  <a href="../api">External</a>
  <a class="active" href="average">Average</a>
  <a href="average_api">External average</a>
  <a href="../product/product">Product</a>
  <a href="../forecast/forecast">Forecast</a>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.8.18/themes/base/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="//code.jquery.com/ui/1.8.18/jquery-ui.min.js"></script>
<script>
  $.datepicker.setDefaults({
    dateFormat: 'yy-mm-dd',
    prevText: '이전 달',
    nextText: '다음 달',
    monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
    monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
    dayNames: ['일', '월', '화', '수', '목', '금', '토'],
    dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    showMonthAfterYear: true,
    yearSuffix: '년'
  });

  $(function() {
	  $("#datepicker1, #datepicker2").datepicker();
  });

</script>

<form action="average.php" method="post">
<p>조회기간:
<input type="text" id="datepicker1" name="first" value="<?php echo $_POST['first']; ?>"> ~
<input type="text" id="datepicker2" name="second" value="<?php echo $_POST['second']; ?>" >
<input type="submit" id="join" value="조회">
</form>
</p>

<?php
$pm1_0arr=array();
$pm2_5arr=array();
$pm10_0arr=array();
if(empty($_POST['first']) && empty($_POST['second'])){
$now=time();
for($i=1;$i<=24;$i++){
	$query="select avg(pm1_0),avg(pm2_5),avg(pm10_0) from dust where timestamp between {$now}-3600*{$i} and {$now}";
	$result=pg_query($query) or die('Query failed:' . pg_last_error());
	$row=pg_fetch_row($result);
	array_push($pm1_0arr,round($row[0]));
	array_push($pm2_5arr,round($row[1]));
	array_push($pm10_0arr,round($row[2]));
}



$PM1_0Config = array(
	"chart" => array(
		"caption" => "평균 초 미세먼지(PM1.0) 수치 변화",
		"xAxisName" => "Time",
		"yAxisName" => "Value",
		"lineColor" => "#000",
		"lineThickness" => "2",
		"theme" => "fusion"
	)
);


$PM1_0Label =array(
		["0","$pm1_0arr[0]"],
		["1","$pm1_0arr[1]"],
		["2","$pm1_0arr[2]"],
		["3","$pm1_0arr[3]"],
		["4","$pm1_0arr[4]"],
		["5","$pm1_0arr[5]"],
		["6","$pm1_0arr[6]"],
		["7","$pm1_0arr[7]"],
		["8","$pm1_0arr[8]"],
		["9","$pm1_0arr[9]"],
		["10","$pm1_0arr[10]"],
		["11","$pm1_0arr[11]"],
		["12","$pm1_0arr[12]"],
		["13","$pm1_0arr[13]"],
		["14","$pm1_0arr[14]"],
		["15","$pm1_0arr[15]"],
		["16","$pm1_0arr[16]"],
		["17","$pm1_0arr[17]"],
		["18","$pm1_0arr[18]"],
		["19","$pm1_0arr[19]"],
		["20","$pm1_0arr[20]"],
		["21","$pm1_0arr[21]"],
		["22","$pm1_0arr[22]"],
		["23","$pm1_0arr[23]"]
	);

$PM1_0data=array();

for($i=0;$i<count($PM1_0Label);$i++){
	array_push($PM1_0data,array(
		"label"=>$PM1_0Label[$i][0],"value"=>$PM1_0Label[$i][1]
	)
);
}

$PM1_0Config["data"] = $PM1_0data;
$PM1_0EncodedData = json_encode($PM1_0Config);

$PM1_0Chart = new FusionCharts("line", "m" , "100%", "300", "PM1_0", "json", $PM1_0EncodedData);
$PM1_0Chart->render();
//2.5
$PM2_5Config = array(
	"chart" => array(
		"caption" => "평균 초 미세먼지(PM2.5) 수치 변화",
		"xAxisName" => "Time",
		"yAxisName" => "Value",
		"lineColor" => "#000",
		"lineThickness" => "2",
		"theme" => "fusion"
	)
);


$PM2_5Label =array(
		["0","$pm2_5arr[0]"],
		["1","$pm2_5arr[1]"],
		["2","$pm2_5arr[2]"],
		["3","$pm2_5arr[3]"],
		["4","$pm2_5arr[4]"],
		["5","$pm2_5arr[5]"],
		["6","$pm2_5arr[6]"],
		["7","$pm2_5arr[7]"],
		["8","$pm2_5arr[8]"],
		["9","$pm2_5arr[9]"],
		["10","$pm2_5arr[10]"],
		["11","$pm2_5arr[11]"],
		["12","$pm2_5arr[12]"],
		["13","$pm2_5arr[13]"],
		["14","$pm2_5arr[14]"],
		["15","$pm2_5arr[15]"],
		["16","$pm2_5arr[16]"],
		["17","$pm2_5arr[17]"],
		["18","$pm2_5arr[18]"],
		["19","$pm2_5arr[19]"],
		["20","$pm2_5arr[20]"],
		["21","$pm2_5arr[21]"],
		["22","$pm2_5arr[22]"],
		["23","$pm2_5arr[23]"]
	);

$PM2_5data=array();

for($i=0;$i<count($PM2_5Label);$i++){
	array_push($PM2_5data,array(
		"label"=>$PM2_5Label[$i][0],"value"=>$PM2_5Label[$i][1]
	)
);
}

$PM2_5Config["data"] = $PM2_5data;
$PM2_5EncodedData = json_encode($PM2_5Config);

$PM2_5Chart = new FusionCharts("line", "a" , "100%", "300", "PM2_5", "json", $PM2_5EncodedData);
$PM2_5Chart->render();
//10_0
$PM10_0Config = array(
	"chart" => array(
		"caption" => "평균 미세먼지(PM10) 수치 변화",
		"xAxisName" => "Time",
		"yAxisName" => "Value",
		"lineColor" => "#000",
		"lineThickness" => "2",
		"theme" => "fusion"
	)
);


$PM10_0Label =array(
		["0","$pm10_0arr[0]"],
		["1","$pm10_0arr[1]"],
		["2","$pm10_0arr[2]"],
		["3","$pm10_0arr[3]"],
		["4","$pm10_0arr[4]"],
		["5","$pm10_0arr[5]"],
		["6","$pm10_0arr[6]"],
		["7","$pm10_0arr[7]"],
		["8","$pm10_0arr[8]"],
		["9","$pm10_0arr[9]"],
		["10","$pm10_0arr[10]"],
		["11","$pm10_0arr[11]"],
		["12","$pm10_0arr[12]"],
		["13","$pm10_0arr[13]"],
		["14","$pm10_0arr[14]"],
		["15","$pm10_0arr[15]"],
		["16","$pm10_0arr[16]"],
		["17","$pm10_0arr[17]"],
		["18","$pm10_0arr[18]"],
		["19","$pm10_0arr[19]"],
		["20","$pm10_0arr[20]"],
		["21","$pm10_0arr[21]"],
		["22","$pm10_0arr[22]"],
		["23","$pm10_0arr[23]"]
	);

$PM10_0data=array();

for($i=0;$i<count($PM10_0Label);$i++){
	array_push($PM10_0data,array(
		"label"=>$PM10_0Label[$i][0],"value"=>$PM10_0Label[$i][1]
	)
);
}

$PM10_0Config["data"] = $PM10_0data;
$PM10_0EncodedData = json_encode($PM10_0Config);

$PM10_0Chart = new FusionCharts("line", "b" , "100%", "300", "PM10_0", "json", $PM10_0EncodedData);
$PM10_0Chart->render();
}else{
	$label=array();
	$start=strtotime($_POST['first']);
	$target=strtotime($_POST['second']);
	$day=86400;
	$end=$start+$day;
	while($end<=$target){
	$query="select avg(pm1_0),avg(pm2_5),avg(pm10_0) from dust where timestamp between {$start} and {$end}";
	$result=pg_query($query) or die('Query failed:' . pg_last_error());
	$row=pg_fetch_row($result);
	array_push($label,date("m-d",$start));
	array_push($pm1_0arr,round($row[0]));
	array_push($pm2_5arr,round($row[1]));
	array_push($pm10_0arr,round($row[2]));
	$start+=$day;
	$end+=$day;
	}

$PM1_0Config = array(
	"chart" => array(
		"caption" => "평균 초 미세먼지(PM1.0) 수치 변화",
		"subCaption"=>"Last 24 hours",
		"xAxisName" => "Time",
		"yAxisName" => "Value",
		"lineColor" => "#000",
		"lineThickness" => "2",
		"theme" => "fusion"
	)
);


	$pm1_0Label =array(
		array()
	);
	for($i=0;$i<count($label);$i++){
		$pm1_0Label[$i][0]=$label[$i];
		$pm1_0Label[$i][1]=$pm1_0arr[$i];
	}

$PM1_0data=array();

for($i=0;$i<count($pm1_0Label);$i++){
	array_push($PM1_0data,array(
		"label"=>$pm1_0Label[$i][0],"value"=>$pm1_0Label[$i][1]
	)
);
}

$PM1_0Config["data"] = $PM1_0data;
$PM1_0EncodedData = json_encode($PM1_0Config);

$PM1_0Chart = new FusionCharts("line", "m" , "100%", "300", "PM1_0", "json", $PM1_0EncodedData);
$PM1_0Chart->render();
 
//2.5
$PM2_5Config = array(
	"chart" => array(
		"caption" => "평균 초 미세먼지(PM2.5) 수치 변화",
		"subCaption"=>"Last 24 hours",
		"xAxisName" => "Time",
		"yAxisName" => "Value",
		"lineColor" => "#000",
		"lineThickness" => "2",
		"theme" => "fusion"
	)
);


	$pm2_5Label =array(
		array()
	);

	for($i=0;$i<count($label);$i++){
		$pm2_5Label[$i][0]=$label[$i];
		$pm2_5Label[$i][1]=$pm2_5arr[$i];
	}
$PM2_5data=array();

for($i=0;$i<count($pm2_5Label);$i++){
	array_push($PM2_5data,array(
		"label"=>$pm2_5Label[$i][0],"value"=>$pm2_5Label[$i][1]
	)
);
}

$PM2_5Config["data"] = $PM2_5data;
$PM2_5EncodedData = json_encode($PM2_5Config);

$PM2_5Chart = new FusionCharts("line", "a" , "100%", "300", "PM2_5", "json", $PM2_5EncodedData);
$PM2_5Chart->render();

$PM10_0Config = array(
	"chart" => array(
		"caption" => "평균 미세먼지(PM10) 수치 변화",
		"xAxisName" => "Time",
		"yAxisName" => "Value",
		"lineColor" => "#000",
		"lineThickness" => "2",
		"theme" => "fusion"
	)
);

	$pm10_0Label=array(
		array()
	);

	for($i=0;$i<count($label);$i++){
		$pm10_0Label[$i][0]=$label[$i];
		$pm10_0Label[$i][1]=$pm10_0arr[$i];
	}



$pm10_0data=array();

for($i=0;$i<count($pm10_0Label);$i++){
	array_push($pm10_0data,array(
		"label"=>$pm10_0Label[$i][0],"value"=>$pm10_0Label[$i][1]
	)
);
}


$PM10_0Config["data"] = $pm10_0data;
$PM10_0EncodedData = json_encode($PM10_0Config);

$PM10_0Chart = new FusionCharts("line", "b" , "100%", "300", "PM10_0", "json", $PM10_0EncodedData);
$PM10_0Chart->render();



}
?>
   <div id="PM1_0">Chart will render here!</div>
<br>
<br>
   <div id="PM2_5">Chart will render here!</div>
<br>
<br>
   <div id="PM10_0">Chart will render here!</div>
</body>

</html>
