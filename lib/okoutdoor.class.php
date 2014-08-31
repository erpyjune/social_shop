<?php

include "../lib/curl.class.php";
include "../lib/parser.class.php";
include "../lib/db.class.php";

class OKOutdoor {

	var $CP_NAME = 'ok';
	var $list_s = '<div class="os_border off">';
	var $list_e = '<div class="gift_wrap">';

	var $title_s = '';
	var $title_e = '';

	var $brand1_s = '';
	var $brand1_e = '';

	var $brand2_s = '';
	var $brand2_e = '';

	var $cate1_s = '';
	var $cate1_e = '';

	var $cate2_s = '';
	var $cate2_e = '';

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
	// 수집 기본이 되는 데이터를 읽어서 array로 리턴.
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
		$doc = new DOMDocument();

		foreach ($result_list as $list) {

			$t_title = '';
			$t_link  = '';
			$t_thumb = '';
			$t_sale_per = '';
			$t_org_price = '';
			$t_sale_price = '';
			$t_brand_str = '';
			$t_cate2 = '';
			$t_special_price = '';
			$t_sell_count = 0;

			libxml_use_internal_errors(true);
			$doc->loadHTML($list);
			libxml_clear_errors();

			$xpath = new DOMXPath($doc);

			// title...
			$tt = '';
			$nodelist = $xpath->query( "//span[@class='prName_PrName']" );
			foreach ($nodelist as $n){
				$s = utf8_decode($n->nodeValue);
				$tt = $s;
				//echo "title -> " . $s . "\n";
			}

			$brand = '';
			$nodelist = $xpath->query( "//span[@class='prName_Brand']" );
			foreach ($nodelist as $n){
				$s = utf8_decode($n->nodeValue);
				$brand = $s;
				//echo "brand -> " . $s . "\n";
			}

			$t_title = $brand . ' ' . $tt;
			echo "title -> " . $t_title . "\n";


			// link...
			$nodelist = $xpath->query( "//p[@class='item_title']/a/@href" );
			foreach ($nodelist as $n){
				$s = utf8_decode($n->nodeValue);
				$t_link = $s;
				echo "link -> " . "http://www.okmall.com" . $s."\n";
			}


			// thumb...
			$nodelist = $xpath->query( "//div[@class='item_img']/a/img/@data-original" );
			foreach ($nodelist as $n){
				$s = utf8_decode($n->nodeValue);
				$t_thumb = $s;
				echo "thumb -> " . $s . "\n";
			}

			// org price...
			$nodelist = $xpath->query( "//div[@class='real_price01 ']/span[@class='r ']" );
			foreach ($nodelist as $n){
				$s = utf8_decode($n->nodeValue);
				$r = str_replace("원", "", $s);
				$s = str_replace(",", "", $r);
				$r = str_replace("\r", "", $s);
				$s = str_replace("\n", "", $r);
				$t_org_price = $s;
				echo "org_price -> " . $s."\n";
			}


			// sale price...
			$nodelist = $xpath->query( "//div[@class='real_price03 f_c16 fb']/span[@class='r ']" );
			foreach ($nodelist as $n){
				$s = utf8_decode($n->nodeValue);
				$r = str_replace("원", "", $s);
				$s = str_replace(",", "", $r);
				$r = str_replace("\r", "", $s);
				$s = str_replace("\n", "", $r);
				$t_sale_price = $s;
				echo "sale_price -> " . $s."\n";
			}


			$item_arr = array(
					"title" => "$t_title",
					"brand" => "$t_brand_str",
					"cate" => "$t_cate2",
					"link" => "$t_link",
					"thumb" => "$t_thumb",
					"sale_per" => "$t_sale_per",
					"org_price" => "$t_org_price",
					"sale_price" => "$t_sale_price",
					"special_price" => "$t_special_price",
					"sell_count" => "$t_sell_count",
					);

			array_push($result_item_arr, $item_arr);

		}

		return $result_item_arr;
	}


	//////////////////////////////////////////////////////////////////
	// 추출된 상품정보를 db에 insert 한다.
	public function putPrdtInfoToDB($result_item_arr, $cmt, $cp, $db, $crawl_url) {

		$now_timestamp = time();
		$reg_datetime = date("Ymdhis",$now_timestamp);

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
				$t_sql = "INSERT INTO SOCIAL_SHOP_T (title, cmt, brand, link, thumb, price_org, price_sale, price_special, sale_per, sell_count, cate, crawl_url, in_timestamp, cp)
					VALUES ('$t_title', '$cmt', '$t_brand', '$t_link', '$t_thumb', '$t_price_org', '$t_price_sale', '$t_price_special', '$t_sale_per', $t_sell_count, '$t_cate', '$crawl_url', $reg_datetime, $this->CP_NAME)";
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
