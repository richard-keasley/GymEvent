<?php
$format = $format ?? 'htm';
$table_header = $table_header ?? true;

$thead = false; 
if($export && $table_header) {
	$row = current($export);
	$thead = array_keys($row);	
}

switch($format) {
	case 'htm':
	$table = \App\Views\Htm\Table::load('bordered');
	if($thead) $table->setHeading($thead);
	else $table->autoHeading = false;
	echo $table->generate($export);
	break;
	
	case 'csv':
	$csv = new \App\Libraries\Csv;
	if($thead) $csv->add_row($thead);
	$csv->add_table($export);
	$csv->write('php://output');
	break;
	
	default:
	printf('<pre>%s</pre>', print_r($tbody, 1));
}
