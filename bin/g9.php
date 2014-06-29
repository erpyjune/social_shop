<?php

if ($argc < 2) {
   die("\n needs query \n");
}

$url = 'http://www.g9.co.kr/deals/search?page=1&size=100&sort=g9best&catel=&h=catel&_=1400988880263&q=';
$q = urlencode($argv[1]);
$qurl = $url.$q;
$contents = file_get_contents($qurl);
//print_r($contents);
$result = json_decode($contents); 
//print_r($result);

foreach ($result->deals as $list) {
	echo ">>> title : ".$list->gdnm."\n";
	echo ">>> desc  : ".$list->gddesc."\n";
	echo ">>> date  : ".$list->expireDate."\n";
	echo ">>> price  : ".$list->sprice."\n";
	echo ">>> sale price  : ".$list->dprice2."\n";
	echo ">>> sell count  : ".$list->soldqty."\n";
	//print_r($list);
	echo "=====================\n";
}

echo ">>> total : ".$result->total."\n";

?>
