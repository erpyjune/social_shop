<?php
include "../lib/curl.class.php";
include "../lib/parser.class.php";

//$S_body = '<div class="size_layer_box" id="viewLayerArea_92421" style="display:none;">';
//$E_body = '<!-- 상품리스트 :: END -->';
$S_list = '<div class="os_border off">';
$E_list = '<div class="gift_wrap">';


/////////////////////// main //////////////////////////////////
if ($argc < 2) {
	die ("(usage) target_url\n");
}

$cu = new EPCurl;
$pa = new EPParser;
$doc = new DOMDocument();

$r = $cu->requestGetDataFromUrl($argv[1], "GET");
$data = iconv("EUC-KR","UTF-8",$r);
//$r = $pa->getBody($data, $S_body, $E_body);
$arr = $pa->getListCount($data, $S_list, 1, $E_list, 1);
//print_r($arr);

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

	$tt = '';
	$nodelist = $xpath->query( "//span[@class='prName_PrName']" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$tt = $s;
		//echo "title -> " . $s . "\n";
	}

	$brand = '';
	$nodelist = $xpath->query( "//span[@class='prName_Brand']" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$brand = $s;
		//echo "brand -> " . $s . "\n";
	}

	$title = $brand . ' ' . $tt;
	echo "title -> " . $title . "\n";



	$nodelist = $xpath->query( "//p[@class='item_title']/a/@href" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "link -> " . "http://www.okmall.com" . $s."\n";
	}

	$nodelist = $xpath->query( "//div[@class='item_img']/a/img/@data-original" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "thumb -> " . $s . "\n";
	}

	$nodelist = $xpath->query( "//div[@class='real_price01 ']/span[@class='r ']" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$r = str_replace("원", "", $s);
		$s = str_replace(",", "", $r);
		$r = str_replace("\r", "", $s);
		$s = str_replace("\n", "", $r);
		echo "org_price -> " . $s."\n";
	}

	$nodelist = $xpath->query( "//div[@class='real_price03 f_c16 fb']/span[@class='r ']" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$r = str_replace("원", "", $s);
		$s = str_replace(",", "", $r);
		$r = str_replace("\r", "", $s);
		$s = str_replace("\n", "", $r);
		echo "row_price -> " . $s."\n";
	}

	echo ">>>>>>>>>>\n";
}


?>
