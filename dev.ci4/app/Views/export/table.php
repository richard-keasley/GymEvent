<?php
$format = $format ?? 'htm';
$table_header = $table_header ?? true;

$tbody = []; $thead = []; 
foreach($export as $row) {
	$row = array_flatten_with_dots($row);
	if($table_header) {
		foreach(array_keys($row) as $key) {
			$thead[] = str_replace('.', '_', $key);
		}
		$table_header = false;
	}
	$tbody[] = $row;
}

switch($format) {
	case 'htm':
	$table = \App\Views\Htm\Table::load('bordered');
	if($thead) $table->setHeading($thead);
	else $table->autoHeading = false;
	echo $table->generate($tbody);
	break;
	
	case 'csv':
	$csv = new \App\Libraries\Csv;
	if($thead) $csv->add_row($thead);
	$csv->add_table($tbody);
	$csv->write('php://output');
	break;

	default:
	printf('<pre>%s</pre>', print_r($tbody, 1));
}
