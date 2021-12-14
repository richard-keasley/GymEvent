<?php $this->extend('default');
 
$this->section('content');?>
<p><?php echo $ruleset->description;?></p>

<div class="d-md-flex">
<section class="border m-1 p-1">
<h4>Element values</h4>
<ul class="list-group"><?php
foreach($ruleset->routine['values'] as $key=>$val) {
	printf('<li class="list-group-item"><strong>%s:</strong>  %2.1f</li>', $key, $val);
}
?></ul>
<p>Max elements: <?php echo $ruleset->routine['count'];?></p>
</section>

<section class="border m-1 p-1">
<h4>Short routine</h4>
<ul class="list-group"><?php
foreach($ruleset->routine['short'] as $count=>$penalty) {
	printf('<li class="list-group-item"><strong>%s elements:</strong> %2.1f</li>', $count, $penalty);

}
?></ul>
</section>

<section class="border m-1 p-1">
<h4>Groups</h4>
<p>Max elements: <?php echo $ruleset->routine['max_group'];?></p>
<p>Values:</p>
<ul class="list-group"><?php
foreach($ruleset->routine['group_vals'] as $group_num=>$group_val) { 
	$label = [];
	foreach($group_val as $dif=>$val) $label[] = "{$dif}={$val}";
	printf('<li class="list-group-item"><strong>%s:</strong> %s.</li>', $group_num, implode(', ', $label));
} ?>
</ul>
</section>

<section class="border m-1 p-1">
<h4>Neutral deductions</h4>
<ul class="list-group">
<?php foreach($ruleset->exes as $abbr=>$exe) {
	if(!empty($exe['neutrals'])) { ?>
		<li class="list-group-item">
		<?php
		echo "<h5>{$exe['name']}</h5><ul>";
		foreach($exe['neutrals'] as $neutral) {
			printf('<li>%s (%2.1f)</li>', $neutral['description'], $neutral['deduction']);
		}
		echo '</ul>';
	}
}
?></ul>
</section>

</div>

<?php d($ruleset); ?>

<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link("mag");?>
</div>
<?php $this->endSection(); 

$this->section('bottom'); ?>
<p>Please tell Richard Keasley if you spot any errors.</p>
<?php $this->endSection(); 
