<?php 
if(empty($exekey)) { 
	echo '<p class="alert-danger">No exercise specified</p>';
	return;
}
if(empty($exeset->ruleset->exes[$exekey])) { 
	printf('<p class="alert-danger">No rules for %s</p>', $exekey);
	return;
}
if(empty($exeset->exercises[$exekey])) {
	printf('<p class="alert-danger">No exercise for %s</p>', $exekey);
	return;
}

$exe_rules = $exeset->ruleset->exes[$exekey];
$exercise = $exeset->exercises[$exekey];

$errors = [];
$routine_elcount = 0;

// D score 
$score = [];
switch($exe_rules['method']) {
	case 'tariff':
		# d($exe_rules);
		$score['Tariff'] = floatval($exercise['elements'][0][0]);
		if($score['Tariff']>$exe_rules['d_max']) {
			$errors[] = "Tariff can be no higher than {$exe_rules['d_max']}";
		}
		break;
	case 'routine':
	default:
		$routine_rules = $exeset->ruleset->routine;
		$score['Value'] = 0;
		$group_count = [];
		foreach(array_keys($routine_rules['groups']) as $grp_key) {
			$score["EG{$grp_key}"] = 0;
			$group_count[$grp_key] = 0;
		}
		if($exe_rules['connection']) {
			$val = $exercise['connection'] ?? 0 ;
			if($val) $score['Connection'] = floatval($val);
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
				if(!in_array($el_group, $exe_rules['dis_groups'])) {
					$errors[] = sprintf('Dismount must be in groups %s.', implode(', ', $exe_rules['dis_groups']));
					continue;
				}
			}
			else {
				// check dismount group not used within routine
				if($el_group==$routine_rules['group_dis']) {
					$errors[] = "Dismount (element {$rownum}) must be on last row";
					continue;
				}
			}
			
			// valid element
			$routine_elcount++;
			$group_count[$el_group]++;
			$score['Value'] += $el_value;
			// group value for this element
			$grp_key = $elnum==$dismount_elnum ? $routine_rules['group_dis'] : $el_group ;
			$group_vals = $routine_rules['groups'][$grp_key];
			foreach($group_vals as $grp_diff=>$grp_worth) {
				$grp_value = $routine_rules['difficulties'][$grp_diff];
				if($el_value>=$grp_value) $score["EG{$grp_key}"] = $grp_worth;
			}
		}
		// count elements per group
		foreach($group_count as $grp_key=>$count) {
			if($count > $routine_rules['group_max']) {
				$errors[] = "Too many elements in group {$grp_key}";
			}
		}
		// end routine
}

if($errors) { ?>
<div class="mt-3 p-1 alert-danger border border-danger rounded">
<ul class="list-unstyled m-0">
<?php foreach($errors as $error) printf('<li>%s</li>', $error); ?>
</ul>
</div>
<?php }

if(array_sum($score)) {
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
	if($nd) $score['ND'] = -$nd;
	
	// score table 
	$start_value = array_sum($score) + 10;
	$score_format = '<div class="px-2 text-end">%.1f</div>';
	$table = new \CodeIgniter\View\Table();
	$table->setTemplate(['table_open' => '<table class="table table-sm">']);
	$table->autoHeading = false;
	$table->setFooting(['SV', sprintf($score_format, $start_value)]);
	$tbody = [];
	foreach($score as $key=>$val) {
		$tbody[] = [
			$key, 
			sprintf($score_format, $val)
		];
	} 
	echo $table->generate($tbody);
}