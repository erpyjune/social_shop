<?php

include "../lib/db.class.php";

if ($argc < 2) {
   die("\n needs query \n");
}

//////////////////////////////////////////////////////////////////////////////
// DB class.
$db   = new EPDB;

$url = 'http://www.g9.co.kr/deals/search?page=1&size=100&sort=g9best&catel=&h=catel&_=1400988880263&q=';
$q = urlencode($argv[1]);
$qurl = $url.$q;
$contents = file_get_contents($qurl);
$result = json_decode($contents); 

//////////////////////////////////////////////////////////////////////////////
// connection DB.
$db->connect();

//////////////////////////////////////////////////////////////////////////////
// Insert to DB.
$total_insert_count = 0;
$total_skip_count = 0;

foreach ($result->deals as $list) {
	echo ">>> title : ".$list->gdnm."\n";
	echo ">>> desc  : ".$list->gddesc."\n";
	echo ">>> date  : ".$list->expireDate."\n";
	echo ">>> org price  : ".$list->sprice."\n";
	echo ">>> sale price  : ".$list->dprice2."\n";
	echo ">>> sell count  : ".$list->soldqty."\n";
	echo ">>> thumb  : ".$list->img."\n";
	echo ">>> link  : ".$list->gdno."\n";

	$t_title = $list->gdnm;

   $s_sql = "select link from social_shop_t where link = '$list->gdno'";
   if ($db->data_exist($s_sql) == 0) {
      $t_thumb = $list->img;
      $t_cmt1 = $list->gddesc;
      $t_price_org = $list->sprice;
      $t_price_sale = $list->dprice2;
      $t_sale_per = "0";
      $t_sell_count = $list->soldqty;
      $t_link = $list->gdno;
      $t_sql = "INSERT INTO SOCIAL_SHOP_T (title, cmt1, link, thumb, price_org, price_sale, sale_per, sell_count, cp)
         VALUES ('$t_title', '$t_cmt1', '$t_link', '$t_thumb', '$t_price_org', '$t_price_sale', '$t_sale_per', $t_sell_count, 'g9')";
      $db->select($t_sql);
      echo "(INSERT) $t_title\n";
      $total_insert_count++;
   }
   else {
      echo "(SKIP) $t_title\n";
      $total_skip_count++;
   }

	//print_r($list);
	echo "=====================\n";
}

$db->commit();
$db->close();

echo "total insert count --> " . $total_insert_count . "\n";
echo "total skip   count --> " . $total_skip_count . "\n";

?>
