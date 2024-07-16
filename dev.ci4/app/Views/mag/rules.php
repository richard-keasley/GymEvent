<?php $this->extend('default');
 
$this->section('content');?>
<p><?php echo $ruleset->description;?>. 
	<span class="text-muted fst-italic">updated <?php 
	$time = new \CodeIgniter\I18n\Time($ruleset->version);
	echo $time->toLocalizedString('d MMM yyyy'); ?></span>
</p>

<div class="d-md-flex flex-wrap">

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
foreach($ruleset->routine['groups'] as $group_num=>$group_vals) { 
	$label = [];
	foreach($group_vals as $dif=>$val) $label[] = "{$dif}={$val}";
	printf('<li class="list-group-item"><strong>%s:</strong> %s</li>', $group_num, implode(' ', $label));
} ?>
</ul>
<p>Max elements: <?php echo $ruleset->routine['group_max'];?></p>
</section>

<section class="border m-1 p-1" style="max-width:20em">
<h4>Dismount group / value</h4>
<ul class="list-group">
<?php foreach($ruleset->exes as $abbr=>$exe) {
	if(!empty($exe['dis_groups'])) { ?>
		<li class="list-group-item"><?php
		printf('<strong>%s:</strong> %s', $exe['name'], implode(', ', $exe['dis_groups']));
		$group_vals = $exe['dis_values'] ?? null;
		if($group_vals) {
			$label = [];
			foreach($group_vals as $dif=>$val) $label[] = "{$dif}={$val}";
			printf('<br>%s', implode(' ', $label));
		}
		?></li>
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
?>
</ul>
</section>

<?php foreach($ruleset->exes as $abbr=>$exe) {
	if($exe['method']=='tariff') { ?>
	<section class="border m-1 p-1">
	<h4><?php echo $exe['name']; ?></h4>
	<ul class="list-group">
		<li class="list-group-item">
			<strong>Max tariff:</strong>
			<?php printf('%.1f', $exe['d_max']);?>
		</li>
		<li class="list-group-item">
			<strong>Exercise count:</strong>
			<?php printf('%u', $exe['exe_count']);?>
		</li>
	</ul>	
	</section>
	<?php } 
} ?>

</div>

<?php 
# d($ruleset); 
$this->endSection(); 

$this->section('top');

# ToDo: convert all this from ma2 to mag */
$ma2 = strpos(current_url(), '/ma2/')!==false;

if($ma2) { ?>
<div class="toolbar"><?php
echo \App\Libraries\View::back_link("mag");
$attrs = [
	'class' => "btn btn-primary bi bi-pencil-square",
	'title' => "Edit stored routines",
	'href' => "/ma2/routine",
];
printf('<a %s></a>', stringify_attributes($attrs));

$names = array_keys($index);
$key = array_search($rulesetname, $names);
if($key!==false) {
	if(isset($names[$key - 1])) {
		$name = $names[$key - 1];
		$attrs = [
			'class' => "btn btn-outline-dark bi-chevron-left",
			'href' => "/ma2/rules/{$name}",
			'title' => $index[$name]
		];
		printf('<a %s></a>', stringify_attributes($attrs));
	}
	
	if(isset($names[$key + 1])) {
		$name = $names[$key + 1];
		$attrs = [
			'class' => "btn btn-outline-dark bi-chevron-right",
			'href' => "/ma2/rules/{$name}",
			'title' => $index[$name]
		];
		printf('<a %s></a>', stringify_attributes($attrs));
	}
}
?></div>	
<?php }

else { // start old version
$attr = ['class' => "toolbar"];
$hidden = ['rulesetname' => $rulesetname];
echo form_open('mag/routine', $attr, $hidden);
echo \App\Libraries\View::back_link("mag");
?>
<button type="submit" title="create routine" class="btn btn-outline-primary"><i class="bi-plus-circle-fill text-success"></i></button>
<?php
$names = array_keys($index);
$key = array_search($rulesetname, $names);
if($key!==false) {
	if(isset($names[$key - 1])) {
		$name = $names[$key - 1];
		printf('<a class="btn btn-outline-dark bi-chevron-left" href="/mag/rules/%s" title="%s"></a>', $name, $index[$name]);
	}
	if(isset($names[$key + 1])) {
		$name = $names[$key + 1];
		printf('<a class="btn btn-outline-dark bi-chevron-right" href="/mag/rules/%s" title="%s"></a>', $name, $index[$name]);
	}
}
echo form_close();
} // end old version
$this->endSection(); 

$this->section('bottom'); ?>
<p>Please tell Richard Keasley if you spot any errors.</p>
<?php $this->endSection(); 
