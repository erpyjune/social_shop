<?php
include "../lib/okoutdoor.class.php";

if ($argc < 2) {
   die("needs filepath!!\n");
}

$ok = new OKOutdoor;
$db = new EPDB;

$data = $ok->fileReadToArray($argv[1]);
$ok->keywordAndUrlInsertToDB($data, $db);

?>
