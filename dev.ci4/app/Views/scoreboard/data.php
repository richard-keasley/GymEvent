<?php $this->extend('default');

$this->section('content'); ?>
<section>
<h5>Available tables</h5>
<ul class="nav list flex-column">
<?php
foreach($scoreboard->tables as $varname=>$file) {
	$href = base_url("/setup/scoreboard/data/{$varname}");
	printf('<li><a class="nav-link" href="%s">%s</a></li>', $href, $varname);
}
?>
</ul>
</section>

<section>
<h5>Categorised Scoreboard data</h5>
<div class="table-responsive">
<?php local_echo($scoreboard->get_exesets()); ?>
</div>
<div class="table-responsive">
<?php local_echo($scoreboard->get_discats()); ?>
</div>
</section>
<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top">
	<?php echo \App\Libraries\View::back_link("setup/scoreboard");?>
	<?php 
	$attr = [
		'class' => "nav-link",
		'title' =>"view scoreboard"
	];
	echo anchor(base_url('/scoreboard'), 'view scoreboard', $attr);
	?>
</div>
<?php $this->endSection(); 


function local_echo($tbody) {
	if($tbody) {
		$table = \App\Views\Htm\Table::load();
		foreach($tbody as $rowkey=>$row) {
			foreach($row as $key=>$val) {
				if(is_array($val)) {
					$row = reset($val);
					$table->setHeading(array_keys($row));
					$tbody[$rowkey][$key] = $table->generate($val);
				}
			}
		}
		$row = reset($tbody);
		$table->setHeading(array_keys($row));
		echo $table->generate($tbody);
	}
}
