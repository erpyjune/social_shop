<?php
$s = '[11111],[22222],[33333],[44444],';

$arr = split(",",$s);
$r = str_replace("[", "", $arr);
$r2 = str_replace("]", "", $r);
print_r($r2);
?>
