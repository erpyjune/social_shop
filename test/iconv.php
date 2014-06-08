<?php
$s = file_get_contents('http://www.wemakeprice.com/main/get_deal_more/990300?curr_deal_cnt=51&r_cnt=30');
$t = encode_utf8($s);
//echo iconv("CP949", "UTF-8", $s);
?>
