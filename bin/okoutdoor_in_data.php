<?php
include "../lib/okoutdoor.class.php";

if ($argc < 2) {
   die("needs filepath!!\n");
}

$cp = new OKOutdoor;
$db = new EPDB;

$data = $cp->fileReadToArray($argv[1]);
$cp->keywordAndUrlInsertToDB($data, $db);

echo "total process count --> $cp->total_process_count\n";
echo "total insert  count --> $cp->total_insert_count\n";
echo "total skip    count --> $cp->total_skip_count\n";

?>
