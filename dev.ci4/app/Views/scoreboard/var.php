<?php $this->extend('default');

$this->section('content'); ?>
<p>This is the current data table as read from the scoreboard database. YOu can import this data into GymEvent for use event entries.</p>

<?php
$attr = ['class' => "toolbar sticky-top"];
$hidden = [];
echo form_open(base_url(uri_string()), $attr, $hidden); 
echo \App\Libraries\View::back_link("setup/scoreboard/data");
$attr = [
	'class' => "nav-link",
	'title' =>"view scoreboard"
];
echo anchor(base_url('/scoreboard'), 'view scoreboard', $attr);
?>
<button class="btn btn-primary" title="import this table" type="submit" name="import" value="1"><i class="bi bi-box-arrow-in-down-right"></i></button>
<?php
echo form_close();

if($tbody) {
	$table = \App\Views\Htm\Table::load();
	$table->setHeading(array_keys($tbody[0]));
	echo $table->generate($tbody);
}

$this->endSection();