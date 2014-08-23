<?php
include "../lib/curl.class.php";
include "../lib/parser.class.php";
include "../lib/db.class.php";

///////////////////////////////////////////////
if ($argc < 2) {
   die("(usage) (all or cp_name) \n");
}

$db_host = 'localhost';
$db_user = 'erpy';
$db_passwd = 'erpy000';
$db_name = 'social';

$pre_json = " '{ ";
$post_json = " }' ";

$pre_param = 'http://localhost:9200/shop/outdoor/';
$mid_param = ' -d ';
$post_param = '';

if ($argv[1] == 'all') {
	$t_sql = 'SELECT id,title,cmt,cate,brand,link,thumb,price_org,price_sale,price_special,sale_per,sell_count,cp FROM SOCIAL_SHOP_T';
} else {
	$t_sql = 'SELECT id,title,cmt,cate,brand,link,thumb,price_org,price_sale,price_special,sale_per,sell_count,cp FROM SOCIAL_SHOP_T ';
	$t_sql = $t_sql . ' ' . 'WHERE cp = ' . "'" . $argv[1] . "'";
}

echo ">> SQL : $t_sql\n";

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

	$cmt = $row['cmt'];
	$post_param = $post_param.",\"cmt\":\"$cmt\"";

	$cate = $row['cate'];
	$post_param = $post_param.",\"cate\":\"$cate\"";

	$brand = $row['brand'];
	$post_param = $post_param.",\"brand\":\"$brand\"";

	$link = $row['link'];
	$post_param = $post_param.",\"link\":\"$link\"";

	$thumb = $row['thumb'];
	$post_param = $post_param.",\"thumb\":\"$thumb\"";

	$price_org = $row['price_org'];
	if ($price_org == "START_POS_NOT" || $price_org == "") $price_org = "0";
//	$post_param = $post_param.",\"price_org\":\"$price_org\"";
	$post_param = $post_param.",\"price_org\":$price_org";

	$price_sale = $row['price_sale'];
	if ($price_sale == "START_POS_NOT" || $price_sale == "") $price_sale = "0";
//	$post_param = $post_param.",\"price_sale\":\"$price_sale\"";
	$post_param = $post_param.",\"price_sale\":$price_sale";

	$price_special = $row['price_special'];
	if ($price_special == "START_POS_NOT" || $price_special == "") $price_special = "0";
//	$post_param    = $post_param.",\"price_special\":\"$price_special\"";
	$post_param    = $post_param.",\"price_special\":$price_special";

	$sale_per = $row['sale_per'];
	if ($sale_per == "START_POS_NOT" || $sale_per == "") $sale_per = "0";
//	$post_param = $post_param.",\"slae_per\":\"$sale_per\"";
	$post_param = $post_param.",\"slae_per\":$sale_per";

	$sell_count = $row['sell_count'];
	if ($sell_count == "START_POS_NOT" || $sell_count == "") $sell_count = "0";
//	$post_param = $post_param.",\"sell_count\":\"$sell_count\"";
	$post_param = $post_param.",\"sell_count\":$sell_count";

	$cp = $row['cp'];
	$post_param = $post_param.",\"cp\":\"$cp\"";

/*
	$data_arr = array("id" => "$id",
							"title"=>"$title",
							"cmt" => "$cmt",
							"cate" => "$cate",
							"brand" => "$brand",
							"link" => "$link",
							"thumb" => "$thumb",
							"price_org" => "$price_org",
							"price_sale" => "$price_sale",
							"price_special" => "$price_special",
							"slae_per" => "$sale_per",
							"sell_count" => "$sell_count",
							"cp" => "$cp");
*/
	$data_arr = array("id" => "$id",
							"title"=>"$title",
							"cmt" => "$cmt",
							"cate" => "$cate",
							"brand" => "$brand",
							"link" => "$link",
							"thumb" => "$thumb",
							"price_org" => $price_org,
							"price_sale" => $price_sale,
							"price_special" => $price_special,
							"slae_per" => $sale_per,
							"sell_count" => $sell_count,
							"cp" => "$cp");

	$data_string = json_encode($data_arr);
	echo "Indexing... ".$data_string."\n";
	echo "===========\n";
	$cresult = $curl->requestPost2($pre_param.$id, $data_string);
	//sleep(0.3);

}

$db->close();
echo "end\n";

?>
