<?php
include "../curl.class.php";

$url = 'http://www.wemakeprice.com/search/get_deal_more';

$str_uri = 'curr_deal_cnt=90&search_keyword=%EB%A7%9B%EC%A7%91&search_cate=top&r_cnt=30';

$s = strlen($str_uri);

$req_headers = array (
		//"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
		//"Accept-Encoding: gzip,deflate,sdch",
		//"Accept-Language: ko-KR,ko;q=0.8,en-US;q=0.6,en;q=0.4",
		//"Connection: keep-alive",
		"Host: www.wemakeprice.com",
		"Referer: http://www.wemakeprice.com/search?search_keyword=%EB%A7%9B%EC%A7%91&search_cate=top",
		"Origin: http://www.wemakeprice.com",
		"X-Requested-With: XMLHttpRequest",
		"Content-Length: $s",
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
