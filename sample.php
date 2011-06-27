<?php
include 'GoogleScraper.class.php';
$obj=new GoogleScraper();
$arr=$obj->getUrlList('test','200.123.187.165:8080');
print_r($arr);
?>