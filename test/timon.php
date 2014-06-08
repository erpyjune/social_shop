<?php
include "../curl.class.php";

$url = 'http://www.ticketmonster.co.kr/search/getDealsContents';

$str_uri = 'deal_srls%5B%5D=57812021&deal_srls%5B%5D=36623409&deal_srls%5B%5D=59592797&deal_srls%5B%5D=63432437&deal_srls%5B%5D=71556149&deal_srls%5B%5D=77375225&deal_srls%5B%5D=75688385&deal_srls%5B%5D=62642581&deal_srls%5B%5D=64947113&deal_srls%5B%5D=62322229&deal_srls%5B%5D=66210945&deal_srls%5B%5D=65677765&gnb_cat=total&keyword=%EB%A7%9B%EC%A7%91&cur_idx=2';

$s = strlen($str_uri);

$req_headers = array (
		//"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
		//"Accept-Encoding: gzip,deflate,sdch",
		//"Accept-Language: ko-KR,ko;q=0.8,en-US;q=0.6,en;q=0.4",
		//"Connection: keep-alive",
		"Host: www.ticketmonster.co.kr",
		"Referer: http://www.ticketmonster.co.kr/search/?keyword_view=%EB%A7%9B%EC%A7%91&keyword=%EB%A7%9B%EC%A7%91&uis=079ab4e0&sarea=g&st=0",
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
