<?php
include "../lib/curl.class.php";
include "../lib/parser.class.php";

$stag = '<td align=center valign=top width="25%">';
$etag = '</td>';

/////////////////////// main //////////////////////////////////
if ($argc < 2) {
	die ("(usage) target_url\n");
}

$cu = new EPCurl;
$pa = new EPParser;
$doc = new DOMDocument();

$r = $cu->requestGetDataFromUrl($argv[1], "GET");
$data = iconv("EUC-KR","UTF-8",$r);
$arr = $pa->getListCount($data, $stag, 1, $etag, 1);
foreach ($arr as $list) {
	libxml_use_internal_errors(true);
	$doc->loadHTML($list);
	libxml_clear_errors();
	$xpath = new DOMXPath($doc);

/*
    $node = $xpath->query("div/img[@class='fooimage']/attribute::src", $entry); // returns a DOMNodeList
    $result['image_src'] = $node->item(0)->value; // get the first node in the list which is a DOMAttr
*/

	$nodelist = $xpath->query( "//div[@style='width:180px;height:50px;padding-top:3px;']/a" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "title -> " . $s."\n";
	}

	$nodelist = $xpath->query( "//div[not(@align)]/a/@href" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "link -> " . "http://www.campingmall.co.kr" . $s."\n";
	}

	$nodelist = $xpath->query( "//div[not(@align)]/a/img/@src" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$t = mb_substr($s, 2);
		echo "thumb -> " . "http://www.campingmall.co.kr/shop" . $t . "\n";
	}

	$nodelist = $xpath->query( "//div[@style='height:10px;']//strike" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "org_price -> " . $s."\n";
	}

	$nodelist = $xpath->query( "//div[@style='color:#ff4e00;font-size:16px;width:180pxheight:18px;']//b" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "sale_price -> " . $s."\n";
	}
	echo ">>>>>>>>>>\n";
}

?>
