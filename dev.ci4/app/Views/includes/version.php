<section class="card bg-light">
<div class="card-header">Versions</div>
<div class="card-body">
<?php
$tbody = [
	['component', '<abbr title="version">v</abbr>'],
	['PHP', phpversion()],
	['CodeIgniter', \CodeIgniter\CodeIgniter::CI_VERSION],
	['Bootstrap', '<span id="bsv"></span>'],
	['Bootstrap icons', '1.5.0'],
	['jQuery', '<span id="jqv"></span>'],
	['TinyMCE', \App\Views\Htm\Editor::version]
];
$table = \App\Views\Htm\Table::load('small');
echo $table->generate($tbody);
?>
<script>
$(function(){
$('#jqv').text(jQuery().jquery);
$('#bsv').text(bootstrap.Tooltip.VERSION);
});
</script>
</div>
</section>