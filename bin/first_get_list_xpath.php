<?php
include "../lib/curl.class.php";
include "../lib/parser.class.php";

$body_s = '<ul class="list" id="bestList_0" name="bestList"  style="display:none;"  >';
$body_e = '<div class="list_count">';

$list_s = '<li';
$list_e = '</li>';

$proc_cnt = 0;

/////////////////////// main //////////////////////////////////
if ($argc < 2) {
	die ("(usage) target_url\n");
}

$cu = new EPCurl;
$pa = new EPParser;
$doc = new DOMDocument();

$s = $cu->requestGetDataFromUrl($argv[1], "GET");
$r = $pa->getBody($s, $body_s, $body_e);
//echo "$r\n";
//die ("erpy!!\n");
//$data = iconv("EUC-KR","UTF-8", $r);
$arr = $pa->getListCount($r, $list_s, 1, $list_e, 1);
//print_r($arr);
//die ("erpy!!\n");

foreach ($arr as $list) {
	libxml_use_internal_errors(true);
	$doc->loadHTML($list);
	libxml_clear_errors();
	$xpath = new DOMXPath($doc);

/*
    $node = $xpath->query("div/img[@class='fooimage']/attribute::src", $entry); // returns a DOMNodeList
    $result['image_src'] = $node->item(0)->value; // get the first node in the list which is a DOMAttr
*/


	$nodelist = $xpath->query( "//span[@class='sub_img']/a/img/@alt" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		echo "title -> " . $s . "\n";
	}

	//$nodelist = $xpath->query( "//p[@class='sub_text']/span[@class='origin_price']" );
	$nodelist = $xpath->query( "//span[@class='origin_price']" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$r = str_replace("원", "", $s);		
		$s = str_replace(",", "", $r);		
		echo "org_price -> " . $s . "\n";
	}

	//$nodelist = $xpath->query( "//p[@class='sub_text']/span[@class='sale_price']" );
	$nodelist = $xpath->query( "//span[@class='sale_price']" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$r = str_replace("원", "", $s);		
		$s = str_replace(",", "", $r);		
		echo "sale_price -> " . $s . "\n";
	}

	$nodelist = $xpath->query( "//span[@class='sale_percent']" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$r = str_replace("[", "", $s);		
		$t = str_replace("]", "", $r);		
		$s = str_replace("%", "", $t);		
		echo "sale_prt -> " . $s . "\n";
	}

	$nodelist = $xpath->query( "//span[@class='sub_img']/a/img/@src" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$t = substr($s, 0, -5);
		echo "img -> " . "http://www.chocammall.co.kr" . $t. "0.jpg\n";
	}

	$nodelist = $xpath->query( "//span[@class='sub_img']/a/@href" );
	foreach ($nodelist as $n){
		$s = utf8_decode($n->nodeValue);
		$t = $pa->getItem($s, '(', ')');
		echo "link -> " . "http://www.chocammall.co.kr/shop/base/product/viewProductDetail.do?goods_no=" . $t."\n";
	}

	$proc_cnt++;

/*

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
*/
	echo ">> proc_cnt : $proc_cnt\n";
	echo ">>>>>>>>>>\n";
}

?>
