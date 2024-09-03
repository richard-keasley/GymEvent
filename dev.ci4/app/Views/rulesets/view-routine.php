<div sclass="d-md-flex flex-wrap">
<?php
# d($exe_rules); 

$exe_var = $exe_rules['difficulties'] ?? null;
if($exe_var) { ?>
<section class="border m-1 p-1">
<h4>Element values</h4>
<div class="row"><?php
$format = '<div class="col col-auto" style="min-width:8em;">%s=%2.1f</div>';
foreach($exe_var as $key=>$val) {
	printf($format, $key, $val);
}
?></div>
</section>	
<?php }

?>
<section class="border m-1 p-1">
<h4>Groups</h4>
<div class="row"><?php
$format = '<div class="col col-auto" style="min-width:10em;"><strong>%s:</strong> %s</div>';
foreach($exe_rules['groups'] as $group_num=>$group_vals) { 
	$label = [];
	foreach($group_vals as $dif=>$val) $label[] = "{$dif}={$val}";
	printf($format, $group_num, implode(' ', $label));
}
?></div>
<div class="row"><?php
$list = [];
$list['max_elements'] = $exe_rules['group_max'] ?? null;

$list['dismount_groups'] = implode(', ', $exe_rules['dis_groups']);

$values = [];
foreach($exe_rules['dis_values'] as $dif=>$val) $values[] = "{$dif}={$val}";
$list['dismount_values'] = implode(' ', $values);

$format = '<div class="col col-auto" style="min-width:12em;"><strong>%s:</strong> %s</div>';
foreach($list as $key=>$item) {
	if(!$item) continue;
	printf($format, humanize($key), $item);
}
?></div>
</section>

<section class="border m-1 p-1">
<h4>Short routine</h4>
<div class="row"><?php
$format = '<div class="col col-auto" style="min-width:8em;"><strong>%s:</strong> %2.1f</div>';
foreach($exe_rules['short'] as $count=>$penalty) {
	printf($format, $count, $penalty);
}
?></div>

<p><strong>Max elements:</strong> <?php echo array_key_last($exe_rules['short']); ?></p>
</section>

<?php
$exe_var = $exe_rules['neutrals'] ?? null;
if($exe_var) { ?>
<section class="border m-1 p-1">
<h4>Penalties</h4>
<div class="row"><?php
$format = '<div class="col col-auto" style="min-width:12em;"><strong>%s:</strong> %2.1f</div>';
foreach($exe_var as $key=>$neutral) {
	printf($format, $neutral['description'], $neutral['deduction']);
} ?></div>
</section>	
<?php }
?></div>
