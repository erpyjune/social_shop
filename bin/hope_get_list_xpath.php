<?php
include "../lib/curl.class.php";
include "../lib/parser.class.php";

$S_body = '<div class="prd-hd-ctrl">';
$E_body = '<ol class="paging">';
$S_list = '<div class="tb-c">';
$E_list = '</div>';


/////////////////////// main //////////////////////////////////
if ($argc < 2) {
	die ("(usage) target_url\n");
}

$cu = new EPCurl;
$pa = new EPParser;
$doc = new DOMDocument();

$r = $cu->requestGetDataFromUrl($argv[1], "GET");
$data = iconv("EUC-KR","UTF-8",$r);
$r = $pa->getBody($data, $S_body, $E_body);
$arr = $pa->getListCount($r, $S_list, 1, $E_list, 1);
print_r($arr);
echo "======== start list =====\n";

foreach ($arr as $list) {
	libxml_use_internal_errors(true);
	$doc->loadHTML($list);
	libxml_clear_errors();
	$xpath = new DOMXPath($doc);

/*
	echo "================\n";
	echo "$list\n";
	echo "================\n";
*/

/*
    $node = $xpath->query("div/img[@class='fooimage']/attribute::src", $entry); // returns a DOMNodeList
    $result['image_src'] = $node->item(0)->value; // get the first node in the list which is a DOMAttr
*/

/*
	$nodelist = $xpath->query( "//div[@style='width:180px;height:50px;padding-top:3px;']/a" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "title -> " . $s."\n";
	}
*/

	$nodelist = $xpath->query( "//li[@class='dsc']" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "title -> " . $s."\n";
	}

	$nodelist = $xpath->query( "//li[@class='thumb']/a/@href" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "link -> " . "http://www.hopehill-korea.com" . $s."\n";
	}

	$nodelist = $xpath->query( "//li[@class='thumb']/a/img/@src" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "thumb -> " . "http://www.hopehill-korea.com" . $s . "\n";
	}

	$nodelist = $xpath->query( "//li[@class='price']" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$r = str_replace("원", "", $s);
		$s = str_replace(",", "", $r);
		echo "org_price -> " . $s."\n";
	}

/*

	$nodelist = $xpath->query( "//div[@style='color:#ff4e00;font-size:16px;width:180pxheight:18px;']//b" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "sale_price -> " . $s."\n";
	}
*/
	echo ">>>>>>>>>>\n";
}


?>
