<?php

$string = "
<a>
<b>
  <c>text</c>
  <c>stuff</c>
</b>
<b>
  <c>code</c>
</b>
</a>";

$xml = new SimpleXMLElement($string);

//relative to root
$b0=$xml->b[0]->xpath('//c');
while(list( , $node) = each($b0)) {
//    echo 'b[0]: //c: ',$node,"\n";
}

$b1=$xml->b[1]->xpath('//c');
while(list( , $node) = each($b1)) {
//    echo 'b[1]: //c: ',$node,"\n";
}

echo "\n";

//relative to current element
$b0=$xml->b[0]->xpath('.//c');
while(list( , $node) = each($b0)) {
    echo 'b[0]: .//c: ',$node,"\n";
}

$b1=$xml->b[1]->xpath('.//c');
while(list( , $node) = each($b1)) {
//    echo 'b[1]: .//c: ',$node,"\n";
}

?>
