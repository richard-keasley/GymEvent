<?php
$thead = []; $tbody = [];
foreach($export as $row) {
	$row = array_flatten_with_dots($row);
	if(!$thead) {
		foreach(array_keys($row) as $key) {
			$thead[] = str_replace('.', '_', $key);
		}
	}
	$tbody[] = $row;
}

if(1) {
	$table = \App\Views\Htm\Table::load('bordered');
	$table->setHeading($thead);
	echo $table->generate($tbody);
}
else {
	$fp = fopen('php://output', 'w');
	fputcsv($fp, $thead);
	foreach($tbody as $row) fputcsv($fp, $row);
	fclose($fp);
}
