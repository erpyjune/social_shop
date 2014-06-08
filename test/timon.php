<?php
include "../curl.class.php";

$url = 'http://www.ticketmonster.co.kr/search/getDealsContents';

$str_uri = 'deal_srls%5B%5D=48220417&deal_srls%5B%5D=64095457&deal_srls%5B%5D=72742457&deal_srls%5B%5D=74965033&deal_srls%5B%5D=38636581&deal_srls%5B%5D=65014733&deal_srls%5B%5D=72400609&deal_srls%5B%5D=75627185&deal_srls%5B%5D=76093053&deal_srls%5B%5D=62363673&deal_srls%5B%5D=58726073&deal_srls%5B%5D=72283165&gnb_cat=total&keyword=%EB%A7%9B%EC%A7%91&cur_idx=1';

$s = strlen($str_uri);

$req_headers = array (
		//"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
		//"Accept-Encoding: gzip,deflate,sdch",
		//"Accept-Language: ko-KR,ko;q=0.8,en-US;q=0.6,en;q=0.4",
		//"Connection: keep-alive",
		"Host: www.ticketmonster.co.kr",
		"Referer: www.ticketmonster.co.kr",
		"Origin: http://www.ticketmonster.co.kr",
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
