<?php
include "../lib/curl.class.php";
include "../lib/parser.class.php";

$S_body = '<td style="padding:15 0">';
$E_body = '<tr><td height=1 bgcolor=#DDDDDD></td></tr>';
$S_list = '<td align=center valign=top width="25%">';
$E_list = '</td>';


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

	$ss = '';
	$nodelist = $xpath->query( "//li[@class='prd-name']" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$ss = $ss . ' ' . $s;
	}

	echo "title -> " . $ss."\n";

	$nodelist = $xpath->query( "//dt[@class='thumb2']/a/@href" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "link -> " . "http://www.dicamping.co.kr" . $s."\n";
	}

	$nodelist = $xpath->query( "//dt[@class='thumb2']/a/img/@src" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "thumb -> " . "http://www.dicamping.co.kr" . $s . "\n";
	}

	$nodelist = $xpath->query( "//li/strike" );
	//$nodelist = $xpath->query( "//font" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$r = str_replace("원", "", $s);
		$s = str_replace(",", "", $r);
		echo "sale_price -> " . $s."\n";
	}

	$nodelist = $xpath->query( "//li[@class='prd-price2']/font" );
	//$nodelist = $xpath->query( "//font" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$r = str_replace("원", "", $s);
		$s = str_replace(",", "", $r);
		echo "org_price -> " . $s."\n";
	}

	echo ">>>>>>>>>>\n";
}


?>
