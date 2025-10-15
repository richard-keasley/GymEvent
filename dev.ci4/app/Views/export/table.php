<?php
$export = $export ?? [];
$thead = $thead ?? true;
$tfoot = $tfoot ?? false;

if(!$export) return;

$table = \App\Views\Htm\Table::load('bordered');
$table->autoHeading = false;

if($thead) {
	$thead = current($export);
	$thead = array_keys($thead);
	$table->setHeading($thead);
}

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
