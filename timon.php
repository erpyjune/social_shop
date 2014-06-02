<?php

include "curl.class.php";
include "parser.class.php";

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
	var $title_end = '</a>';

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

	/*
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
	*/

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
$body = $cl->getUrl($search_url, 'GET');
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

	/*
	$s = $pa->getItem($list, $coo->org_price_s, $coo->org_price_e);
	$result = $pa->getItem($s, $coo->sub_org_price_s, $coo->sub_org_price_e);
	printf("org price : (%s)\n", trim($result));

	$s = $pa->getItem($list, $coo->sale_price_s, $coo->sale_price_e);
	$result = $pa->getItem($s, $coo->sub_sale_price_s, $coo->sub_sale_price_e);
	printf("slae price : (%s)\n", trim($result));

	$s = $pa->getItem($list, $coo->buy_count_s, $coo->buy_count_e);
	$result = $pa->getItem($s, $coo->sub_buy_count_s, $coo->sub_buy_count_e);
	printf("selling count : (%s)\n", trim($result));
	*/
	$total = $total + 1;
	echo "================================\n";
}

echo "Total List : ".$total."\n";


?>
