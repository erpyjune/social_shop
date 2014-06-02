<?php

include "curl.class.php";
include "parser.class.php";

class WeMaf {

	var $queryString1 = 'http://www.wemakeprice.com/search?search_cate=top&search_keyword=';
	var $queryString2 = '';
	var $agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)';
	var $list_arr = array();

	var $start_pos = '<h4 class="searched_cate">';
	var $end_pos   = 'var _is_load_complete = 0';

	var $list_start = '<li class=" " item_id="';
	var $list_end   = '</li>';

	var $title_start = '<strong class="tit_desc">';
	var $title_end = '</strong>';

	var $cmt_s = '<span class="standardinfo">';
	var $cmt_e = '</span>';

	var $link_s = '<a href="';
	var $link_e = '"  id="';

	//var $best_s = '<div class="dealunit-best dealunit-best__no1">';
	//var $best_e = '</div>';

	var $thumb_s = '<img src="';
	var $thumb_e = '" alt="';

	var $sale_per_s = '<strong class="dealunit-type__percent">';
	var $sale_per_e = '<span class="unit">';

	var $org_price_s = '<span class="prime">';
	var $org_price_e = '<span class="';

	var $sale_price_s = '<span class="sale">';
	var $sale_price_e = '<span class="won">';

	var $sell_count_s = '<span class="txt_num"><strong class="point">';
	var $sell_count_e = '</strong>';

	var $delivery_start = '<span class="ye">';
	var $delivery_end   = '</span>';

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

$cp = new WeMaf;
$cl = new EPCurl;
$pa = new EPParser;

// make query string...
$search_url = $cp->queryString1.urlencode($sQuery);

// searching...
echo ">>> Search URL : ".$search_url."\n";
$body = $cl->getUrl($search_url);
echo "$body\n";
$data = $pa->getBody($body, $cp->start_pos, $cp->end_pos);
//echo "$data\n";
$search_list = $pa->getList($data, $cp->list_start, $cp->list_end);
//printf("array count : %d\n", count($coo->list_arr));

$total = 0;

foreach ($search_list as $list) {
	$result = $pa->getItem($list, $cp->title_start, $cp->title_end);
	printf("title : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->cmt_s, $cp->cmt_e);
	printf("comment : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->link_s, $cp->link_e);
	printf("link : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->thumb_s, $cp->thumb_e);
	printf("thumb : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->sale_per_s, $cp->sale_per_e);
	printf("sale per : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->org_price_s, $cp->org_price_e);
	printf("org price : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->sale_price_s, $cp->sale_price_e);
	printf("slae price : (%s)\n", trim($result));

	$result = $pa->getItem($list, $cp->sell_count_s, $cp->sell_count_e);
	printf("selling count : (%s)\n", trim($result));

	$total = $total + 1;
	echo "================================\n";
}

echo "Total List : ".$total."\n";


?>
