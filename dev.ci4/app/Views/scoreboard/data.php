<?php $this->extend('default');

$this->section('content'); 
$scoreboard = new \App\ThirdParty\scoreboard;
$files = new CodeIgniter\Files\FileCollection([APPPATH . 'ThirdParty/scoreboard']);
$tablenames = [];
foreach($files as $file) $tablenames[] = $file->getBasename('.php');

/*
Future project

This will read the scoreboard database, so we can put in the current values

$success = $scoreboard->init_db();
foreach($tablenames as $tablename) {
	$sql = "SELECT * FROM `{$tablename}`";
	$res = $scoreboard->query($sql);
	d($res);
	echo '<pre> $ret = ' . var_export($res, 1) . '; </pre>';
}
*/
?>

<section>
<h5>Available tables</h5>
<ul class="list">
<?php
foreach($tablenames as $tablename) {
	printf('<li><code>%s</code> %s</li>', $tablename, $scoreboard->get_time($tablename, 'j F Y'));
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
