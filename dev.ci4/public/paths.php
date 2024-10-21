<?php 
/*
needed to access database without CI framework
used by public/apx/sse
*/
$include = __DIR__ . '/../app/Config/Paths.php';
if(!is_file($include)) die("{$include} not found");
include_once($include);
