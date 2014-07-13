<?php

include "../lib/okoutdoor.class.php";

///////////////////////////////////////////////////////////
//
if ($argc < 2) {
	die("(usage) all \n");
}

mb_internal_encoding("UTF-8");

$cp = new OKOutdoor;
$cl = new EPCurl;
$pa = new EPParser;
$db   = new EPDB;
$s_db = new EPDB;

///////////////////////////////////////////////////////////
// 수집할 url & keyword를 DB에서 가져온다.
$t_sql = "select keyword1, url from SOCIAL_SHOP_CRAWL_T";
$s_db->connect();
$result = $s_db->select($t_sql);
$result->data_seek(0);
while ($row = $result->fetch_assoc()) {
	$t_keyword1 = $row['keyword1'];
	$t_url = $row['url'];

	echo ">> keyword -> $t_keyword1\n";
	echo ">> url -> $t_url\n";
	///////////////////////////////////////////////////////////
	// 검색결과 요청.
	$r = $cl->requestGetDataFromUrl($t_url);

	///////////////////////////////////////////////////////////
	// 수집한 검색결과에서 리스트별로 추출하여 array에 담는다.
	$body = iconv("EUC-KR", "UTF-8", $r);
	$search_list = $pa->getList($body, $cp->list_s, $cp->list_e);

	///////////////////////////////////////////////////////////
	// 검색결과 list에서 item 추출하여 array에 담당 리턴.
	$result_item = $cp->parsePrdtInfo($search_list, $cp, $pa);
	//print_r($result_item);

	///////////////////////////////////////////////////////////
	// 추출한 결과를 DB에 insert 합니다.
	$cp->putPrdtInfoToDB($result_item, $t_keyword1, $cp, $db);

	sleep(1.3);
}

$db->close();
$s_db->close();

echo "total process count --> $cp->total_process_count\n";
echo "total insert  count --> $cp->total_insert_count\n";
echo "total skip    count --> $cp->total_skip_count\n";

?>
