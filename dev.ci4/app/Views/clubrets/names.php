<?php $this->extend('default');

$this->section('content');
if($names) {
	$table = \App\Views\Htm\Table::load('responsive');
	$table->setHeading(array_keys($names[0]));
	echo $table->generate($names);
}
$this->endSection();

$this->section('top');
$attr = [
	'class' => "toolbar sticky-top"
];
echo form_open(current_url(), $attr);
echo \App\Libraries\View::back_link($back_link); 
?>
<button type="submit" name="download" value="names" class="btn btn-secondary" title="Download this spreadsheet"><i class="bi-download"></i></button>
<?php 
echo form_close();
$this->endSection();
