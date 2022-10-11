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
echo anchor(base_url('/scoreboard'), 'view scoreboard', $attr);
?>
<div class="dropdown">
<button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">View DB table</button>
<ul class="dropdown-menu">
<?php
foreach($scoreboard->files as $file) {
	$varname = $file->getBaseName('.php');
	$href = base_url("/setup/scoreboard/data/{$varname}");
	printf('<li class="dropdown-item"><a class="nav-link" href="%s">%s</a></li>', $href, $varname);
}
?>
</ul>
</div>

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
