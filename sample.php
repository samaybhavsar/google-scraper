<?php
ini_set ( 'max_execution_time', 0); 
include 'GoogleScraper.class.php';
$obj=new GoogleScraper();
$arr=$obj->getUrlList('test','111.11.27.194:80');
print_r($arr);
?>