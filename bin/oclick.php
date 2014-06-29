<?php

include "../lib/curl.class.php";
include "../lib/parser.class.php";

class OClick {

	var $queryString1 = 'http://www.oclock.co.kr/search/search_main.jsp?searchCategory=all&searchSort=order&firstKeyword=';
	var $agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)';
	var $list_arr = array();

	var $start_pos = '<div class="deal_sort">';
	var $end_pos = '<!-- e: deal list -->';

	var $list_start = '<li id="oclock_recom_';
	var $list_end = '</li>';

	var $title_start = 'class="deal_Tit">';
	var $title_end = '</a>';

	var $cmt_s = '<span class="guideCon">';
	var $cmt_e = '</span>';

	var $link_s = '<a href="';
	var $link_e = '" class="imgBox">';

	//var $best_s = '<div class="dealunit-best dealunit-best__no1">';
	//var $best_e = '</div>';

	var $thumb_s = '<img src="';
	var $thumb_e = '" alt="';

	//var $sale_per_s = '<strong class="dealunit-type__percent">';
	//var $sale_per_e = '<span class="unit">';

	var $org_price_s = '<span class="cost"><strong>';
	var $org_price_e = '</strong>';

	//var $sale_price_s = '<span class="sale">';
	//var $sale_price_e = '<span class="won">';

	var $sell_count_s = '<span class="counter"><strong>';
	var $sell_count_e = '</strong>';

	var $delivery_s = '<span class=\'freeDeli on\'>';
	var $delivery_e = '</span>';

	var $org_data = "";
	var $list_body = "";

	///////////////////////////////////////////////////////////
	public function __construct() {
		;
   }

	///////////////////////////////////////////////////////////
   public function __destruct() {
		;
   }

} // class


///////////////////////////////////////////////////////////
if ($argc < 2) {
	die("needs query");
}

$sQuery = iconv("UTF-8", "EUC-KR", $argv[1]);

mb_internal_encoding("UTF-8");

$cp = new OClick;
$cl = new EPCurl;
$pa = new EPParser;

// make query string...
$search_url = $cp->queryString1.urlencode($sQuery);

// searching...
$s = $cl->getUrl($search_url);
$body = iconv("EUC-KR", "UTF-8", $s);
//echo $body;
// get search result body.
$data = $pa->getBody($body, $cp->start_pos, $cp->end_pos);
$search_list = $pa->getList($data, $cp->list_start, $cp->list_end);
//printf("array count : %d\n", count($coo->list_arr));

$total = 0;

foreach ($search_list as $list) {
	//echo $list."\n";
	//echo "================================\n";
	$result = $pa->getItem($list, $cp->title_start, $cp->title_end);
	printf("title : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->cmt_s, $cp->cmt_e);
	printf("comment : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->link_s, $cp->link_e);
	printf("link : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->thumb_s, $cp->thumb_e);
	printf("thumb : (%s)\n", trim($result));

	//$result = $pa->getItem($list, $cp->sale_per_s, $cp->sale_per_e);
	//printf("sale per : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->org_price_s, $cp->org_price_e);
	printf("org price : (%s)\n", trim($result));

	//$result = $pa->getItem($list, $cp->sale_price_s, $cp->sale_price_e);
	//printf("slae price : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->sell_count_s, $cp->sell_count_e);
	printf("selling count : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->delivery_s, $cp->delivery_e);
	printf("delivery : (%s)\n", trim($result));


	$total = $total + 1;
	echo "================================\n";
}

echo "Total List : ".$total."\n";


?>
