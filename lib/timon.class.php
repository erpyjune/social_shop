<?php
include "curl.class.php";
include "parser.class.php";
include "db.class.php";

class TiMonMore {
	var $moreSearchUrl = 'http://www.ticketmonster.co.kr/search/getDealsContents';
	var $firstSearchUri = 'http://www.ticketmonster.co.kr/search/?keyword_view=';
	var $secondSearchUri = '&uis=684f9245&sarea=g&st=0&keyword=';

	var $prdid_stag = 'var deals =';
	var $prdid_etag = 'var total_count =';
	var $prdid_sub_stag = '[[';
	var $prdid_sub_etag = ']];';

	//////////////////////////////////////////////////////////////////
	// Request Header 정보.
	var $h_host 	= "Host: www.ticketmonster.co.kr";
	var $h_referer = "Referer: http://www.ticketmonster.co.kr/search/?keyword_view=%s&keyword=%s&uis=684f9245&sarea=g&st=0";
	var $h_origin 	= "Origin: http://www.ticketmonster.co.kr";
	var $h_request = "X-Requested-With: XMLHttpRequest";
	var $h_length 	= "Content-Length: %d";
	var $h_agent  	= "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36";

	//////////////////////////////////////////////////////////////////
	// parser tag list.

	var $start_pos = '<div id="deal_lst" class="wrap_deal_lst"';
	var $end_pos   = '<div class="alimipop" id="alarmpop" style="display:none;">';

	var $list_start = '<li onmouseover="';
	var $list_end = '</li>';

	var $title_start = 'style="text-decoration:none" title="';
	var $title_end = '">';

	var $cmt_s = 'NOT';
	var $cmt_e = '">';

	var $link_s = '<a href="';
	var $link_e = '" target="';

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

	var $total_insert_count = 0;
	var $total_skip_count = 0;


	//////////////////////////////////////////////////////////////////
	// 상품 리스트를 받기 위한 첫 검색결과 호출
	public function requestFristSearchResult($query) {
		$curl = new EPCurl;
		$searchQuery = $this->firstSearchUri.urlencode($query).$this->secondSearchUri.urlencode($query);
		$r = $curl->requestGetDataFromUrl($searchQuery, 'GET');
		return $r;
	}

	//////////////////////////////////////////////////////////////////
	// 검색결과에서 상품ID를 추출하고 이를 array로 받는다
	public function getPrdtIdArr($r, $pa) {
		$r2 = $pa->getBody($r, $this->prdid_stag, $this->prdid_etag);
		$result = $pa->getBody($r2, $this->prdid_sub_stag, $this->prdid_sub_etag);
		$arr = explode(",", $result);
		$r = str_replace("[", "", $arr);
		$r2 = str_replace("]", "", $r);

		return $r2;
	}

	//////////////////////////////////////////////////////////////////
	// array로 받은 상품ID를 {10}건 단위로 요청 uri를 생성하여 array로 리턴한다.
	public function requestPrdtList($prdtListArr, $query) {
		$a = array();
		$list = '';
		$cur_idx = 0;
		$total = sizeof($prdtListArr);

		for ($i=0;$i<$total;$i++) {
			$list = $list.'deal_srls%5B%5D='.$prdtListArr[$i].'&';
			if ($i % 12 == 0 && $i != 0) {
				$list = $list.'keyword='.urlencode($query).'&cur_idx='.$cur_idx++;
				//echo "(ERPY) param : $list\n";
				array_push($a, $list);
				$list = '';
			}
		}

		array_push($a, $list);

		return $a;
	}

	//////////////////////////////////////////////////////////////////
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
			array_push($headers_arr, $this->h_host);
			$ss = sprintf($this->h_referer, urlencode($query), urlencode($query));
			array_push($headers_arr, $ss);
			array_push($headers_arr, $this->h_origin);
			array_push($headers_arr, $this->h_request);
			array_push($headers_arr, $this->h_agent);

