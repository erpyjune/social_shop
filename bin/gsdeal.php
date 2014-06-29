<?php

include "../lib/curl.class.php";
include "../lib/parser.class.php";
include "../lib/db.class.php";

class OClick {

	var $bestDealUrl = 'http://www.gsshop.com/deal/dealListSub.gs?kind=all&today=';
	var $recommandDealUrl = 'http://www.gsshop.com/deal/dealListSub.gs?kind=rcmd&today=';
	var $todayDealUrl = 'http://www.gsshop.com/deal/dealListSub.gs?kind=new&today=';
	var $endDealUrl = 'http://www.gsshop.com/deal/dealListSub.gs?kind=end&today=';

	var $link_url = 'http://www.gsshop.com/deal/deal.gs?dealNo=';
	var $agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)';
	var $list_arr = array();

	var $start_pos = '<ul id="deal_list" class="clearfix on">';
	var $end_pos = '<!-- //wrap -->';

	var $list_start = '<li ';
	var $list_end = '</li>';

	var $title_start = '<dd class="tit">';
	var $title_end = '</dd>';

	var $cmt_s = '<dt>';
	var $cmt_e = '</dt>';

	var $link_s = '<a href="javascript:goLink(\'deal\',\'';
	var $link_e = '\', \'';

	//var $best_s = '<div class="dealunit-best dealunit-best__no1">';
	//var $best_e = '</div>';

	var $thumb_s = '<img src="';
	var $thumb_e = '" alt="';

	//var $sale_per_s = '<span class="sale">';
	//var $sale_per_e = '<span>';

	//var $org_price_s = '<span class="p_tit">정가</span>';
	//var $org_price_e = '원</s>';

	var $sale_price_s = '할인가 : </label><strong>';
	var $sale_price_e = '</strong>';

	var $sell_count_s = '<dd class="cnt"><strong>';
	var $sell_count_e = '</strong>';

	//var $delivery_s = '<div class="ico_freedlv">';
	//var $delivery_e = '</div>';

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

//$sQuery = iconv("UTF-8", "EUC-KR", $argv[1]);
$sQuery = urlencode($argv[1]);

mb_internal_encoding("UTF-8");

$cp = new OClick;
$cl = new EPCurl;
$pa = new EPParser;
$db = new EPDB;

// searching...
$s_best = $cl->requestGetDataFromUrl($cp->bestDealUrl);
$s_recom = $cl->requestGetDataFromUrl($cp->recommandDealUrl);
$s_today = $cl->requestGetDataFromUrl($cp->todayDealUrl);
$s_end = $cl->requestGetDataFromUrl($cp->endDealUrl);

$merge_data = $s_best." ".$s_recom." ".$s_today." ".$s_end;

$body = iconv("EUC-KR", "UTF-8", $merge_data);
$search_list = $pa->getList($body, $cp->list_start, $cp->list_end);

////////////////////////////////////////////
// db connect
$db->connect();

$total_insert_count = 0;
$total_skip_count = 0;

foreach ($search_list as $list) {
	//echo "================================\n";
	$result = $pa->getItem($list, $cp->title_start, $cp->title_end);
	$t_title = trim($result);

	$result = $pa->getItem($list, $cp->cmt_s, $cp->cmt_e);
	$t_cmt1 = trim($result);
	if (mb_strlen($t_title) == 0) {
		$t_title = $t_title." ".$t_cmt1;
//		printf("title : (%s)\n", $t_title);
	} else {
//		printf("cmt : (%s)\n", $t_cmt1);
	}

	$result = $pa->getItem($list, $cp->link_s, $cp->link_e);
	$t_link = trim($result);
//	printf("link : (%s%s)\n", $cp->link_url, $t_link);

	$result = $pa->getItem($list, $cp->sell_count_s, $cp->sell_count_e);
	$t_sell_count = trim($result);
//	printf("selling count : (%s)\n", $t_sell_count);

	$result = $pa->getItem($list, $cp->thumb_s, $cp->thumb_e);
	$t_thumb = trim($result);
//	printf("thumb : (%s)\n", $t_thumb);

	$result = $pa->getItem($list, $cp->sale_price_s, $cp->sale_price_e);
	$t_price_sale = trim($result);
//	printf("slae price : (%s)\n", $t_price_sale);

   $s_sql = "select link from social_shop_t where link = '$t_link'";
   if ($db->data_exist($s_sql) == 0) {
		$t_price_org = '0';
		$t_sale_per = '0';
      $t_sql = "INSERT INTO SOCIAL_SHOP_T (title, cmt1, link, thumb, price_org, price_sale, sale_per, sell_count, cp)
         VALUES ('$t_title', '$t_cmt1', '$t_link', '$t_thumb', '$t_price_org', '$t_price_sale', '$t_sale_per', $t_sell_count, 'gs')";
      $db->select($t_sql);
      echo "(INSERT) $t_title\n";
      $total_insert_count++;
   }
   else {
      echo "#SKIP# $t_title\n";
      $total_skip_count++;
   }
	echo "================================\n";
}

$db->commit();
$db->close();

echo "total insert count --> " . $total_insert_count . "\n";
echo "total skip   count --> " . $total_skip_count . "\n";

?>
