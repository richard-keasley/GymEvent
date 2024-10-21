<?php
$format = $format ?? 'htm';
$headings = $headings ?? [];
$export = $export ?? [];
# d($export);
# print_r($headings);
 
$cattable = new \App\Views\Htm\Cattable($headings);
$cattable->data = $export;
$cattable->table_header = $table_header ?? true;
# echo $cattable; return;

echo match($format) {
	'htm' => $cattable->__toString(),
	default => $cattable->csv()
};
