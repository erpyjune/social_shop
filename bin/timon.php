<?php

include "../lib/curl.class.php";
include "../lib/parser.class.php";

class TiMon {

	var $queryString1 = 'http://www.ticketmonster.co.kr/search/?keyword_view=';
	var $queryString2 = '&uis=8e07547d&sarea=g&st=0&keyword=';
	var $agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)';
	var $list_arr = array();

	var $start_pos = '<div id="deal_lst" class="wrap_deal_lst" style="margin-top:0px">';
	var $end_pos = '<div class="src_area" id="srch_search_form_btm">';

	var $list_start = '<li onmouseover="';
	var $list_end = '</li>';

	var $title_start = 'style="text-decoration:none" title="';
	var $title_end = '">';

	//var $cmt_s = '<p class="dealunit-desc">';
	//var $cmt_e = '</p>';

	var $link_s = '<a href="';
	var $link_e = '" target="_blank"';

	//var $best_s = '<div class="dealunit-best dealunit-best__no1">';
	//var $best_e = '</div>';

	var $thumb_s = 'img src="';
	var $thumb_e = '" width="';

	var $sale_per_s = '<p class="percent">';
	var $sale_per_e = '<em>';

	var $org_price_s = '정상가</span><em>';
	var $org_price_e = '</em>';

	var $sale_price_s = '할인가</span><em>';
	var $sale_price_e = '</em>';

	var $timon_price_s = '티몬가</span><em>';
	var $timon_price_e = '</em>';

	var $buy_count_s = '<span class="people"><em>';
	var $buy_count_e = '</em>';

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

$sQuery = $argv[1];

mb_internal_encoding("UTF-8");

$cp = new TiMon;
$cl = new EPCurl;
$pa = new EPParser;

// make query string...
$search_url = $cp->queryString1.$sQuery.$cp->queryString2.$sQuery;

// searching...
$body = $cl->requestGetDataFromUrl($search_url, 'GET');
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

	/*
	$result = $pa->getItem($list, $coo->cmt_s, $coo->cmt_e);
	printf("comment : (%s)\n", trim($result));
	*/

	$result = $pa->getItem($list, $cp->link_s, $cp->link_e);
	printf("link : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->thumb_s, $cp->thumb_e);
	printf("thumb : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->sale_per_s, $cp->sale_per_e);
	printf("sale per : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->sale_price_s, $cp->sale_price_e);
	printf("sale price : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->org_price_s, $cp->org_price_e);
	printf("org price : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->timon_price_s, $cp->timon_price_e);
	printf("timon price : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->buy_count_s, $cp->buy_count_e);
	printf("sell count : (%s)\n", trim($result));

	$total++;
	echo "================================\n";
}

echo "Total List : ".$total."\n";


?>
