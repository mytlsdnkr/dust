<?php
//header("Content-Type:application/json");
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



<div id="graph1" style="height:400px"></div>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script>
var chart;
var timestamp=new Array();
var pm10=new Array();
//var PM2_5=new Array();
//var PM1_0=new Array();
var index=0;

function getValue(){
$.ajax({
type: 'post',
dataType: 'text',
url: './aaa.php',
success: function (data) {
var imsi=JSON.parse(data);


var series=chart.series[0];

shift=series.data.length>20;

chart.series[0].addPoint([imsi[0].timestamp,imsi[0].pm10_0],true,shift);



//PM2_5.push(data.pm2_5);
//PM1_0.push(data.pm1_0);
setTimeout(getValue,1000);
}
});
}

chart= new Highcharts.chart('graph1',{
chart:{
type:'line',
events:{
load :getValue
}
},

title:{
text:"실시간 미세먼지 수치 그래프"
	  },
subtitle:{
text:'x'
		 },
tooltip:{
crosshairs:[false,true],
		   valueDecimals:1
		},
xAxis:{
type:'string',
tickPixelInterval:150,
maxZoom:1000,
	 title:{
text:'시간'
	 }
	  },
yAxis:{
minPadding:0.2,
		   maxPadding:0.2,
		   title:{
text:'value',
	 margin:80
		   }

	  },
series:[{
name:'PM10',
data:[
	   } 
	

]



});


</script>


</body>

</html>

