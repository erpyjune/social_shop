<?php
include "../lib/timon.class.php";
//include "../lib/curl.class.php";
//include "../lib/parser.class.php";
//include "../lib/db.class.php";

/////////////////////// main //////////////////////////////////
if ($argc < 2) {
	die ("(usage) query\n");
}

$ti   = new TiMonMore;
$pa   = new EPParser;
$cu   = new EPCurl;
$db   = new EPDB;
$result_item_arr = array();

/////////////////////////////////////////////////////////////////
// 상품ID 추출을 위해 첫페이지 검색을 한다.
$result = $ti->requestFristSearchResult($argv[1]);

/////////////////////////////////////////////////////////////////
// 첫번째 결과 html 안에서 상품ID를 추출하여 array로 리턴한다.
$prdtIDarr = $ti->getPrdtIdArr($result, $pa);

/* debug product id list
	$no = sizeof($prdtIDarr);
	for ($i=0 ; $i<$no ; $i++) {
	echo $prdtIDarr[$i]."\n";
	}
 */

/////////////////////////////////////////////////////////////////
// DEBUG
//die("ERPY debug die !!!!\n");

/////////////////////////////////////////////////////////////////
// 상품정보를 더 가져오기 위해 추출한 상품ID를 이용하여 Request URI 를 생성하여 array로 리턴한다.
$arr = $ti->requestPrdtList($prdtIDarr, $argv[1]);

/* debug uri list
	$no = sizeof($arr);
	for ($i=0 ; $i<$no ; $i++) {
	echo "arr[$i] -> ".$arr[$i]."\n";
	}
 */

/////////////////////////////////////////////////////////////////
// DEBUG
//die("ERPY debug die !!!!\n");

/////////////////////////////////////////////////////////////////
// 생성된 Reqeust URI 를 이용하여 TiMon 웹서버에 상품리스트를 요청한다. 
// 결과를 Array로 반환한다.
$res = $ti->requestSearchPrdt($arr, $argv[1]);
//echo "$res\n";

/////////////////////////////////////////////////////////////////
// DEBUG
//die("ERPY debug die !!!!\n");

/////////////////////////////////////////////////////////////////
// 상품 리스트를 추출하여 array에 담아 return.
$list_arr = $pa->getList($res, $ti->list_start, $ti->list_end);

/////////////////////////////////////////////////////////////////
// array에 담긴 list에서 title, price등 item을 추출하여 array에 담아서 return.
$result_item_list_arr = $ti->parsePrdtInfo($list_arr, $pa, $ti);

////////////////////////////////////////////
// DEBUG MODE.
//die("debug die!!!!\n");

////////////////////////////////////////////
// db에 데이터를 insert 한다.
$result = $ti->putPrdtInfoToDB($result_item_list_arr, $db);

echo "total insert count --> " . $ti->total_insert_count . "\n";
echo "total skip   count --> " . $ti->total_skip_count . "\n";
echo "Process Terminated Normally...\n";

?>
