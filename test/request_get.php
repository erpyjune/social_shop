<?php
include "../lib/curl.class.php";
include "../lib/parser.class.php";

$stag = '<table border="0" cellspacing="0" cellpadding="0" width=100%>';
$etag = '</table>';

/////////////////////// main //////////////////////////////////
if ($argc < 2) {
	die ("(usage) target_url\n");
}

$cu = new EPCurl;
$pa = new EPParser;

$r = $cu->requestGetDataFromUrl($argv[1], "GET");
$data = iconv("EUC-KR","UTF-8",$r);
echo $data;
//$arr = $pa->getListCount($data, $stag, 1, $etag, 2);
//print_r($arr);

?>
