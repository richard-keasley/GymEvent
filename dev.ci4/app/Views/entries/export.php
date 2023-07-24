<?php $this->extend('default');

$this->section('content');
#d($source);
if($layout && $source) {
	?>
	<p class="alert alert-light p-1"><?php
	echo match($source) {
		'score_table' => 'Manual score sheets',
		'scoreboard' => 'Data for importing into the scoreboard app',
		'entry_list' => 'Numbered list of entries',
		default => humanize($source)
	};
	?></p>
	<?php
	echo $this->include("export/{$layout}");
}
$this->endSection(); 

$this->section('top'); ?>
<form method="GET" id="selector" class="toolbar sticky-top"><?php 
echo \App\Libraries\View::back_link("entries/view/{$event->id}");

$input = [
	'name' => "source",
	'class' => "form-control",
	'options' => $source_opts,
	'selected' => $source,
	'style' => "max-width:10em"
];
echo form_dropdown($input);
?>
<button type="submit" name="action" value="download" class="btn btn-secondary" title="Download this as spreadsheet"><i class="bi bi-table"></i></button>
<script>
$(function() {
$('[name=source]').change(function() { $('#selector').submit(); });
});
</script>
</form>
<?php $this->endSection(); 
