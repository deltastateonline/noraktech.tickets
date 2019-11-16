<?php
//Green Weller 
$emailfrom = "<green.man@andresw.com.au>";

echo $emailfrom,"\n";

$keywords = preg_split("/</", $emailfrom);
print_r($keywords);