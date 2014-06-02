<?php

include "curl.class.php";
include "parser.class.php";
include "db.class.php";

class CooPangExtract {

	var $cooPangUrl = 'http://www.coupang.com/search.pang?q=';
	var $agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)';
	var $list_arr = array();

	var $start_pos = '<div class="dealList">';
	var $end_pos = '<ul id="personalization">';

	var $list_start = '<li>';
	var $list_end = "</li>";

	var $title_start = '<p class="dealunit-title">';
	var $title_end = '</p>';

	var $cmt_s = '<p class="dealunit-desc">';
	var $cmt_e = '</p>';

	var $link_s = '<a class="dealunit-link" href="';
	var $link_e = '" data-cclick="Search';

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

mb_internal_encoding("UTF-8");

$coo  = new CooPangExtract;
$curl = new EPCurl;
$pa   = new EPParser;
$db   = new EPDB;

$sQuery = $argv[1];
$search_url = $coo->cooPangUrl.$sQuery;
$rr = $curl->getUrl($search_url, 'GET');
$data = $pa->getBody($rr, $coo->start_pos, $coo->end_pos);
$search_list = $pa->getList($data, $coo->list_start, $coo->list_end);
//printf("array count : %d\n", count($coo->list_arr));


$conn = $db->connect();

$total = 0;
$skip  = 0;

foreach ($search_list as $list) {
	//echo $list."\n";
	//echo "================================\n";
	$result = $pa->getItem($list, $coo->best_s, $coo->best_e);
	printf("best : (%s)\n", trim($result));

	$result = $pa->getItem($list, $coo->title_start, $coo->title_end);
	$t_title = $result;
	printf("title : (%s)\n", trim($result));

	$result = $pa->getItem($list, $coo->cmt_s, $coo->cmt_e);
	$t_cmt1 = $result;
	printf("comment : (%s)\n", trim($result));

	$result = $pa->getItem($list, $coo->link_s, $coo->link_e);
	$t_link = $result;
	printf("link : (%s)\n", trim($result));

	$result = $pa->getItem($list, $coo->thumb_s, $coo->thumb_e);
	$t_thumb = $result;
	printf("thumb : (%s)\n", trim($result));

	$result = $pa->getItem($list, $coo->sale_per_s, $coo->sale_per_e);
	$t_sale_per = $result;
	printf("sale per : (%s)\n", trim($result));

	$s = $pa->getItem($list, $coo->org_price_s, $coo->org_price_e);
	$result = $pa->getItem($s, $coo->sub_org_price_s, $coo->sub_org_price_e);
	$t_price_org = $result;
	printf("org price : (%s)\n", trim($result));

	$s = $pa->getItem($list, $coo->sale_price_s, $coo->sale_price_e);
	$result = $pa->getItem($s, $coo->sub_sale_price_s, $coo->sub_sale_price_e);
	$t_price_sale = $result;
	printf("slae price : (%s)\n", trim($result));

	$s = $pa->getItem($list, $coo->buy_count_s, $coo->buy_count_e);
	$result = $pa->getItem($s, $coo->sub_buy_count_s, $coo->sub_buy_count_e);
	$t_sell_count = $result;
	printf("selling count : (%s)\n", trim($result));

	$s_sql = "select link from social_shop_t where link = '$t_link'";
	echo "(EXIST_CHECK) $s_sql\n";
	if ($db->data_exist($conn, $s_sql) == 0) {
		$t_sql = "INSERT INTO SOCIAL_SHOP_T (title, cmt1, link, thumb, price_org, price_sale, sale_per, sell_count) 
			VALUES ('$t_title', '$t_cmt1', '$t_link', '$t_thumb', '$t_price_org', '$t_price_sale', '$t_sale_per', $t_sell_count)";
		echo $t_sql."\n";
		$db->select($conn, $t_sql);
		echo "(INSERT) $t_link\n";
		$db->commit($conn);
		$total = $total + 1;
	}
	else {
		echo "(SKIP) $t_link\n";
		$skip = $skip + 1;
	}
}

echo ">>> inset count : ".$total."\n";
echo ">>> skip  count : ".$skip."\n";

$db->commit($conn);
$db->close($conn);

?>
