<?php 
d($tbody);
$cattable = new \App\Views\Htm\Cattable($headings);
echo $cattable->htm($tbody);
