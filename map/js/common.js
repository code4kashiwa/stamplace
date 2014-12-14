// デフォルト設定
var map_min_size = 11;			// マップサイズ（広域）
var map_max_size = 17;			// マップサイズ（ズーム）
var base_lat     = 35.862239;	// 柏駅の緯度
var base_lon     = 139.970981;	// 柏駅の経度

// オブジェクト定義
var map;						// マップオブジェクト
var markdata;					// 施設・店舗情報
var markArray = [];				// マーカー格納配列
var windowArray = [];			// メッセージウィンドウ格納配列
var params = new Array();		// GETパラメータ格納領域

/* GETパラメータ取得 */
function initGetParams() {
	var query = window.location.search.substring(1);
	var parms = query.split('&');
	for (var i=0; i<parms.length; i++) {
		var pos = parms[i].indexOf('=');
		if (pos > 0) {
			var key = parms[i].substring(0,pos);
			var val = parms[i].substring(pos+1);
			params[key] = val;
		}
	}
}

/* 地図の初期化 */
function initialize() {

	/* 地図のオプション設定 */
	var myOptions={
		/*初期のズーム レベル */
		zoom: map_min_size,
		/* 地図の中心点 */
		center: new google.maps.LatLng(base_lat, base_lon),
		/* デフォルトの UI 全体を無効化 */
		disableDefaultUI: true,
/*
		panControl: false,
		zoomControl: false,
		mapTypeControl: false,
		scaleControl: false,
		streetViewControl: false,
		overviewMapControl: false,
*/
		/* 地図タイプ */
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	/* 地図オブジェクト */
	map=new google.maps.Map(document.getElementById("map"), myOptions);

	/* 店舗データ取得 */
	$.ajax({
		type: "GET",
		data: {
			"prm1": 1,
			"prm2": 2,
			"prm3": 3
		},
		cache: false,
		url: "./testjson.php",
		success: function(data)
		{
			markdata = eval("(" + data + ")");

			var ptStart = {0: 174, 1: 197, 2: 126, 3: 6};

			/* マーカーオブジェクト */
			for (i = 0; i < markdata.length; i++) {
				var shop = markdata[i];

				var shop_latlng= new google.maps.LatLng(shop.lat, shop.lng);

				var iconImg = new google.maps.MarkerImage(
//								'/img/marker/default.png',						// url
//								new google.maps.Size(23, 20),					// size (png)
//								new google.maps.Point(ptStart[shop.type], 12),	// origin
//								new google.maps.Point(15, 20)					// anchor

					'/img/marker/default.gif',						// url
					new google.maps.Size(24, 23),					// size (gif)
					new google.maps.Point(ptStart[shop.type], 10),	// origin
					new google.maps.Point(16, 22)					// anchor
				);

				var marker = new google.maps.Marker({
					position: shop_latlng,
					map: map,
					icon: iconImg,
					title: shop.name
				});

				markArray[i] = marker;

				/* マーカーがクリックされた時 */
				attachMessage(markArray[i], i);

			}
		}
	});

	/* div:map のサイズ設定 */
	var map_width = $("#map").width();
	$("#map").height(map_width * 0.8);

	/* URLバーを消す */
	setTimeout(scrollTo, 100, 0, 1);
}

/* マーカークリック時のイベント設定 */
function attachMessage(marker, num) {
	// メッセージ設定
	var msg = markdata[num].name + "<br /><a href='placedetail.html?m="+ markdata[num].id + "'>詳細</a>";
	// メッセージウィンドウ定義
	var infowindow = new google.maps.InfoWindow({
		content:msg
	});
	// イベント設定
	google.maps.event.addListener(marker, 'click', function() {
		shopAction(num);
	});
	// メッセージウィンドウ情報を配列に格納
	windowArray[num] = infowindow;
}

/* 全てのメッセージウィンドウを消す */
function infowindowAllClear() {
	for (i = 0; i < windowArray.length; i++) {
		if(windowArray[i]) windowArray[i].close();
	}
}

/* 選択された位置に指定されたサイズで移動 */
function moveZoomPosition(lat, lng, size) {
	/* 位置設定 */
	var latlng= new google.maps.LatLng(lat, lng);
	/* 選択された位置に移動 */
	map.panTo(latlng);
	/* ズームアップ */
	map.setZoom(size);
}

/* 施設・店舗選択時のアクション */
function shopAction(num) {
	// 全てのメッセージウィンドウを消す
	infowindowAllClear();
	// 指定のメッセージウィンドウを表示
	windowArray[num].open(map, markArray[num]);
	// 選択された位置に移動＆ズームアップ
	var pos = markArray[num].getPosition();
	moveZoomPosition(pos.lat(), pos.lng(), map_max_size);
}

/* ズームアップ */
function zoomUp() {
	var nowzoom = map.getZoom();
	if(nowzoom < map_max_size) {
		map.setZoom(nowzoom + 1);
	}
}

/* ズームダウン */
function zoomDown() {
	var nowzoom = map.getZoom();
	if(nowzoom > map_min_size) {
		map.setZoom(nowzoom - 1);
	}
}

/* 特定のエリアを拡大表示 */
function viewNear(){
	// 全てのメッセージウィンドウを消す
	infowindowAllClear();
	// 選択された位置に移動＆ズームダウン
	moveZoomPosition(base_lat, base_lon, 16);
}

/* 広域選択 */
function viewAll(){
	// 全てのメッセージウィンドウを消す
	infowindowAllClear();
	// 選択された位置に移動＆ズームダウン
	moveZoomPosition(base_lat, base_lon, map_min_size);
}
