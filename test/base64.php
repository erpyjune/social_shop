<?php
$s = file_get_contents('http://www.wemakeprice.com/main/get_deal_more/990300?curr_deal_cnt=51&r_cnt=30');
$d = base64_encode($s);
echo "$d\n";
?>
