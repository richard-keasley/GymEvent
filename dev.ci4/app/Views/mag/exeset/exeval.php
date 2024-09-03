<?php 
// delete this. no longer used.
// use ma2\exeset\exeval
return;


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

$errors = [];
$routine_elcount = 0;

// D score 
$dscore = [];
$exe_rules = $exeset->ruleset->$exekey;

switch($exe_rules['method']) {
	case 'tariff':
	$dscore['Tariff'] = floatval($exercise['elements'][0][0]);
	if($dscore['Tariff']>$exe_rules['d_max']) {
		$errors[] = "Tariff can be no higher than {$exe_rules['d_max']}";
	}
	if($dscore['Tariff']<$exe_rules['d_min']) {
		$errors[] = "Invalid tariff";
	}
	break;
		
	case 'routine':
	default:
	$routine_rules = $exeset->ruleset->routine;
	$dscore['Value'] = 0;
	$group_count = [];
	foreach(array_keys($routine_rules['groups']) as $grp_key) {
		$dscore["EG{$grp_key}"] = 0;
		$group_count[$grp_key] = 0;
	}
	
	$dismount_elnum = array_key_last($exercise['elements']); 
	foreach($exercise['elements'] as $elnum=>$element) {
		$rownum = $elnum==$dismount_elnum ? 'D' : $elnum + 1;
		$el_diff = $element[0];
		$el_group = $element[1];
		
		if(!$el_diff && !$el_group) continue; // blank row

		// check difficulty
		$el_value = $routine_rules['difficulties'][$el_diff] ?? 0 ;
		if(!$el_value) {
			$errors[] = "Element {$rownum} has no value";
			continue;
		}
		// check group
		if(!isset($routine_rules['groups'][$el_group])) {
			$errors[] = "Enter a valid group for element {$rownum}";
			continue;
		}
		// check dismount
		if($elnum==$dismount_elnum) {
			// check dismount element is in valid group
			$dis_groups = $exe_rules['dis_groups'];	
			if(!in_array($el_group, $dis_groups)) {
				$errors[] = sprintf('Dismount must be in groups %s.', implode(', ', $dis_groups));
				continue;
			}
		}
		else {
			// check element group is valid
			if(!in_array($el_group, $exe_rules['elm_groups'])) {
				$errors[] = "Dismount (element {$rownum}) must be on last row";
				continue;
			}
		}
		
		// valid element
		$routine_elcount++;
		$group_count[$el_group]++;
		$dscore['Value'] += $el_value;
		
		// group value for this element
		$group_vals = $routine_rules['groups'][$el_group];
		$dis_values = $exe_rules['dis_values'];
		if($elnum==$dismount_elnum && $dis_values) {
			$group_vals = $dis_values;
		}
		foreach($group_vals as $grp_diff=>$grp_worth) {
			$grp_value = $routine_rules['difficulties'][$grp_diff];
			if($el_value>=$grp_value) {
				$dkey = "EG{$el_group}";
				if($dscore[$dkey]<$grp_worth) $dscore[$dkey] = $grp_worth;
			}
		}
	}
	// count elements per group
	foreach($group_count as $grp_key=>$count) {
		if($count > $routine_rules['group_max']) {
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
$table->setFooting(['D', sprintf($score_format, $dscore_total)]);
$tbody = [];
foreach($dscore as $key=>$val) {
	$tbody[] = [
		$key, 
		sprintf($score_format, $val)
	];
} 
echo $table->generate($tbody);

// neutral deductions
$nd = 0;
switch($exe_rules['method']) {
	case 'tariff':
		break;
	case 'routine':
	default:
		$short = $routine_rules['short'][$routine_elcount] ?? 0 ;
		$nd += $short;
}
foreach($exercise['neutrals'] as $nkey=>$nval) { 
	if(!$nval) {
		$neutral = $exe_rules['neutrals'][$nkey]; 
		$nd += $neutral['deduction'];
	}
}
if($nd) printf('<p><strong>ND:</strong> %.1f</p>', $nd);


# $start_value =  + 10;
	
	
