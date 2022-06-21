<?php
$tbody = [];
foreach($export as $rowkey=>$row) {
	$row = array_flatten_with_dots($row);
	if(!$rowkey) {
		$thead = [];
		foreach(array_keys($row) as $key) {
			$thead[] = str_replace('.', '_', $key);
		}
		$tbody[] = $thead;
	}
	$tbody[] = $row;
}
$table = \App\Views\Htm\Table::load('bordered');
echo $table->generate($tbody);
