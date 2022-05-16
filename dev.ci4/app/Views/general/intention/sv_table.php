<section class="my-3 row justify-content-start">
<div class="col-auto">
<?php
$sv_table = $intention->sv_table();

$val_format = '<div class="text-end">%1.1f</div>';
$arr = [
	'difficulties' => 'difficulty',
	'specials' => 'special requirements',
	'bonuses' => 'bonus'
];
$tbody = []; $total = 0;
foreach($arr as $key=>$label) {
	$sum = array_sum($sv_table['values'][$key]);
	$total += $sum;
	$tbody[] = [$label, sprintf($val_format, $sum)];
}

$table = \App\Views\Htm\Table::load('small');
$table->autoHeading = false;
$table->setFooting(['Start value', sprintf($val_format, $total)]);
echo $table->generate($tbody);
?>
</div>

<?php if($sv_table['errors']) { ?>
<div class="col" style="min-width:20em;">
<div class="mt-3 p-1 alert-danger">
<h5>Routine construction errors</h5>
<ul class="alert-danger list-unstyled p-1"><?php foreach($sv_table['errors'] as $error) {
	printf('<li>%s</li>', $error);
} ?>
</ul>
</div>
</div>
<?php } ?>
</section>
<?php 
# d($sv_table); 
