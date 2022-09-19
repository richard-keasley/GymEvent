<?php 
$cattable = new \App\Views\Htm\Cattable($headings);
$cattable->table_header = true;
$cattable->data = $export;

$format = $format ?? 'htm';
switch($format) {
	case 'htm':
	echo $cattable->htm();
	break;
	
	default:
	echo $cattable->csv();
}
