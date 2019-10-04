<?php
include '../include.php';
include ("../integrations/php/fusioncharts-wrapper/fusioncharts.php");
?>
<html>
<link rel="stylesheet" href="../../maincss.css">
<head>
<meta charset="utf-8">
<title>현재 미세먼지 농도</title>
<script src="../js/fusioncharts.js"></script>
<script src="../js/themes/fusioncharts.theme.fusion.js"></script>
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

<script>const dataSource = {
chart: {
caption: "Countries With Most Oil Reserves [2017-18]",
		 subcaption: "In MMbbl = One Million barrels",
		 xaxisname: "Country",
		 yaxisname: "Reserves (MMbbl)",
		 numbersuffix: "K",
		 theme: "fusion"
	   },
data: [
	  {
label: "Venezuela",
	   value: "290"
	  },
	  {
label: "Saudi",
	   value: "260"
	  },
	  {
label: "Canada",
	   value: "180"
	  },
	  {
label: "Iran",
	   value: "140"
	  },
	  {
label: "Russia",
	   value: "115"
	  },
	  {
label: "UAE",
	   value: "100"
	  },
	  {
label: "US",
	   value: "30"
	  },
	  {
label: "China",
	   value: "30"
	  }
	   ]
};

FusionCharts.ready(function() {
		var myChart = new FusionCharts({
type: "column2d",
renderAt: "chart-container",
width: "100%",
height: "100%",
dataFormat: "json",
dataSource
}).render();
		});

</script>

<div id="chart-container"></div>





</body>

</html>

