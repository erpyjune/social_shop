<?php
$s = ' | http://www.daum.net';
$item = explode("|", $s);
$t_keyword = trim($item[0]);
$t_url     = trim($item[1]);

echo "keyword : $t_keyword\n";
echo "url     : $t_url\n";
?>
