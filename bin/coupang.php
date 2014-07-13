<?php
include "../lib/curl.class.php";
include "../lib/parser.class.php";
include "../lib/db.class.php";

class Coupang {
	var $moreSearchUrl = 'http://www.coupang.com/getMoreDeal.pang';
	var $firstSearchUri = 'http://www.coupang.com/search.pang?q=';
	var $moreUri = 'http://www.coupang.com/getMoreDeal.pang?uri=/search.pang&q=';

	var $prdid_stag = 'pageJson = jQuery.parseJSON';
	var $prdid_etag = 'dealProvider = new DealProvider';
	var $prdid_sub_stag = '[[';
	var $prdid_sub_etag = ']]}';

	var $h_host 	= "Host: www.coupang.com";
	var $h_referer = "Referer: http://www.coupang.com/search.pang?q=%s";
	var $h_origin 	= "Origin: http://www.coupang.com";
	var $h_request = "X-Requested-With: XMLHttpRequest";
	var $h_length 	= "Content-Length: %d";
	var $h_uri    	= "uri: /search.pang&q=%s";
	var $h_agent  	= "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36";

	// parser tag list.
   var $list_start = '<li>';
   var $list_end = "</li>";

   var $start_pos = '<div class="dealList">';
   var $end_pos = '<ul id="personalization">';

   var $title_start = '<p class="dealunit-title">';
   var $title_end = '</p>';

   var $cmt_s = '<div class="dealunit-price-desc" title="';
   var $cmt_e = '">';

   var $link_s = '<a class="dealunit-link" href="';
   var $link_e = '" data-cclick="';

   var $best_s = '<div class="dealunit-best dealunit-best__no1">';
   var $best_e = '</div>';

   var $thumb_s = '<img src="';
   var $thumb_e = '" width="';

   var $sale_per_s = '<strong class="dealunit-type__percent">';
   var $sale_per_e = '<span class="unit">';

   var $org_price_s = '<div class="dealunit-price-originalvalue">';
   var $sub_org_price_s = '<del>';
   var $sub_org_price_e = '</del><span class="unit">';
   var $org_price_e = '</div>';

   var $sale_price_s = '<div class="dealunit-price-value">';
   var $sub_sale_price_s = '<strong>';
   var $sub_sale_price_e = '</strong><span class="unit">';
   var $sale_price_e = '</div>';

   var $buy_count_s = '<div class="dealunit-buyinfo">';
   var $sub_buy_count_s = '<em class="dealunit-buyinfo-count">';
   var $sub_buy_count_e = '</em>';
   var $buy_count_e = '</div>';


	// 상품 리스트를 받기 위한 첫 검색결과 호출
	public function requestFristSearchResult($query) {
		$curl = new EPCurl;
		$searchQuery = $this->firstSearchUri.urlencode($query);
		$r = $curl->requestGetDataFromUrl($searchQuery, 'GET');
		return $r;
	}

// 검색결과에서 상품ID를 추출하고 이를 array로 받는다
public function getPrdtIdArr($r) {
	$pa   = new EPParser;
	$r2 = $pa->getBody($r, $this->prdid_stag, $this->prdid_etag);
	$result = $pa->getBody($r2, $this->prdid_sub_stag, $this->prdid_sub_etag);
	$arr = explode(",", $result);
	$r = str_replace("[", "", $arr);
	$r2 = str_replace("]", "", $r);

	return $r2;
}

// array로 받은 상품ID를 {10}건 단위로 요청 uri를 생성하여 array로 리턴한다.
public function requestPrdtList($prdtListArr) {
	$a = array();
	$list = '';
	$total = sizeof($prdtListArr);

	for ($i=0;$i<$total;$i++) {
		$list = $list.'coupangSrls%5B%5D='.$prdtListArr[$i].'&';
		if ($i % 11 == 0 && $i != 0) {
			array_push($a, $list);
			$list = '';
		}
	}

	array_push($a, $list);

	return $a;
}

// prdt id list를 쿠팡에 요청하여 결과 html을 받는다.
public function requestSearchPrdt($prdtArr, $query) {
	$curl = new EPCurl;
	$res = '';

	$total = sizeof($prdtArr);

	for ($i=0; $i<$total; $i++) {
		//echo "prdtArr[$i] -> ".$prdtArr[$i]."\n";
		$headers_arr = array();
		$ss = sprintf($this->h_length, strlen($prdtArr[$i]));
		array_push($headers_arr, $ss);
		$ss = sprintf($this->h_uri, urlencode($query));
		array_push($headers_arr, $ss);
		array_push($headers_arr, $this->h_host);
		array_push($headers_arr, $this->h_referer);
		array_push($headers_arr, $this->h_origin);
		array_push($headers_arr, $this->h_request);
		array_push($headers_arr, $this->h_agent);

		echo ">>>>> request : ".$prdtArr[$i]."\n";
		$data = $curl->requestPostDataFromUrl($this->moreSearchUrl, $prdtArr[$i], $headers_arr);
		sleep(1.7);
		$res = $res." ".$data;
		//echo $result;
	}
	return $res;
}


// 상품정보 array에서 하나씩 추출하여 상품정보를 추출하여 이를 array에 저장한다.
public function parsePrdtInfo($data) {

}

// 추출된 상품정보를 db에 insert 한다.
public function putPrdtInfoToDB() {

}
}


