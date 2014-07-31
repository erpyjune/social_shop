<?php
    include "xpath.class.php";
    $strUrl = "http://donxu.tistory.com";
    $xPathManager = new XPathManager($strUrl);
    $recentPosts = $xPathManager->getObjectList("//div[@id='recentPost']/ul/li/a");
        for($i = 0; $i < $recentPosts->length; $i++) {
        $value = $recentPosts->item($i)->nodeValue;
        echo "$value";
    }
?>
