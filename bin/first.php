<?php

include "../lib/first.class.php";

///////////////////////////////////////////////////////////
//
if ($argc < 2) {
	die("(usage) all \n");
}

mb_internal_encoding("UTF-8");

$cp = new First;
$cl = new EPCurl;
$pa = new EPParser;
$db   = new EPDB;
$s_db = new EPDB;

///////////////////////////////////////////////////////////
// 수집할 url & keyword를 DB에서 가져온다.
$t_sql = "select keyword1, url from SOCIAL_SHOP_CRAWL_T where cp='first'";
$s_db->connect();
$result = $s_db->select($t_sql);
$result->data_seek(0);
while ($row = $result->fetch_assoc()) {
	$t_keyword1 = $row['keyword1'];
	$t_url = $row['url'];

	echo "===========================\n";
	echo "url : $t_url\n";
	echo "key : $t_keyword1\n";
	echo "===========================\n";

	$page = 1;

	for (;;) {
		///////////////////////////////////////////////////////////
		// 변수 초기화.
		$cp->total_process_count = 0;
		$cp->total_insert_count  = 0;
		$cp->total_skip_count    = 0;

		///////////////////////////////////////////////////////////
		// 검색결과 요청.
		$s_url = $t_url . "&page_no=" . $page;
		echo ">> Keyword : $t_keyword1\n";
		echo ">> Request : $s_url\n";
		$r = $cl->requestGetDataFromUrl($s_url);
		//$body = iconv("EUC-KR", "UTF-8", $r);

		///////////////////////////////////////////////////////////
		// 리스트 부분만 추출.
		$t_body = $pa->getBody($r, $cp->body_s, $cp->body_e);

		///////////////////////////////////////////////////////////
		// 수집한 검색결과에서 리스트별로 추출하여 array에 담는다.
		$search_list = $pa->getList($t_body, $cp->list_s, $cp->list_e);

		///////////////////////////////////////////////////////////
		// 검색결과 list에서 item 추출하여 array에 담당 리턴.
		$result_item = $cp->parsePrdtInfo($search_list, $cp, $pa);
		//print_r($result_item);

		///////////////////////////////////////////////////////////
		// 추출한 결과를 DB에 insert 합니다.
		$cp->putPrdtInfoToDB($result_item, $t_keyword1, $cp, $db);

		echo "total process count --> $cp->total_process_count\n";
		echo "total insert  count --> $cp->total_insert_count\n";
		echo "total skip    count --> $cp->total_skip_count\n";
		echo "url -> $s_url\n";

/* next 페이지가 없어도 결과가 나오기 때문에 first는 무조건 2 페이지까지 읽는다.
		if ($cp->total_process_count <= 29) {
			break;
		}
*/

		if ($page >= 2) {
			break;
		}

		sleep(0.35);

		$page++;
	}
}

$db->close();
$s_db->close();

/*
echo "total process count --> $cp->total_process_count\n";
echo "total insert  count --> $cp->total_insert_count\n";
echo "total skip    count --> $cp->total_skip_count\n";
*/

?>
