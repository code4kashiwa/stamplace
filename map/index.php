<?php
mb_language("Japanese");
mb_internal_encoding("utf-8"); //内部文字コードを変更

// パラメーターを取得
if($_SERVER["REQUEST_METHOD"] != "POST")
{
	$params = $_GET;
}
else
{
	$params = $_POST;
}

?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta content='text/html; charset=UTF-8' http-equiv='Content-Type'/>
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<title>StamPlace - かしわ直売所マップ</title>
		<!-- CSS -->
		<link rel="stylesheet" href="./css/style.css">
		<link rel="stylesheet" href="./css/jquery.pageslide.css">
		<!-- jQuery -->
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<!-- SmoothScroll -->
		<script type="text/javascript" src="./js/jquery/smoothscroll.js"></script>
		<!-- Vertical Accordion Nav Menu -->
		<script type="text/javascript" src="./js/jquery/nav.js"></script>
<!-- inview -->
<script type="text/javascript" src="./js/jquery/jquery.inview.min.js"></script>
		<!-- Google Maps APIの読み込み -->
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<!-- オリジナルスクリプト -->
		<script type="text/javascript" src="./js/map_placesearchmap.js"></script>

		</head>

	<body onload="initialize()">

<?php require_once('_parts_header.inc') ?>

		<div id="main">
<?php require_once('_parts_mapmenu.inc') ?>
			<div id="map"></div>
			<div id="place_info" style="display:none">
<?php require_once('_parts_placeinfo.inc') ?>
			</div>
		</div>

<?php require_once('_parts_footer.inc') ?>

<?php require_once('_parts_sidemenu.inc') ?>

<!-- iPhoneのURLバーを消す -->
<script>setTimeout(scrollTo, 100, 0, 1);</script>

	</body>
</html>
