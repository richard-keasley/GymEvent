<section class="card bg-light">
<div class="card-header">Versions</div>
<div class="card-body">
<?php
$tbody = [
	['component', '<abbr title="version">v</abbr>'],
	['PHP', phpversion()],
	['CodeIgniter', \CodeIgniter\CodeIgniter::CI_VERSION],
	['Bootstrap', '5.1.0'],
	['Bootstrap icons', '1.5.0'],
	['jQuery', '3.6.0'],
	['TinyMCE', '6.0.0']
];
$table = new \CodeIgniter\View\Table();
$table->setTemplate(['table_open' => '<table class="table table-sm">']);
echo $table->generate($tbody);
?>
</div>
</section>