<?php
header('Content-Type: application/json; charset=utf8'); 
require_once('checkCity.php');
$test = new checkCity();
$test->select_City();