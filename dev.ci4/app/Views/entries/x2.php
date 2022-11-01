<?php $this->extend('default');

$this->section('content');
if($layout) {
	echo view("export/{$layout}", $this->data);
}
$this->endSection(); 

$this->section('top'); ?>
<form method="GET" id="selector" class="toolbar"><?php 
echo \App\Libraries\View::back_link("entries/view/{$event->id}");

$options = [];
foreach($sources as $key) $options[$key] = $key;
$input = [
	'name' => "source",
	'class' => "form-control",
	'options' => $options,
	'selected' => $source,
	'style' => "max-width:10em"
];
echo form_dropdown($input);
?>
<button type="submit" name="action" value="download"class="btn btn-secondary" title="Download this as spreadsheet"><i class="bi bi-table"></i></button>
<script>
$(function() {
$('[name=source]').change(function() { $('#selector').submit(); });
});
</script>
</form>
<?php $this->endSection(); 
