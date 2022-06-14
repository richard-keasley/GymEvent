<?php 
$fp =  fopen('php://output', 'w');
$thead = [];
foreach($export as $row) {
	$row = array_flatten_with_dots($row);
	if(!$thead) {
		foreach(array_keys($row) as $key) {
			$thead[] = str_replace('.', '_', $key);
		}
		fputcsv($fp, $thead);
	}
	fputcsv($fp, $row);
}
fclose($fp);