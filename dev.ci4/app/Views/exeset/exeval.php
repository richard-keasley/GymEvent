<?php 
if(empty($exekey)) { 
	echo '<p class="alert alert-danger">No exercise specified</p>';
	return;
}
if(empty($exeset->ruleset->exes[$exekey])) { 
	printf('<p class="alert alert-danger">No rules for %s</p>', $exekey);
	return;
}
if(empty($exeset->exercises[$exekey])) {
	printf('<p class="alert alert-danger">No exercise for %s</p>', $exekey);
	return;
}

$exercise = $exeset->exercises[$exekey];
$exe_rules = $exeset->ruleset->$exekey;
# d($exe_rules);

$errors = [];
$routine_elcount = 0;

// D score 
$dscore = [];

switch($exe_rules['method']) {
	case 'tariff':
	foreach($exercise['elements'] as $elkey=>$element) {
		$tariff = (float) $element[0];
		$el_group = $element[1]; // element group
		$el_num = $elkey + 1;
		if(!$tariff && !$el_group) continue;
		
		// first element is D score for this exercise
		if(!$elkey) $dscore['Tariff'] = $tariff;
		
		if(!isset($exe_rules['groups'][$el_group])) {
			$errors[] = "Enter a valid group for #{$el_num}";
		}
		
		$format = "Tariff #{$el_num} must be %s than %.1f";
		if($tariff>$exe_rules['d_max']) {
			$errors[] = sprintf($format, 'no higher', $exe_rules['d_max']);
		}
		if($tariff<$exe_rules['d_min']) {
			$errors[] = sprintf($format, 'no lower', $exe_rules['d_min']);
		}
	}
	break;
	
	case 'routine':
	default:
	$dscore['Value'] = 0;
	$group_count = [];
	foreach(array_keys($exe_rules['groups']) as $grp_key) {
		$dscore["EG{$grp_key}"] = 0;
		$group_count[$grp_key] = 0;
	}
	
	$dismount_elnum = $exe_rules['dismount'] ?
		array_key_last($exercise['elements']) : 
		9999 ;
	foreach($exercise['elements'] as $elkey=>$element) {
		$el_num = $elkey==$dismount_elnum ? 'D' : $elkey + 1;
		$el_diff = $element[0]; // difficulty letter: A, B, C
		$el_group = $element[1]; // element group
		
		if(!$el_diff && !$el_group) continue; // blank row

		// check difficulty
		$el_value = $exe_rules['difficulties'][$el_diff] ?? 0 ;
		if(!$el_value) {
			$errors[] = "Element {$el_num} has no value";
			continue;
		}
		// check group
		if(!isset($exe_rules['groups'][$el_group])) {
			$errors[] = "Enter a valid group for element {$el_num}";
			continue;
		}
		// check dismount
		if($el_num=='D') {
			// check dismount element is in valid group
			$exevar = $exe_rules['dis_groups'];
			if(!in_array($el_group, $exevar)) {
				$errors[] = sprintf('Dismount must be in groups %s.', implode(', ', $exevar));
				continue;
			}
		}
		else {
			// check element group is valid
			if(!in_array($el_group, $exe_rules['elm_groups'])) {
				$errors[] = "Dismount (element {$el_num}) must be on last row";
				continue;
			}
		}
		
		// valid element
		$routine_elcount++;
		$group_count[$el_group]++;
		$dscore['Value'] += $el_value;
		
		// group value for this element
		$group_vals = $exe_rules['groups'][$el_group];
		if($el_num=='D' && $exe_rules['dis_values']) {
			$group_vals = $exe_rules['dis_values'];
			$el_group = 4 ; // force to be dismount group
		}
		$dkey = "EG{$el_group}";
		// ensure dismount group exists
		if(!isset($dscore[$dkey])) $dscore[$dkey] = 0;
		
		foreach($group_vals as $grp_diff=>$grp_worth) {
			$rules_val = $exe_rules['difficulties'][$grp_diff];
			if($el_value>=$rules_val && $dscore[$dkey]<$grp_worth) {
				$dscore[$dkey] = $grp_worth;
			}
		}
	}
	
	// count elements per group
	foreach($group_count as $grp_key=>$count) {
		if($count > $exe_rules['group_max']) {
			$errors[] = "Too many elements in group {$grp_key}";
		}
	}
	// connection 
	if(array_sum($dscore) && $exe_rules['connection']) {
		$val = $exercise['connection'] ?? 0 ;
		if($val>0) $dscore['Connection'] = floatval($val);
	}
	// end routine
}

if(!$errors) {
// look for repeated elements 
$lastkey = array_key_last($exercise['elements']);
foreach($exercise['elements'] as $elkey=>$element) {
	if(!$element[2]) continue; // no description
	for($cmpkey=$elkey+1; $cmpkey<=$lastkey; $cmpkey++) {
		if($exercise['elements'][$cmpkey]==$element) {
			$errors[] = sprintf('Elements #%u &amp; %u are repeats', $elkey+1, $cmpkey+1);
			break (2);
		}
	}
}	
}

if($errors) { ?>
<div class="p-1 alert alert-danger">
<ul class="list-unstyled m-0">
<?php foreach($errors as $error) printf('<li>%s</li>', $error); ?>
</ul>
</div>
<?php 
return; // no calculation for routine errors
}

$dscore_total = array_sum($dscore);
if(!$dscore_total) return; // empty routine
 
// D score table 
$score_format = '<div class="px-2 text-end">%.1f</div>';
$table = \App\Views\Htm\Table::load('small');
$table->autoHeading = false;

$tbody = [];
foreach($dscore as $key=>$val) {
	$tbody[] = [
		$key, 
		sprintf($score_format, $val)
	];
}
$tfoot = [['<div title="D score">D</div>', $dscore_total]];

// neutral deductions
$nd = 0;
switch($exe_rules['method']) {
	case 'tariff':
	break;
	
	case 'routine':
	default:
	$short = $exe_rules['short'][$routine_elcount] ?? 0 ;
	$nd += $short;
}

foreach($exercise['neutrals'] as $nkey=>$nval) { 
	if(!$nval) {
		$neutral = $exe_rules['neutrals'][$nkey]; 
		$nd += $neutral['deduction'];
	}
}
if($nd) {
	$tfoot[] = ['<div title="neutral deductions">ND</div>', $nd];
}

$tr = [];
foreach(array_keys($tfoot[0]) as $colidx) {
	$column = array_column($tfoot, $colidx);
	if($colidx==1) {
		foreach($column as $key=>$val) {
			$column[$key] = sprintf($score_format, $val);
		}
	}
	$tr[] = implode('', $column);
}
$table->setFooting($tr);

echo $table->generate($tbody);
