<?php
include "../curl.class.php";

$url = 'http://www.coupang.com/getMoreDeal.pang';

$str_uri = 'coupangSrls%5B%5D=63385781&coupangSrls%5B%5D=63438696&coupangSrls%5B%5D=62992894&coupangSrls%5B%5D=62905038&coupangSrls%5B%5D=63000630&coupangSrls%5B%5D=62830559&coupangSrls%5B%5D=63226478&coupangSrls%5B%5D=62794431&coupangSrls%5B%5D=63075792&coupangSrls%5B%5D=63440923&coupangSrls%5B%5D=63506756&coupangSrls%5B%5D=63035922&menuId=';
$h_host = "Host: www.coupang.com";
$h_referer = "Referer: http://www.coupang.com/search.pang?q=%s";
$h_origin = "Origin: http://www.coupang.com";
$h_request = "X-Requested-With: XMLHttpRequest";
$h_length = "Content-Length: %d";
$h_uri = "uri: /search.pang&q=%s";
$h_agent = "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36";


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

$headers_arr = array();
array_push($headers_arr, $h_host);
array_push($headers_arr, $h_referer);
array_push($headers_arr, $h_origin);
array_push($headers_arr, $h_request);
$ss = sprintf($h_length, strlen($str_uri));
array_push($headers_arr, $ss);
$ss = sprintf($h_uri, urlencode('등산'));
array_push($headers_arr, $ss);
array_push($headers_arr, $h_agent);


$curl = new EPCurl;
$r = $curl->requestPostDataFromUrl($url, $str_uri, $headers_arr);
echo "result:".$r;

?>
