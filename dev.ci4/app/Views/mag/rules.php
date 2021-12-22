<?php $this->extend('default');
 
$this->section('content');?>
<p><?php echo $ruleset->description;?></p>

<div class="d-md-flex">
<section class="border m-1 p-1">
<h4>Element values</h4>
<ul class="list-group"><?php
foreach($ruleset->routine['difficulties'] as $difficulty=>$value) {
	printf('<li class="list-group-item"><strong>%s:</strong>  %2.1f</li>', $difficulty, $value);
}
?></ul>
</section>

<section class="border m-1 p-1">
<h4>Short routine</h4>
<ul class="list-group"><?php
foreach($ruleset->routine['short'] as $count=>$penalty) {
	printf('<li class="list-group-item"><strong>%s elements:</strong> %2.1f</li>', $count, $penalty);
}
?></ul>
<p>Max elements: <?php echo array_key_last($ruleset->routine['short']); ?></p>
</section>

<section class="border m-1 p-1">
<h4>Groups</h4>
<ul class="list-group"><?php
foreach($ruleset->routine['groups'] as $group_num=>$group_val) { 
	$label = [];
	foreach($group_val as $dif=>$val) $label[] = "{$dif}={$val}";
	printf('<li class="list-group-item"><strong>%s:</strong> %s.</li>', $group_num, implode(', ', $label));
} ?>
</ul>
<p>Max elements: <?php echo $ruleset->routine['group_max'];?></p>
<h4>Dismount</h4>
<p>Group <?php echo $ruleset->routine['group_dis'];?></p>
<ul class="list-group">
<?php foreach($ruleset->exes as $abbr=>$exe) {
	if(!empty($exe['dis_groups'])) { ?>
		<li class="list-group-item">
		<?php
		printf('<strong>%s:</strong> %s.', $exe['name'], implode(', ', $exe['dis_groups']));
		?>
		</li>
	<?php }
}
?></ul>
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

$this->section('top'); 
$action = base_url('mag/routine');
$attr = ['class' => "toolbar"];
$hidden = ['rulesetname' => $rulesetname];
echo form_open($action, $attr, $hidden);
echo \App\Libraries\View::back_link("mag");
?>
<button type="submit" title="create routine" class="btn bi-plus btn-outline-primary"></button>
<?php
echo form_close();
$this->endSection(); 

$this->section('bottom'); ?>
<p>Please tell Richard Keasley if you spot any errors.</p>
<?php $this->endSection(); 
