<?php

include "../lib/okoutdoor.class.php";

$seed_url_arr = array(
						"http://www.okoutdoor.com/product/product.html?p_category_id=T&page=",
						"http://www.okoutdoor.com/product/product.html?p_category_id=B&page=",
						"http://www.okoutdoor.com/product/product.html?p_category_id=D&page=",
						
/*
COOLER="http://www.okoutdoor.com/product/product.html?p_category_id=T&page="
SHOES="http://www.okoutdoor.com/product/product.html?p_category_id=B&page="
BAG="http://www.okoutdoor.com/product/product.html?p_category_id=D&page="
SLEEPING="http://www.okoutdoor.com/product/product.html?p_category_id=U&page="
TENT="http://www.okoutdoor.com/product/product.html?p_category_id=F&page="
MAT="http://www.okoutdoor.com/product/product.html?p_category_id=V&page="
LANTERN="http://www.okoutdoor.com/product/product.html?p_category_id=E&page="
STOVE="http://www.okoutdoor.com/product/product.html?p_category_id=W&page="
COPEL="http://www.okoutdoor.com/product/product.html?p_category_id=Z&page="
KNIFE="http://www.okoutdoor.com/product/product.html?p_category_id=I&page="
WATCH="http://www.okoutdoor.com/product/product.html?p_category_id=K&page="
GLASS="http://www.okoutdoor.com/product/product.html?p_category_id=L&page="
EAT="http://www.okoutdoor.com/product/product.html?p_category_id=7&page="
*/

///////////////////////////////////////////////////////////
//
if ($argc < 3) {
	die("(usage) page_num request_url\n");
}

mb_internal_encoding("UTF-8");

$cp = new OKOutdoor;
$cl = new EPCurl;
$pa = new EPParser;
$db = new EPDB;

///////////////////////////////////////////////////////////
// make request url.
$page_num = $argv[1];
$req_url  = $argv[2];
$url      = $req_url . $page_num;

///////////////////////////////////////////////////////////
// 검색결과 요청.
$r = $cl->requestGetDataFromUrl($url);

///////////////////////////////////////////////////////////
// 수집한 검색결과에서 리스트별로 추출하여 array에 담는다.
$body = iconv("EUC-KR", "UTF-8", $r);
$search_list = $pa->getList($body, $cp->list_s, $cp->list_e);
//print_r($search_list);

///////////////////////////////////////////////////////////
// 검색결과 list에서 item 추출하여 array에 담당 리턴.
$result_item = $cp->parsePrdtInfo($search_list, $cp, $pa);
//print_r($result_item);

///////////////////////////////////////////////////////////
// 검색결과 list에서 item 추출하여 array에 담당 리턴.
$cp->putPrdtInfoToDB($result_item, $cp, $db);

echo "total process count --> $cp->total_process_count\n";
echo "total insert  count --> $cp->total_insert_count\n";
echo "total skip    count --> $cp->total_skip_count\n";

?>
