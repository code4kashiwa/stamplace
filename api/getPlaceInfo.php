<?php

//require_once(dirname(__FILE__) . "/../module/ApiModule.inc");
require_once('/home/code4kashiwa/www/stamplace/module/ApiModule.php');

// デバッグモード
if(DEBUGMODE)
{
	ini_set("display_errors", "On");
	error_reporting(E_ALL);
//	error_reporting(E_WARNING);
}

class getPlaceInfoModule
{
	var $api;
/*
 *  初期処理
 */
	function __construct()
	{
		return true;
	}

/*
 *  API制御処理
 */
	function execute()
	{
		try {
			$this->api = new ApiModule();
			$this->api->logWrite(SP_LOG_INFO, "getPlaceInfoModule Start");
			$this->api->isAuthApiKey();

			$resType = (array_key_exists(RESPONSE_TYPE, $this->api->params)) ? $this->api->params[RESPONSE_TYPE] : "json";
			$encStr = (array_key_exists(RESPONSE_ENC, $this->api->params) && array_key_exists($this->api->params[RESPONSE_ENC], $this->api->encArray)) ? $this->api->params[RESPONSE_ENC] : "utf8";
			$place = $this->getPlaceSql();
			$this->api->makeResponse($resType, $place, $encStr);

			$this->api->logWrite(SP_LOG_INFO, "getPlaceInfoModule Finish");
		}
		catch (BaseModuleException $e)
		{
			print $e->getMessage();
			exit();
		}
	}

/*
 *  位置情報取得SQL文編集
 */
	function getPlaceSql($params = null)
	{
		// SQL文の編集
		$sqlStr  = "SELECT p.place_id,p.lat,p.lng,p.place_name,p.zip,p.address,p.tel,p.status";
		$sqlStr .= ", CASE WHEN pa.place_id IS NULL THEN 0";
		$sqlStr .= "  WHEN ( DAYOFWEEK(now()) = pa.off_week_code ) THEN 3";
		$sqlStr .= "  WHEN ( pa.start_open_time >= now() OR now() >= pa.finish_open_time ) THEN 2";
		$sqlStr .= "  ELSE 1 END as view_status";
		$sqlStr .= " FROM place p";
		$sqlStr .= " LEFT JOIN place_advance pa ON p.place_id = pa.place_id";


		$result = $this->api->getSelectData($sqlStr);

		return $result;
	}

/*
 *  終了処理
 */
	function __destruct() {
		return true;
	}
}

$module = new getPlaceInfoModule();
$module->execute();
header($module->api->apiHeaderInfo);
print $module->api->apiBodyInfo;
?>
