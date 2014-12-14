<?php 

require_once(dirname(__FILE__) . "/../module/Init.inc");

// デバッグモード
if(DEBUGMODE)
{
	ini_set("display_errors", "On");
	error_reporting(E_ALL);
//	error_reporting(E_WARNING);
}

// ログファイル名定義
$fpathStr = str_replace("#NOWDATE#", date("Ymd"), LOG_PATH);
// ログファイルを開く
$logFp = fopen($fpathStr, "r");
// ログ読み込み
$logLine = "";
while (!feof($logFp)) {
	$buffer = fgets($logFp);
	$logLine .= $buffer;
}
// ファイルクローズ
fclose($logFp);
?>
<html>
	<head>
		<title>StamPlace API Log</title>
	</head>
	<body>
		<b>StamPlace API Log</b>
		<hr>
		<pre><?php print $logLine; ?>
	</body>
</html>

