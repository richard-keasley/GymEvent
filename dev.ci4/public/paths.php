<?php 
/*
needed to access database without CI framework
used in
/public/sse/index.php
/public/index.php
*/
$include = __DIR__ . '/../app/Config/Paths.php';
if(!is_file($include)) die("{$include} not found");
include_once($include);
