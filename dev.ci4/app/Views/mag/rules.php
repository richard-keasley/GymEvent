<?php $this->extend('default');
 
$this->section('content');?>
<p><?php echo $ruleset->description;?></p>

<div class="d-flex">
<section class="border m-1 p-1">
<h4>Element values</h4>
<ul class="list-group"><?php
foreach($ruleset->values as $key=>$val) {
	printf('<li class="list-group-item"><strong>%s:</strong>  %2.1f</li>', $key, $val);
}
?></ul>
<p>Max elements: <?php echo $ruleset->count;?></p>
</section>

<section class="border m-1 p-1">
<h4>Short routine</h4>
<ul class="list-group"><?php
foreach($ruleset->short as $count=>$penalty) {
	printf('<li class="list-group-item"><strong>%s elements:</strong> %2.1f</li>', $count, $penalty);

}
?></ul>
</section>

<section class="border m-1 p-1">
<h4>Groups</h4>
<p>Max elements: <?php echo $ruleset->max_group;?></p>

<h4>Dismount</h4>
<ul class="list-group"><?php
foreach($ruleset->dismount as $key=>$val) {
	printf('<li class="list-group-item"><strong>%s:</strong> %2.1f</li>', $key, $val);

}
?></ul>
</section>

<section class="border m-1 p-1">
<h4>Neutral deductions</h4>
<?php foreach($ruleset->neutral_deductions as $app=>$rows) {
	if($rows) {
		echo "<h5>{$app}</h5>";
		foreach($rows as $row) {
			printf('<li class="list-group-item">%s (%2.1f)</li>', $row['description'], $row['deduction']);
		}
	}
}
?>
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
