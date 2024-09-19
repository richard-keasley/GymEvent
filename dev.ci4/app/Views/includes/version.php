<section class="card bg-light">
<div class="card-header">Versions</div>
<div class="card-body">
<?php
$vars = [
	'PHP' => phpversion(),
	'CodeIgniter' => \CodeIgniter\CodeIgniter::CI_VERSION,
	'Bootstrap' => '<span id="bsv"></span>',
	'Bootstrap icons' => '1.5.0',
	'jQuery' => '<span id="jqv"></span>',
	'TinyMCE' => \App\Views\Htm\Editor::version,
	'Minify' => '1.3.73',
	'Path-converter' => '1.1.3'
];
echo new \App\Views\Htm\Vartable($vars);
?>
<script>
$(function() {
$('#jqv').text(jQuery().jquery);
$('#bsv').text(bootstrap.Tooltip.VERSION);
});
</script>
</div>
</section>