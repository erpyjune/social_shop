<?php
include "../curl.class.php";

$url = 'http://www.coupang.com/getMoreDeal.pang';

$str_uri = 'coupangSrls%5B%5D=63385781&coupangSrls%5B%5D=63438696&coupangSrls%5B%5D=62992894&coupangSrls%5B%5D=62905038&coupangSrls%5B%5D=63000630&coupangSrls%5B%5D=62830559&coupangSrls%5B%5D=63226478&coupangSrls%5B%5D=62794431&coupangSrls%5B%5D=63075792&coupangSrls%5B%5D=63440923&coupangSrls%5B%5D=63506756&coupangSrls%5B%5D=63035922&menuId=';

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
