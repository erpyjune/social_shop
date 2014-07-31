<?php
if ($argc <= 1) {
	die("(usage) in_string\n");
}

$s = urldecode($argv[1]);
echo "$s\n";
?>
