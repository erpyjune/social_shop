<?php
include "../curl.class.php";

//
$str_uri = 'coupangSrls%5B%5D=63385781&coupangSrls%5B%5D=63438696&coupangSrls%5B%5D=62992894&coupangSrls%5B%5D=62905038&coupangSrls%5B%5D=63000630&coupangSrls%5B%5D=62830559&coupangSrls%5B%5D=63226478&coupangSrls%5B%5D=62794431&coupangSrls%5B%5D=63075792&coupangSrls%5B%5D=63440923&coupangSrls%5B%5D=63506756&coupangSrls%5B%5D=63035922&menuId=';

class Coupang {
	var $reqUrl = 'http://www.coupang.com/getMoreDeal.pang';
	var $searchiUri = 'http://www.coupang.com/search.pang?q=';
	var $reqHeaders = array (
		//"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
		//"Accept-Encoding: gzip,deflate,sdch",
		//"Accept-Language: ko-KR,ko;q=0.8,en-US;q=0.6,en;q=0.4",
		//"Connection: keep-alive",
		"Host: www.coupang.com",
		"Referer: http://www.coupang.com/search.pang?q=%EB%A7%9B%EC%A7%91",
		"Origin: http://www.coupang.com",
		"X-Requested-With: XMLHttpRequest",
		"Content-Length: $s",
		"uri: /search.pang&q=%EB%A7%9B%EC%A7%91",
		"User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36"
		);

	// 상품 리스트를 받기 위한 첫 검색결과 호출
	public function requestFristSearchResult($query) {
	}

	// 검색결과에서 상품ID를 추출하고 이를 array로 받는다
	public function getPrdtIdArr() {
	}

	// array로 받은 상품ID를 {10}건 단위로 요청하여 상품정보를 받아서 이를 array에 저장한다.
	public function requestPrdtListInfo() {
	}

	// 상품정보 array에서 하나씩 추출하여 상품정보를 추출하여 이를 array에 저장한다.
	public function parsePrdtInfo() {
	}

	// 추출된 상품정보를 db에 insert 한다.
	public function putPrdtInfoToDB() {
	}
}


$s = strlen($str_uri);

$req_headers = array (
		//"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
		//"Accept-Encoding: gzip,deflate,sdch",
		//"Accept-Language: ko-KR,ko;q=0.8,en-US;q=0.6,en;q=0.4",
		//"Connection: keep-alive",
		"Host: www.coupang.com",
		"Referer: http://www.coupang.com/search.pang?q=%EB%A7%9B%EC%A7%91",
		"Origin: http://www.coupang.com",
		"X-Requested-With: XMLHttpRequest",
		"Content-Length: $s",
		"uri: /search.pang&q=%EB%A7%9B%EC%A7%91",
		"User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36"
		);

/*
foreach($req_headers as $key=>$value) {
	echo "$key ->"."$value"."\n";
}
*/

$curl = new EPCurl;
$r = $curl->requestPostDataFromUrl($url, $str_uri, $req_headers);
echo "result:".$r;

?>
