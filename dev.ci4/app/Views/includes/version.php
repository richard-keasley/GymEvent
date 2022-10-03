<section class="card bg-light">
<div class="card-header">Versions</div>
<div class="card-body">
<?php
$tbody = [
	['component', '<abbr title="version">v</abbr>'],
	['PHP', phpversion()],
	['CodeIgniter', \CodeIgniter\CodeIgniter::CI_VERSION],
	['Bootstrap', '5.2.2'],
	['Bootstrap icons', '1.5.0'],
	['jQuery', '3.6.1'],
	['TinyMCE', \App\Views\Htm\Editor::version]
];
$table = \App\Views\Htm\Table::load('small');
echo $table->generate($tbody);
?>
</div>
</section>