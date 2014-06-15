<?php
include "../curl.class.php";
include "../parser.class.php";

$str_uri = 'coupangSrls%5B%5D=63385781&coupangSrls%5B%5D=63438696&coupangSrls%5B%5D=62992894&coupangSrls%5B%5D=62905038&coupangSrls%5B%5D=63000630&coupangSrls%5B%5D=62830559&coupangSrls%5B%5D=63226478&coupangSrls%5B%5D=62794431&coupangSrls%5B%5D=63075792&coupangSrls%5B%5D=63440923&coupangSrls%5B%5D=63506756&coupangSrls%5B%5D=63035922&menuId=';

class Coupang {
	var $moreSearchUrl = 'http://www.coupang.com/getMoreDeal.pang';
	var $firstSearchUri = 'http://www.coupang.com/search.pang?q=';
	var $moreUri = 'http://www.coupang.com/getMoreDeal.pang?uri=/search.pang&q=';
	var $reqHeaders = array (
			//"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
			//"Accept-Encoding: gzip,deflate,sdch",
			//"Accept-Language: ko-KR,ko;q=0.8,en-US;q=0.6,en;q=0.4",
			//"Connection: keep-alive",
			"Host: www.coupang.com",
			"Referer: http://www.coupang.com/search.pang?q=%EB%A7%9B%EC%A7%91",
			"Origin: http://www.coupang.com",
			"X-Requested-With: XMLHttpRequest",
			"Content-Length: %d",
			"uri: /search.pang&q=%EB%A7%9B%EC%A7%91",
			"User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36"
			);

	var $prdid_stag = 'pageJson = jQuery.parseJSON';
	var $prdid_etag = 'dealProvider = new DealProvider';
	var $prdid_sub_stag = '[[';
	var $prdid_sub_etag = ']]}';

	var $h_host 	= "Host: www.coupang.com";
	var $h_referer = "Referer: http://www.coupang.com/search.pang?q=%s";
	var $h_origin 	= "Origin: http://www.coupang.com";
	var $h_request = "X-Requested-With: XMLHttpRequest";
	var $h_length 	= "Content-Length: %d";
	var $h_uri    	= "uri: /search.pang&q=%s";
	var $h_agent  	= "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36";


	// 상품 리스트를 받기 위한 첫 검색결과 호출
	public function requestFristSearchResult($query) {
		$curl = new EPCurl;
		$searchQuery = $this->firstSearchUri.urlencode($query);
		$r = $curl->requestGetDataFromUrl($searchQuery, 'GET');
		return $r;
	}

// 검색결과에서 상품ID를 추출하고 이를 array로 받는다
public function getPrdtIdArr($r) {
	$pa   = new EPParser;
	$r2 = $pa->getBody($r, $this->prdid_stag, $this->prdid_etag);
	$result = $pa->getBody($r2, $this->prdid_sub_stag, $this->prdid_sub_etag);
	$arr = explode(",", $result);
	$r = str_replace("[", "", $arr);
	$r2 = str_replace("]", "", $r);

	return $r2;
}

// array로 받은 상품ID를 {10}건 단위로 요청 uri를 생성하여 array로 리턴한다.
public function requestPrdtList($prdtListArr) {
	$a = array();
	$list = '';
	$total = sizeof($prdtListArr);

	for ($i=0;$i<$total;$i++) {
		$list = $list.'coupangSrls%5B%5D='.$prdtListArr[$i].'&';
		if ($i % 11 == 0 && $i != 0) {
			array_push($a, $list);
			$list = '';
		}
	}

	array_push($a, $list);

	return $a;
}

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
		$ss = sprintf($this->h_uri, urlencode($query));
		array_push($headers_arr, $ss);
		array_push($headers_arr, $this->h_host);
		array_push($headers_arr, $this->h_referer);
		array_push($headers_arr, $this->h_origin);
		array_push($headers_arr, $this->h_request);
		array_push($headers_arr, $this->h_agent);
		echo ">>>>> request : ".$prdtArr[$i]."\n";
		$data = $curl->requestPostDataFromUrl($this->moreSearchUrl, $prdtArr[$i], $headers_arr);
		sleep(1);
		$res = $res." ".$data;
		//echo $result;
	}
	return $res;
}


// 상품정보 array에서 하나씩 추출하여 상품정보를 추출하여 이를 array에 저장한다.
public function parsePrdtInfo() {

}

// 추출된 상품정보를 db에 insert 한다.
public function putPrdtInfoToDB() {

}
}


/////////////////////// main //////////////////////////////////
if ($argc < 2) {
	die ("(usage) query\n");
}

$coo = new Coupang;

$result = $coo->requestFristSearchResult($argv[1]);
$prdtIDarr = $coo->getPrdtIdArr($result);
$no = sizeof($prdtIDarr);
/*
	for ($i=0 ; $i<$no ; $i++) {
	echo $prdtIDarr[$i]."\n";
	}
 */
$arr = $coo->requestPrdtList($prdtIDarr);
/*
	$no = sizeof($arr);
	for ($i=0 ; $i<$no ; $i++) {
	echo "arr[$i] -> ".$arr[$i]."\n";
	}
*/
$res = $coo->requestSearchPrdt($arr, $argv[1]);
/*
echo "$res";
*/

//print_r($arr);

?>