/////////////////////// main //////////////////////////////////
if ($argc < 2) {
	die ("(usage) query\n");
}

$coo  = new Coupang;
$pa   = new EPParser;
$cu   = new EPCurl;
$db   = new EPDB;
$result_item_arr = array();

$result = $coo->requestFristSearchResult($argv[1]);
$prdtIDarr = $coo->getPrdtIdArr($result);
$no = sizeof($prdtIDarr);
/*
	for ($i=0 ; $i<$no ; $i++) {
	echo $prdtIDarr[$i]."\n";
	}
 */
$arr = $coo->requestPrdtList($prdtIDarr);
/*
	$no = sizeof($arr);
	for ($i=0 ; $i<$no ; $i++) {
	echo "arr[$i] -> ".$arr[$i]."\n";
	}
*/
$res = $coo->requestSearchPrdt($arr, $argv[1]);
/*
echo "$res";
*/
$list_arr = $pa->getList($res, $coo->list_start, $coo->list_end);

////////////////////////////////////////////
// parse data & make array list
$total = sizeof($list_arr);
for ($i = 0; $i<$total; $i++) {
	echo "============================================\n";
	$list = $list_arr[$i];
	//echo "$list\n";

	// get title
   $result = $pa->getItem($list, $coo->title_start, $coo->title_end);
   $t_title = trim($result);
   printf("title : (%s)\n", $result);

   $result = $pa->getItem($list, $coo->cmt_s, $coo->cmt_e);
   $t_cmt1 = trim($result);
   printf("comment : (%s)\n", $result);

   $result = $pa->getItem($list, $coo->sale_per_s, $coo->sale_per_e);
   $t_sale_per = trim($result);
   printf("sale per : (%s)\n", $t_sale_per);

   $s = $pa->getItem($list, $coo->org_price_s, $coo->org_price_e);
   $result = $pa->getItem($s, $coo->sub_org_price_s, $coo->sub_org_price_e);
   $t_price_org = trim($result);
   printf("org price : (%s)\n", $result);

   $s = $pa->getItem($list, $coo->sale_price_s, $coo->sale_price_e);
   $result = $pa->getItem($s, $coo->sub_sale_price_s, $coo->sub_sale_price_e);
   $t_price_sale = trim($result);
   printf("slae price : (%s)\n", $result);

   $s = $pa->getItem($list, $coo->buy_count_s, $coo->buy_count_e);
   $result = $pa->getItem($s, $coo->sub_buy_count_s, $coo->sub_buy_count_e);
   $t_sell_count = trim($result);
   printf("selling count : (%s)\n", $result);

   $result = $pa->getItem($list, $coo->thumb_s, $coo->thumb_e);
   $t_thumb = trim($result);
   printf("thumb : (%s)\n", $result);

   $result = $pa->getItem($list, $coo->link_s, $coo->link_e);
   $t_link = trim($result);
   printf("link : (%s)\n", $result);

	$item_arr = array("title" => "$t_title",
							"cmt1" => "$t_cmt1",
							"sale_per" => "$t_sale_per",
							"sale_price" => "$t_price_sale",
							"org_price" => "$t_price_org",
							"sell_count" => "$t_sell_count",
							"thumb" => "$t_thumb",
							"link" => "$t_link",
					);

	array_push($result_item_arr, $item_arr);
}


////////////////////////////////////////////
// DEBUG MODE.
//die("debug die!!!!\n");

////////////////////////////////////////////
// db connect
$db->connect();

////////////////////////////////////////////
// insert to dbms
$total_insert_count = 0;
$total_skip_count = 0;
$total = sizeof($result_item_arr);
for ($i=0; $i<$total; $i++) {
	echo "=====\n";
	$tmp = $result_item_arr[$i];
	$t_title = $tmp["title"];
	$t_link = $tmp["link"];
	$s_sql = "select link from social_shop_t where link = '$t_link'";
	if ($db->data_exist($s_sql) == 0) {
		$t_thumb = $tmp["thumb"];
		$t_cmt1 = $tmp["cmt1"];
		$t_price_org = $tmp["org_price"];
		$t_price_sale = $tmp["sale_price"];
		$sale_per = $tmp["sale_per"];
		$t_sell_count = $tmp["sell_count"];
		$t_sql = "INSERT INTO SOCIAL_SHOP_T (title, cmt, link, thumb, price_org, price_sale, sale_per, sell_count, cp)
			VALUES ('$t_title', '$t_cmt1', '$t_link', '$t_thumb', '$t_price_org', '$t_price_sale', '$t_sale_per', $t_sell_count, 'coupang')";
		$db->select($t_sql);
		echo "(INSERT) $t_title\n";
		$total_insert_count++;
	}
	else {
		echo "(SKIP) $t_title\n";
		$total_skip_count++;
	}
}

$db->commit();
$db->close();

echo "total insert count --> " . $total_insert_count . "\n";
echo "total skip   count --> " . $total_skip_count . "\n";

?>
