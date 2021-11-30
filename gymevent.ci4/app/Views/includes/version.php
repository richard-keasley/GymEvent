<section class="card bg-light" style="max-width:14em;">
<div class="card-header">Versions</div>
<div class="card-body">
<?php
$tbody = [
	['component', '<abbr title="version">v</abbr>'],
	['PHP', phpversion()],
	['CodeIgniter', \CodeIgniter\CodeIgniter::CI_VERSION],
	['Bootstrap', '5.1.0'],
	['Bootstrap icons', '1.5.0'],
	['jQuery', '3.6.0']
];
$table = new \CodeIgniter\View\Table();
$table->setTemplate(['table_open' => '<table class="table table-sm">']);
echo $table->generate($tbody);
?>
<p>PHP version 7.3 or newer is required, with the *intl* extension and *mbstring* extension installed.</p>
</div>
</section>