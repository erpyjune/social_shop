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

	$tags = $doc->getElementsByTagName('strike');
	echo "<strike ===========================\n";
	foreach ($tags as $tag) {
		$value = $tag->nodeValue;
		$s = utf8_decode($value);
		//$s = iconv("CP949","UTF-8",$value) ;
		echo "value -> $s\n";
	}
	echo "===========================\n";

	$tags = $doc->getElementsByTagName('b');
	echo "<b ===========================\n";
	foreach ($tags as $tag) {
		$value = $tag->nodeValue;
		$s = utf8_decode($value);
		//$s = iconv("CP949","UTF-8",$value) ;
		echo "value -> $s\n";
	}
	echo "===========================\n";

/*
	$tags = $doc->getElementsByTagName('a');
	echo "<a ===========================\n";
	foreach ($tags as $tag) {
		$href   = $tag->getAttribute('href');
		$value = $tag->nodeValue;
		$s = utf8_decode($value);
		//$s = iconv("CP949","UTF-8",$value) ;
		echo "href  -> $href\n";
		echo "value -> $s\n";
	}
	echo "===========================\n";

	$tags = $doc->getElementsByTagName('img');
	echo "<img ===========================\n";
	foreach ($tags as $tag) {
		$src   = $tag->getAttribute('src');
		$value = $tag->nodeValue;
		echo "src -> $src\n";
		echo "value   -> $value\n";
	}
	echo "===========================\n";
*/
}

?>
