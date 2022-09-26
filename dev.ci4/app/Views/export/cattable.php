<?php 
$cattable = new \App\Views\Htm\Cattable($headings);
$cattable->data = $export;

$format = $format ?? 'htm';
switch($format) {
	case 'htm':
	$cattable->table_header = true;
	echo $cattable->htm();
	break;
	
	default:
	echo $cattable->csv();
}
