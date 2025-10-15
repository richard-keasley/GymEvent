<?php
$export = $export ?? [];
$thead = $thead ?? true;
$headings = $headings ?? [];

if(!$export) return;
 
$cattable = new \App\Views\Htm\Cattable($export, $headings);
$cattable->table_header = $thead;
echo $cattable;

# d($export, $headings, $cattable);
