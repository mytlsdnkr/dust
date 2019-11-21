<?php
//header("Content-Type:application/json");
include '../include.php';
include ("../integrations/php/fusioncharts-wrapper/fusioncharts.php");
?>
<html>
<link rel="stylesheet" href="maincss.css">
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
<title></title>
<script src="../js/fusioncharts.js"></script>
<script src="../js/themes/fusioncharts.theme.fusion.js"></script>
</head>
<body>
<div class="topnav">
  <a href="../main">Realtime</a>
  <a href="../api">External</a>
  <a href="../calender/average">Average</a>
  <a href="../calender/average_api">External average</a>
  <a class="active" href="product">Product</a>
  <a href="../forecast/forecast">Forecast</a>
</div>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<?php
$query="select * from product";
$result=pg_query($query) or die('Query failed:' . pg_last_error());
$i=1;
while($line=pg_fetch_array($result,null,PGSQL_ASSOC)){


	echo "<br>
		<div class=\"section--itemcard_img\" style=\"text-align:center;\">
				<a href=\"{$line[reference]}\" class=\"link--itemcard\" target=\"_blank\">
					<img src=\"../images/{$i}.jpg\" class=\"image--itemcard\"> 
				</a>
			</div>
			<div class=\"section--itemcard_info\" style=\"text-align:center;\">
				<div class=\"section--itemcard_info_major\">
				<div class=\"area--itemcard_title\">
				<span class=\"text--itemcard_title_ellipsis\">
				<a href=\"{$line[reference]}\" class=\"link--itemcard\" target=\"_blank\" style=\"text-decoration:none; font-weight:bold; border-bottom: 1px solid #b4e7f8; box-shadow:inset 0 -4px 0 #b4e7f8; color:inherit; overflow-wrap:break-word; word-wrap:break-word; word-break; break-word;\">
				<span class=\"text--title\">{$line[title]}</span>
				</a>
				</span>
				</div>
			<div class=\"area--itemcard_price\">
			<strong class=\"text--price_seller\">{$line[price]}</strong>
			<span class=\"text--unit\">원</span>
			</span>
			</div>
			</div>
			<div class=\"section--itemcard_info_add\">
			<span class=\"text--adinfo\">{$line[addinfo]}</span>
			</div>
			<div class=\"section--itemcard_info_score\">
			<span class=\"for-ally\">{$line[reviewscore]}</span>
			<br>
			<span class=\"text--reviewcnt\">{$line[reviewcount]}</span>
			<br>
			<span class=\"text--buycnt\">{$line[sellcount]}</span>
			<br>
			</div>
";
		$i++;
}


			

?>
</body>

</html>


