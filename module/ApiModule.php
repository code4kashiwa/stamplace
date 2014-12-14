<?php
/*
 *  アクセスマップ表示モジュール
 */

//require_once(dirname(__FILE__) . "/BaseModule.inc");
require_once('/home/code4kashiwa/www/stamplace/module/BaseModule.php');

class ApiModule extends baseModule
{
	// リクエストパラメータ格納領域
	var $params = array();
	// APIキー
	var $apiKey = null;
	// APIレスポンス情報
	var $apiResType = "json";
	var $apiHeaderInfo;
	var $apiBodyInfo = "";
	// ログ情報
	var $logInfo;
	// 文字エンコード設定
	var $encArray = array("jis" => "ISO-2022-JP", "sjis" => "SJIS_win", "euc" => "EUCJP_win", "utf8" => "utf-8");

/*
 *  初期処理
 */
	function __construct()
	{
		parent::__construct();

		// パラメータ定義
		$this->getParams();

		// APIキー取得
		$this->apiKey = $this->params[API_KEY];

		if($this->apiKey == null || strlen($this->apiKey) == 0)
		{
			$this->setErrorInfo("APIキーの取得に失敗しました。");
			return false;
		}

		// DB接続
		$this->connSite = $this->getConnection(ADMINDBNAME, DBSERVER, BASEDBUSER, BASEDBPASS);

		return true;
	}

/*
 *  出力ログレベル設定
 */
	function setLoglevel($logLevel = SP_LOG_INFO)
	{
		if(array_key_exists($logLevel, $this->logLevelStr))
			$this->logLv = $logLevel;
		else
			$this->logLv = SP_LOG_INFO;

		return true;
	}

/*
 *  APIキー認証（あとで作る）
 */
	function isAuthApiKey()
	{
		$this->logWrite(SP_LOG_INFO, "AuthApiKey:[" . $this->apiKey . "]");
/*

		// アクセス日時取得
		session_start();
		$this->accessTime = $_SESSION['ACCESS'];
		// ベースキー・アクセス日時・最終ログイン日時より管理者情報取得
		if(!$this->getAdminInfo()) return false;
		// 取得したサイトIDよりサイト情報取得
		if(!$this->getSiteInfo()) return false;
		// アクセス日時の更新
		$this->accessTime = date("YmdHis");
		// アクセス日時更新
		if(!$this->updateAccessTime()) return false;
		// セッション情報設定
		session_start();
		$_SESSION['ACCESS'] = $this->accessTime;
		// ログインフラグ設定
		$this->isLogin = true;
*/

		return true;
	}

	/*
	 *  SELECT文実行＆結果抽出
	 */
	function getSelectData($sql)
	{
		$result = array();

		$rows = $this->queryExec($this->connSite, $sql);
		if($rows->rowCount() > 0)
		{
			while($row = $rows->fetch(PDO::FETCH_ASSOC))
			{
				$result[] = $row;
			}
		}

		return $result;
	}

/*
 *  レスポンス情報の作成
 */
	function makeResponse($resType, $resParams, $encode_type = "utf8")
	{
		// パラメータチェック
		if($resType == null || strlen($resType) == 0)
		{
			$this->setErrorInfo("パラメータ種別の取得に失敗しました。");
			return null;
		}
		// パラメータチェック
		if($resParams == null || count($resParams) == 0)
		{
			$this->setErrorInfo("出力内容の取得に失敗しました。");
			return null;
		}
		// 文字エンコード設定（UTF-8以外のものを変換）
		if($encode_type != "utf8")
		{
			mb_convert_variables($this->encArray[$encode_type], 'UTF-8', $resParams);
		}
		// レスポンス種別を取得
		$this->apiResType = $resType;
		// レスポンスの種別により出力内容を変える
		switch ($resType) {
			case "json":
				$encode = json_encode($resParams);
				$this->apiHeaderInfo = "Content-Type: text/javascript; charset=" . $this->encArray[$encode_type];
				$this->apiBodyInfo = $encode;
				break;
			case "jsonp":
				$callback = $_GET["callback"];
				$encode = json_encode($resParams);
				$this->apiHeaderInfo = "Content-Type: text/javascript; charset=" . $this->encArray[$encode_type];
				$this->apiBodyInfo = $callback . "(" . $encode. ")";
				break;
			case "xml":
				$this->apiHeaderInfo = "Content-Type: text/xml; charset=" . $this->encArray[$encode_type];
				$xmlstr = "<?xml version=\"1.0\" ?><result></result>";
				$xml = new SimpleXMLElement($xmlstr);
				foreach($resParams as $arrKey => $arrVal)
				{
					if(!is_array($arrVal))
					{
						$xmlitem = $xml->addChild($arrKey, $arrVal);
					}
					else
					{
						$xmlitem = $xml->addChild("item");
						foreach($arrVal as $xmlKey => $xmlVal)
						{
							$xmlitem->addChild($xmlKey, $xmlVal);
						}
					}
				}
				$this->apiBodyInfo = $xml->asXML();
				break;
		}

		return true;
	}

/*
 *  終了処理
 */
	function __destruct() {
		return true;
	}

}
?>
