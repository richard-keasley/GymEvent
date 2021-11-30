<?php 

$page = filter_input(INPUT_GET, 'p');
if(!$page) $page = 'index';
$include = __DIR__ . "/setup/{$page}.php";

if(file_exists($include)) include $include;
