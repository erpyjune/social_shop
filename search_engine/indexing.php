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

$pre_param = 'http://localhost:9200/coopang/social/';
$mid_param = ' -d ';
$post_param = '';
$t_sql = 'SELECT id, title,cmt1,link,thumb,price_org,price_sale,sell_count,cp FROM SOCIAL_SHOP_T';

$curl = new EPCurl;
$db   = new EPDB;

$db->connect();
$result = $db->select($t_sql);
$result->data_seek(0);
while ($row = $result->fetch_assoc()) {

	$id = $row['id'];
	$post_param = "\"id\":\"$id\"";

	$title = $row['title'];
	$post_param = "\"title\":\"$title\"";

	$cmt1 = $row['cmt1'];
	$post_param = $post_param.",\"cmt1\":\"$cmt1\"";

	$link = $row['link'];
	$post_param = $post_param.",\"link\":\"$link\"";

	$thumb = $row['thumb'];
	$post_param = $post_param.",\"thumb\":\"$thumb\"";

	$price_org = $row['price_org'];
	$post_param = $post_param.",\"price_org\":\"$price_org\"";

	$price_sale = $row['price_sale'];
	$post_param = $post_param.",\"price_sale\":\"$price_sale\"";

	$sell_count = $row['sell_count'];
	$post_param = $post_param.",\"sell_count\":\"$sell_count\"";

	$cp = $row['cp'];
	$post_param = $post_param.",\"cp\":\"$cp\"";

	$data_arr = array("id" => "$id",
							"title"=>"$title",
							"cmt1" => "$cmt1",
							"link" => "$link",
							"thumb" => "$thumb",
							"price_org" => "$price_org",
							"price_sale" => "$price_sale",
							"sell_count" => "$sell_count",
							"cp" => "$cp");

	$data_string = json_encode($data_arr);
	echo "data_string = ".$data_string."\n";
	$cresult = $curl->requestPost2($pre_param.$id, $data_string);
	sleep(0.5);

}

$db->close();
echo "end\n";

?>