			//echo ">>>>> request : ".$prdtArr[$i]."\n";
			$data = $curl->requestPostDataFromUrl($this->moreSearchUrl, $prdtArr[$i], $headers_arr);
			sleep(1.3);
			$res = $res." ".$data;

			/* debug
				echo $data."\n";
				echo "===erpy===\n";
			 */
		}
		return $res;
	}

	//////////////////////////////////////////////////////////////////
	// 상품정보 array에서 하나씩 추출하여 상품정보를 추출하여 이를 array에 저장한다.
	public function parsePrdtInfo($list_arr, $pa) {
		$result_item_arr = array();
		$total = sizeof($list_arr);
		for ($i = 0; $i<$total; $i++) {
			echo "============================================\n";
			$list = $list_arr[$i];
			//echo "$list\n";

			// get title
			$result = $pa->getItem($list, $this->title_start, $this->title_end);
			$t_title = trim($result);
			printf("title : (%s)\n", $result);

			$result = $pa->getItem($list, $this->cmt_s, $this->cmt_e);
			$t_cmt1 = trim($result);
			printf("comment : (%s)\n", $result);

			$result = $pa->getItem($list, $this->sale_per_s, $this->sale_per_e);
			$t_sale_per = trim($result);
			printf("sale per : (%s)\n", $t_sale_per);

			$result = $pa->getItem($list, $this->org_price_s, $this->org_price_e);
			$t_price_org = trim($result);
			printf("org price : (%s)\n", $result);

			$result = $pa->getItem($list, $this->sale_price_s, $this->sale_price_e);
			$t_price_sale = trim($result);
			printf("slae price : (%s)\n", $result);

			$result = $pa->getItem($list, $this->timon_price_s, $this->timon_price_e);
			$t_price_timon = trim($result);
			printf("timon price : (%s)\n", $result);

			$result = $pa->getItem($list, $this->buy_count_s, $this->buy_count_e);
			$t_sell_count = trim($result);
			printf("selling count : (%s)\n", $result);

			$result = $pa->getItem($list, $this->thumb_s, $this->thumb_e);
			$t_thumb = trim($result);
			printf("thumb : (%s)\n", $result);

			$result = $pa->getItem($list, $this->link_s, $this->link_e);
			$t_link = trim($result);
			printf("link : (%s)\n", $result);

			$item_arr = array("title" => "$t_title",
					"cmt1" => "$t_cmt1",
					"sale_per" => "$t_sale_per",
					"sale_price" => "$t_sale_per",
					"timon_price" => "$t_price_timon",
					"org_price" => "$t_price_org",
					"sell_count" => "$t_sell_count",
					"thumb" => "$t_thumb",
					"link" => "$t_link",
					);

			array_push($result_item_arr, $item_arr);
		}
		return $result_item_arr;
	}

	//////////////////////////////////////////////////////////////////
	// 추출된 상품정보를 db에 insert 한다.
	public function putPrdtInfoToDB($result_item_arr, $db) {
		$db->connect();

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
				$t_price_timon = $tmp["timon_price"];
				if (strcmp($t_price_sale, "START_POS_NOT") == 0) {
					$t_price_sale = $t_price_timon;
				}
				$t_sale_per = $tmp["sale_per"];
				$t_sell_count = $tmp["sell_count"];
				$t_sql = "INSERT INTO SOCIAL_SHOP_T (title, cmt1, link, thumb, price_org, price_sale, sale_per, sell_count, cp)
					VALUES ('$t_title', '$t_cmt1', '$t_link', '$t_thumb', '$t_price_org', '$t_price_sale', '$t_sale_per', $t_sell_count, 'timon')";
				$db->select($t_sql);
				echo "(INSERT) $t_title\n";
				$this->total_insert_count++;
			}
			else {
				echo "#SKIP# $t_title\n";
				$this->total_skip_count++;
			}
		}

		$db->commit();
		$db->close();
	}
} // class 

?>
