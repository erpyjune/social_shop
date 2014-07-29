<?php
include "../lib/curl.class.php";
include "../lib/parser.class.php";
include "../lib/db.class.php";

$db_host = 'localhost';
$db_user = 'erpy';
$db_passwd = 'erpy000';
$db_name = 'social';

$pre_json = " '{ ";
$post_json = " }' ";

$pre_param = 'http://localhost:9200/coopang/_search';
$mid_param = ' -d ';
//$data_string = '{ "query" : { "term" : { "title" : "코펠" } } } ';
$data_string = '{
    "query": {
        "query_string": {
            "query": "코펠",
            "fields": ["title"]
        }
    }
}
';

$curl = new EPCurl;
$db   = new EPDB;

$r = $curl->requestPost2($pre_param, $data_string);
//$s = iconv("EUC-KR","UTF-8",$r) ;
$s = iconv("UTF-8","EUC-KR",$r) ;

echo "$s\n";
echo "end!!\n";

?>
