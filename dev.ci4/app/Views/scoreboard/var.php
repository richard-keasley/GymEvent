<?php $this->extend('default');

$this->section('content'); ?>
<p>This is the current data table as read from the scoreboard database. You can import this data into GymEvent for use event entries. The data shown here is cleaned before use.</p>
<p><strong>Import date for this table:</strong> 
<?php echo $scoreboard->get_time($varname, 'd MMMM YYY');?>.
</p>

<?php
if($tbody) {
	$table = \App\Views\Htm\Table::load();
	$table->setHeading(array_keys($tbody[0]));
	echo $table->generate($tbody);
}

$this->endSection();

$this->section('top');
$attrs = ['class' => "toolbar sticky-top"];
$hidden = [];
echo form_open('', $attrs, $hidden); 
echo \App\Libraries\View::back_link("setup/scoreboard/data");
?>
<div class="dropdown">
<button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Select</button>
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

<button class="btn btn-primary" title="import this table" type="submit" name="import" value="1"><i class="bi bi-box-arrow-in-down-right"></i></button>
<?php
echo form_close();
$this->endSection();
