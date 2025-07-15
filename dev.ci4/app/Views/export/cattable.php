<?php
$format = $format ?? 'htm';
$thead = $thead ?? true;
$export = $export ?? [];
if(!$export) return;
 
$headings = $headings ?? [];
$cattable = new \App\Views\Htm\Cattable($export, $headings);
$cattable->table_header = $thead;
echo $cattable;

# d($export, $headings, $cattable);
