<?php

include "../lib/timon.class.php";

///////////////////////////////////////////////////////////
mb_internal_encoding("UTF-8");

if ($argc < 2) {
	die("needs query\n");
}

$tm = new TiMonMore;
$cl = new EPCurl;
$pa = new EPParser;
$db = new EPDB;

$sQuery = urlencode($argv[1]);

/////////////////////////////////////////////////////////////////
// 검색할 요청할 Request URI를 생성.
$search_url = $tm->firstSearchUri.$sQuery.$tm->secondSearchUri.$sQuery;

/////////////////////////////////////////////////////////////////
// GET 방식으로 Request 요청. 첫페이지 검색.
$body = $cl->requestGetDataFromUrl($search_url, 'GET');

/////////////////////////////////////////////////////////////////
// 검색 결과에서 검색결과 부분만 추출.
$data = $pa->getBody($body, $tm->start_pos, $tm->end_pos);

/////////////////////////////////////////////////////////////////
// 검색결과 List 추출. array로 리턴.
$search_list_arr = $pa->getList($data, $tm->list_start, $tm->list_end);

/////////////////////////////////////////////////////////////////
// array에 담긴 list에서 title, price등 item을 추출하여 array에 담아서 return.
$result_item_list_arr = $tm->parsePrdtInfo($search_list_arr, $pa);

////////////////////////////////////////////
// db에 데이터를 insert 한다.
$result = $tm->putPrdtInfoToDB($result_item_list_arr, $db);

echo "total insert count --> " . $tm->total_insert_count . "\n";
echo "total skip   count --> " . $tm->total_skip_count . "\n";
echo "Process Terminated Normally...\n";

?>
