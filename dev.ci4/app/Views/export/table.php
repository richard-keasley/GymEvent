<?php
$format = $format ?? 'htm';
$thead = $thead ?? true;
$export = $export ?? [];
if(!$export) return;

$table = \App\Views\Htm\Table::load('bordered');
$table->autoHeading = false;

if($thead) {
	$thead = current($export);
	$thead = array_keys($thead);
	$table->setHeading($thead);
}

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
