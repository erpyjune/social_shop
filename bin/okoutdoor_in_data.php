<?php
include "../lib/okoutdoor.class.php";

if ($argc < 3) {
   die("needs filepath cp_name\n");
}

$cp = new OKOutdoor;
$db = new EPDB;

$data = $cp->fileReadToArray($argv[1]);
$cp->keywordAndUrlInsertToDB($data, $argv[2], $db);

echo "total process count --> $cp->total_process_count\n";
echo "total insert  count --> $cp->total_insert_count\n";
echo "total skip    count --> $cp->total_skip_count\n";

?>
