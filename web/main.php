<?php
//header("Content-Type:application/json");
include 'include.php';
include ("integrations/php/fusioncharts-wrapper/fusioncharts.php");
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
<title>실시간 미세먼지 농도</title>
<script src="js/fusioncharts.js"></script>
<script src="js/themes/fusioncharts.theme.fusion.js"></script>
</head>
<body>

<div class="topnav">
  <a class="active" href="main">Realtime</a>
  <a href="api">External</a>
  <a href="calender/average">Average</a>
  <a href="calender/average_api">External average</a>
  <a href="product/product">Product</a>
  <a href="forecast/forecast">Forecast</a>
</div>


<div id="graph1" style="height:90%; width:90%"></div>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>


<script>

      Highcharts.setOptions({
                global: {
                    useUTC: false
                }
            });
           
            var chart;
            function requestData() {
                $.ajax({
                     url: 'ajax.php',
                    success: function (point) {
                        var series = chart.series[0],
                            shift = series.data.length > 20;

						var series1=chart.series[1],
						shift1=series1.data.length>20;

						var series2=chart.series[2],
						shift2=series2.data.length>20;


                        var timestamp = point[0];
                        var date = new Date(timestamp);
						var x=point[0];
						var pm1_0=Number(point[1]);
						var pm2_5=Number(point[2]);
						var pm10=Number(point[3]);
                        chart.series[0].addPoint([x,pm1_0], true, shift);
                        chart.series[1].addPoint([x,pm2_5], true, shift1);
                        chart.series[2].addPoint([x,pm10], true, shift2);

                       
                        setTimeout(requestData, 1000);
                    },
                    cache: false
                });
            }
           
            $(function () {
                chart = new Highcharts.Chart({
                    chart: {
                        renderTo: 'graph1',
                        defaultSeriesType: 'spline',
                        events: {
                            load: requestData
                        }
                    },
                    title: {
                        text: '미세먼지 농도 변화 그래프'
                    },
                    xAxis: {
                        type: 'datetime',
                        tickPixelInterval: 150,
                        maxZoom: 20 * 1000
                    },
                    yAxis: {
                        minPadding: 0.2,
                        maxPadding: 0.2,
                        title: {
                            text: 'Value',
                            margin: 80
                        }
                    },
                    series: [{
                        name: 'PM1.0',
                        data: []
                    },
							{name:'PM2.5',data:[]},
							{name:'PM10',data:[]}
					],
                });
            });

</script>
</body>

</html>

