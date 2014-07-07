<?php

include "../lib/curl.class.php";
include "../lib/parser.class.php";
include "../lib/db.class.php";

class OClick {

	var $bestDealUrl = 'http://www.okoutdoor.com/product/product.html?p_category_id=B&page=';

	var $agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)';

	var $start_pos = '';
	var $end_pos = '';

	var $title_s = '<span class="prName_PrName">';
	var $title_e = '</span>';

	var $list_s = '<p class="item_title" name="shortProductName">';
	var $list_e = '<div class="val_top last">';
	//var $list_e = '<div class="gift_wrap">';

	var $brand1_s = '<span class="prName_Brand">';
	var $brand1_e = '</span>';

	var $brand2_s = '<p class="brand_name">브랜드명:';
	var $brand2_e = '</p>';

	var $cate1_s = '<div class="AttrIcon5">';
	var $cate1_e = '</div>';

	var $cate2_s = '<dd>';
	var $cate2_e = '</dd>';

	var $link_s = 'href="';
	var $link_e = '"';

	var $thumb_s     = '<!-- 상품이미지 -->';
	var $thumb_sub_s = "data-original='";
	var $thumb_sub_e = "'";
	var $thumb_e     = '</a>';

	var $sale_per_s = '<p class="icon">';
	var $sale_per_e = '%</p>';

	var $org_price_s = '<span class="l">정찰 판매가</span>';
	var $org_price_sub_s = '<span class="r ">';
	var $org_price_sub_e = '원</span>';
	var $org_price_e = '</div>';

	var $sale_price_s = '할인가 : </label><strong>';
	var $sale_price_sub_s = '<span class="r ">';
	var $sale_price_sub_e = '원<a name="viewPrice"';
	var $sale_price_e = '</strong>';

	var $low_price_s = '<span class="l">국내 최저가</span>';
	var $low_price_sub_s = '<span class="r ">';
	var $low_price_sub_e = '원<a name="viewPrice"';
	var $low_price_e = '</span>';

	var $special_price_s = '<span class="l">우수회원 최대 할인가</span>';
	var $special_price_sub_s = '<span class="r ">';
	var $special_price_sub_e = '원<a name="viewPrice"';
	var $special_price_e = '</div>';

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
	die("needs page num!!\n");
}

$sQuery = urlencode($argv[1]);
mb_internal_encoding("UTF-8");

$cp = new OClick;
$cl = new EPCurl;
$pa = new EPParser;
$db = new EPDB;

// searching...
$getUrl = $cp->bestDealUrl . $sQuery;
$r = $cl->requestGetDataFromUrl($getUrl);
$body = iconv("EUC-KR", "UTF-8", $r);
$search_list = $pa->getList($body, $cp->list_s, $cp->list_e);

////////////////////////////////////////////
// db connect
//$db->connect();

$total_proc_count = 0;
$total_insert_count = 0;
$total_skip_count = 0;

foreach ($search_list as $list) {
	//echo "================================\n";
	$result = $pa->getItem($list, $cp->title_s, $cp->title_e);
	$t_title = trim($result);
	echo "title --> $t_title\n";

	$result = $pa->getItem($list, $cp->brand1_s, $cp->brand1_e);
	$t_brand1 = trim($result);
	//echo "brand1 --> $t_brand1\n";

	$result = $pa->getItem($list, $cp->brand2_s, $cp->brand2_e);
	$t_brand2 = trim($result);
	//echo "brand2 --> $t_brand2\n";
	{
		$t_brand_str = "";
		if (strcmp($t_brand1, "START_POS_NOT")!=0) {
			$t_brand_str = $t_brand1;
		}

		if (strcmp($t_brand2, "START_POS_NOT")!=0) {
			$t_brand_str = $t_brand_str . " " . $t_brand2;
		}

		echo "brand --> $t_brand_str\n";
	}

	$result = $pa->getItem($list, $cp->cate2_s, $cp->cate2_e);
	$t_cate2 = trim($result);
	echo "cate2 --> $t_cate2\n";

	$result  = $pa->getItem($list, $cp->link_s, $cp->link_e);
	$t_link = trim($result);
	echo "link --> $t_link\n";

	$r      = $pa->getItem($list, $cp->thumb_s, $cp->thumb_e);
	$result = $pa->getItem($r, $cp->thumb_sub_s, $cp->thumb_sub_e);
	$t_thumb = trim($result);
	echo "thumb --> $t_thumb\n";

	$r = $pa->getItem($list, $cp->sale_per_s, $cp->sale_per_e);
	$result = str_replace(",","",$r);
	$t_sale_per = trim($result);
	echo "sale_per --> $t_sale_per\n";

	$r  = $pa->getItem($list, $cp->org_price_s, $cp->org_price_e);
	$rr = $pa->getItem($r, $cp->org_price_sub_s, $cp->org_price_sub_e);
	$result = str_replace(",","",$rr);
	$t_org_price = trim($result);
	echo "org_price --> $t_org_price\n";

	$r  = $pa->getItem($list, $cp->sale_price_s, $cp->sale_price_e);
	$rr = $pa->getItem($r, $cp->sale_price_sub_s, $cp->sale_price_sub_e);
	$result = str_replace(",","",$rr);
	$t_sale_price = trim($result);
	//echo "sale_price --> $t_sale_price\n";

	$r  = $pa->getItem($list, $cp->low_price_s, $cp->low_price_e);
	$rr = $pa->getItem($r, $cp->low_price_sub_s, $cp->low_price_sub_e);
	$result = str_replace(",","",$rr);
	$t_low_price = trim($result);
	//echo "low_price --> $t_low_price\n";

	$sale_price_str = "";
	{
		if (strcmp($t_sale_price, "START_POS_NOT")==0) {
			$sale_price_str = $t_low_price;
		}

		if (strcmp($t_low_price, "START_POS_NOT")==0) {
			$sale_price_str = $t_sale_price;
		}
	}
	echo "sale_price --> $sale_price_str\n";

	$r  = $pa->getItem($list, $cp->special_price_s, $cp->special_price_e);
	$rr = $pa->getItem($r, $cp->special_price_sub_s, $cp->special_price_sub_e);
	$result = str_replace(",","",$rr);
	$t_special_price = trim($result);
	echo "special_price --> $t_special_price\n";

/*
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
*/

	$total_proc_count++;
	echo "================================\n";
}

//$db->commit();
//$db->close();

echo "total proc   count --> " . $total_proc_count . "\n";
echo "total insert count --> " . $total_insert_count . "\n";
echo "total skip   count --> " . $total_skip_count . "\n";

?>
