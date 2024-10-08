<?php
$format = $format ?? 'htm';
$table_header = $table_header ?? true;
$export = $export ?? [];

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
	
	$tfoot = $tfoot ?? false;
	if($tfoot) {
		foreach($tfoot as $key=>$fn) {
			$column = array_column($export, $key);
			$tfoot[$key] = match($fn) {
				'sum' => array_sum($column),
				'count' => count($column),
				default => $fn
			};
		}
		$table->setFooting($tfoot);
	}
		
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
