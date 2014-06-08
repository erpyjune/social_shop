<?php
// wemaf ...
$body = file_get_contents('http://www.wemakeprice.com/main/get_deal_more/990300?curr_deal_cnt=51&r_cnt=30');
// 스페이스바 처리 (탭 같은건 처리 안했음..)
$s = preg_replace("/[\s]/s", "\u0020", $body); 
// \u -> %로 변환. \u 가 아닌 것들은 %00X로 변환
$t = preg_replace("/((\\\u([0-9A-F]+))|(.*?))/ie", "conv(\"$1\")", $s); 
// url decode
$s = rawurldecode($t); 
// 적절히 인코딩 변환 후 출력 해보기
echo iconv('utf-16be', 'utf-8', $s);
//echo iconv("EUC-KR","UTF-8",$body);

//$s = urlencode($body);
//$t = urldecode($s);
//echo "$t\n";

/*
$s 	= urlencode($g);
$str 	= str_replace("\\n","\n", $s);
$s 	= str_replace("\\t"," ", $str);
$str 	= iconv("UTF-8", "CP949", rawurldecode($s)) ;
echo "$str\n";
*/

//echo $r;
//$s = iconv("CP949","UTF-8",$r) ;
//$s = iconv("EUC-KR","UTF-8",$r) ;
//$result = urldecode($s);
//$str = str_replace("\n","\\r\\n", $r);
//$r   = str_replace("\n","\\n", $str);
//echo "$r\n";

/**
$t =  json_encode($s,JSON_UNESCAPED_UNICODE);
echo $t;
**/
//echo iconv("CP949", "UTF-8", $s);

?>
