<?php 
$fp =  fopen('php://output', 'w');
foreach($export as $key=>$row) {
	$row = array_flatten_with_dots($row);
	if(!$key) fputcsv($fp, array_keys($row));
	fputcsv($fp, $row);
}
fclose($fp);