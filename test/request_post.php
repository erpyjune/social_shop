<?php
include "../lib/curl.class.php";

$request_url = "http://mobile.auction.co.kr/ajax/Arche.MobileWeb.Search.SearchSlot,Arche.MobileWeb.Web.ashx";
$request_uri = "_method=GetDaSlot&_session=no";
$headers_arr = array();

$h_post_data = "";
$h_length  = "Content-Length: %d";
$h_host = "";
$h_referer = "";
$h_origin = "";
$h_request = "";
$h_agent = "";

if ($argc < 2) {
	die ("(usage) request_url");
}

$ss = sprintf($h_length, mb_strlen($h_post_data));
array_push($headers_arr, $ss);
$ss = sprintf($h_uri, urlencode($query));
array_push($headers_arr, $ss);
array_push($headers_arr, $h_host);
array_push($headers_arr, $h_referer);
array_push($headers_arr, $h_origin);
array_push($headers_arr, $h_request);
array_push($headers_arr, $h_agent);

echo ">>>>> request : ".$prdtArr[$i]."\n";
$data = $curl->requestPostDataFromUrl($this->moreSearchUrl, $prdtArr[$i], $headers_arr);
sleep(1.7);
$res = $res." ".$data;

?>
