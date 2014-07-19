<?php

include "../lib/curl.class.php";
include "../lib/parser.class.php";
include "../lib/db.class.php";

class OKOutdoor {

	var $bestDealUrl = 'http://www.okoutdoor.com/product/product.html?p_category_id=B&page=';

	var $agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)';

	var $start_pos = '';
	var $end_pos = '';

	var $list_s = '<div class="os_border off">';
	var $list_e = '<div class="gift_wrap">';

	var $title_s = '<span class="prName_PrName">';
	var $title_e = '</span>';

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

	var $thumb_s     = "data-original='";
	var $thumb_e     = "' /></a>";

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

	var $total_process_count = 0;
	var $total_insert_count = 0;
	var $total_skip_count = 0;

	///////////////////////////////////////////////////////////
	public function __construct() {
		;
	}

	///////////////////////////////////////////////////////////
	public function __destruct() {
		;
	}

	///////////////////////////////////////////////////////////
	// ok outdoor 수집 기본이 되는 데이터를 읽어서 array로 리턴.
	public function fileReadToArray($filepath) {
		$crawl_list_arr = array();
		$buffer = "";

		$fp = fopen($filepath, "r") or die("$filepath : 파일열기에 실패 하였습니다!!!\n");
		while(!feof($fp)) {
			$buffer = fgets($fp);
			$data = trim($buffer);
			if (strlen($data) != 0) {
				array_push($crawl_list_arr, $buffer);
			}
		}
		fclose($fp);
		return $crawl_list_arr;
	}

	///////////////////////////////////////////////////////////
	// keyword | crawl url을 array로 받아서 db에 등록한다.
	public function keywordAndUrlInsertToDB($arr_list, $cp_name, $db) {
		$db->connect();
		foreach($arr_list as $key => $value) {
			$item = explode("|", $value);
			$t_keyword = trim($item[0]);
			$t_url     = trim($item[1]);

			$s_sql = "SELECT url FROM SOCIAL_SHOP_CRAWL_T WHERE url = '$t_url'";
			if ($db->data_exist($s_sql) == 0) { // db에 link가 없다면 insert.
				$t_sql = "INSERT INTO SOCIAL_SHOP_CRAWL_T (keyword1, keyword2, keyword3, url, cp)
					VALUES ('$t_keyword', '', '', '$t_url', '$cp_name')";
				$db->select($t_sql);
				echo "(INSERT) $t_keyword\n";
				$this->total_insert_count++;
			}
			else { // db 에 이미 link가 저장되어 있다면 skip.
				echo "#SKIP# $t_keyword\n";
				$this->total_skip_count++;
			}
			$this->total_process_count++;
		}
		$db->commit();
		//$db->close();
	}

	///////////////////////////////////////////////////////////
	// html list 추출한 데이터에서 각 item을 추출한다.
	public function parsePrdtInfo($result_list, $cp, $pa) {
		$result_item_arr = array();

		foreach ($result_list as $list) {
			//echo "================================\n";
			$result = $pa->getItem($list, $cp->title_s, $cp->title_e);
			$t_title = trim($result);
			//echo "title --> $t_title\n";

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

				//echo "brand --> $t_brand_str\n";
			}

			$result = $pa->getItem($list, $cp->cate2_s, $cp->cate2_e);
			$t_cate2 = trim($result);
			if (strcmp($t_cate2, "START_POS_NOT") == 0) {
				$t_cate2 = "";
			}
			//echo "cate2 --> $t_cate2\n";

			$result  = $pa->getItem($list, $cp->link_s, $cp->link_e);
			$t_link = "http://www.okoutdoor.com" . trim($result);
			//echo "link --> $t_link\n";

			$result  = $pa->getItem($list, $cp->thumb_s, $cp->thumb_e);
			$t_thumb = trim($result);
			//echo "thumb --> $t_thumb\n";

			$r = $pa->getItem($list, $cp->sale_per_s, $cp->sale_per_e);
			$result = str_replace(",","",$r);
			$t_sale_per = trim($result);
			//echo "sale_per --> $t_sale_per\n";

			$r  = $pa->getItem($list, $cp->org_price_s, $cp->org_price_e);
			$rr = $pa->getItem($r, $cp->org_price_sub_s, $cp->org_price_sub_e);
			$result = str_replace(",","",$rr);
			$t_org_price = trim($result);
			//echo "org_price --> $t_org_price\n";

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
					$t_sale_price_str = $t_low_price;
				}

				if (strcmp($t_low_price, "START_POS_NOT")==0) {
					$t_sale_price_str = $t_sale_price;
				}
			}
			//echo "sale_price --> $sale_price_str\n";

			$r  = $pa->getItem($list, $cp->special_price_s, $cp->special_price_e);
			$rr = $pa->getItem($r, $cp->special_price_sub_s, $cp->special_price_sub_e);
			$result = str_replace(",","",$rr);
			$t_special_price = trim($result);
			//echo "special_price --> $t_special_price\n";

			$t_sell_count = 0;

			$item_arr = array(
					"title" => "$t_title",
					"brand" => "$t_brand_str",
					"cate" => "$t_cate2",
					"link" => "$t_link",
					"thumb" => "$t_thumb",
					"sale_per" => "$t_sale_per",
					"org_price" => "$t_org_price",
					"sale_price" => "$t_sale_price_str",
					"special_price" => "$t_special_price",
					"sell_count" => "$t_sell_count",
					);

			array_push($result_item_arr, $item_arr);

		}

		return $result_item_arr;
	}


	//////////////////////////////////////////////////////////////////
	// 추출된 상품정보를 db에 insert 한다.
	public function putPrdtInfoToDB($result_item_arr, $cmt, $cp, $db) {
		$db->connect();

		$total = sizeof($result_item_arr);
		for ($i=0; $i<$total; $i++) {
			echo "=====\n";
			$tmp = $result_item_arr[$i];
			$t_title = $tmp["title"];
			$t_link = $tmp["link"];
			$s_sql = "select link from social_shop_t where link = '$t_link'";
			if ($db->data_exist($s_sql) == 0) { // db에 link가 없다면 insert.
				$t_thumb = $tmp["thumb"];
				$t_brand = $tmp["brand"];
				$t_cate  = $tmp["cate"];
				$t_sale_per  = $tmp["sale_per"];
				$t_price_org = $tmp["org_price"];
				$t_price_sale = $tmp["sale_price"];
				$t_price_special = $tmp["special_price"];
				$t_sell_count = $tmp["sell_count"];
				$t_sql = "INSERT INTO SOCIAL_SHOP_T (title, cmt, brand, link, thumb, price_org, price_sale, price_special, sale_per, sell_count, cate, cp)
					VALUES ('$t_title', '$cmt', '$t_brand', '$t_link', '$t_thumb', '$t_price_org', '$t_price_sale', '$t_price_special', '$t_sale_per', $t_sell_count, '$t_cate', 'ok')";
				$db->select($t_sql);
				echo "(INSERT) $t_title\n";
				$cp->total_insert_count++;
			}
			else { // db 에 이미 link가 저장되어 있다면 skip.
				echo "#SKIP# $t_title\n";
				$cp->total_skip_count++;
			}
			$cp->total_process_count++;
		}

		$db->commit();
		//$db->close();
	} //putPrdtInfoToDB

} // class

?>
