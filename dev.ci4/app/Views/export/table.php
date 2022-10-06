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

	default:
	$csv = new \App\Libraries\Csv;
	$csv->open('php://output');
	if($thead) $csv->put_row($thead);
	$csv->put_table($tbody);
	$csv->close();
}
