<?php $this->extend('default');

$this->section('content'); ?>
<h5>Categorised Scoreboard data</h5>
<p>Remember the data has been cleaned after reading the database. You may need to edit <code>\App\ThirdParty\scoreboard</code> if the source data structure changes.</p>
<?php 
local_echo($scoreboard->get_exesets()); 
local_echo($scoreboard->get_discats()); 
$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top">
<?php 
echo \App\Libraries\View::back_link("setup/scoreboard");

$attr = [
	'class' => "nav-link",
	'title' =>"view scoreboard"
];
echo anchor('scoreboard', 'view scoreboard', $attr);
?>
<div class="dropdown">
<button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">View DB table</button>
<ul class="dropdown-menu">
<?php
$attrs = [
	'class' => "nav-link"
];
foreach($scoreboard->files as $file) {
	$varname = $file->getBaseName('.php');
	$link = anchor("/setup/scoreboard/data/{$varname}", $varname, $attrs);
	printf('<li class="dropdown-item">%s</li>', $link);
}
?>
</ul>
</div>
<?php

$attrs = [
	'class' => "nav-link",
	'title' => "data filters"
];
echo anchor('setup/scoreboard/filters', 'filters', $attrs);

?>
</div>
<?php $this->endSection(); 

function local_echo($tbody) {
	if($tbody) {
		$table = \App\Views\Htm\Table::load('bordered');
		foreach($tbody as $rowkey=>$row) {
			foreach($row as $key=>$val) {
				if(is_array($val)) {
					$row = reset($val);
					$table->setHeading(array_keys($row));
					$tbody[$rowkey][$key] = sprintf('<div class="table-responsive">%s</div>', $table->generate($val));
				}
			}
		}
		$row = reset($tbody);
		$table->setHeading(array_keys($row));
		echo $table->generate($tbody);
	}
}
