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
	'style' => "max-width:10em",
	'onchange' => "$('#selector').submit();"
];
echo form_dropdown($input);

$format = '<button type="submit" name="download" value="%s" class="btn btn-secondary" title="Export as %s"><i class="bi-%s"></i></button>';
foreach($filetypes as $filetype=>$icon) {
	printf($format, $filetype, strtoupper($filetype), $icon);
}

?>

</form>
<?php $this->endSection(); 
