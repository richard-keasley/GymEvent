<?php 
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

$score = [];

switch($exe_rules['method']) {
	case 'tariff':
		$score['tariff'] = floatval($exercise['elements'][0][0]);
		break;
	case 'routine':
	default:
		$routine_rules = $exeset->ruleset->routine;
		$score['Difficulty'] = 0;
		$group_count = [];
		foreach(array_keys($routine_rules['groups']) as $grp_key) {
			$score["EGR {$grp_key}"] = 0;
			$group_count[$grp_key] = 0;
		}
		$element_count = 0;
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
			// check dismount not used within routine
			if($elnum!=$dismount_elnum && $el_group==$routine_rules['group_dis']) {
				$errors[] = "Dismount (element {$rownum}) must be on last row";
				continue;
			}
			
			// valid element
			$element_count++;
			$group_count[$el_group]++;
			$score['Difficulty'] += $el_value;
			// group value for this element
			$grp_key = $elnum==$dismount_elnum ? $routine_rules['group_dis'] : $el_group ;
			$group_vals = $routine_rules['groups'][$grp_key];
			foreach($group_vals as $grp_diff=>$grp_worth) {
				$grp_value = $routine_rules['difficulties'][$grp_diff];
				if($el_value>=$grp_value) $score["EGR {$grp_key}"] = $grp_worth;
			}
		}
		// count elements per group
		foreach($group_count as $grp_key=>$count) {
			if($count > $routine_rules['group_max']) {
				$errors[] = "Too many elements in group {$grp_key}";
			}
		}
		// neutral deductions
		$score['ND'] = 0 ;
		$short = $routine_rules['short'][$element_count] ?? 0 ;
		$score['ND'] -= $short;
		foreach($exercise['neutrals'] as $nkey=>$nval) { 
			if(!$nval) {
				$neutral = $exe_rules['neutrals'][$nkey]; 
				$score['ND'] -= $neutral['deduction'];
			}
		}
		// end routine
}

if($errors) { ?>
<div class="mt-3 p-1 alert-danger">
<h5>Routine construction errors</h5>
<ul class="list-unstyled">
<?php foreach($errors as $error) printf('<li>%s</li>', $error); ?>
</ul>
</div>
<?php } 

$score_format = '<div class="px-2 text-end">%.1f</div>';
$table = new \CodeIgniter\View\Table();
$table->setTemplate(['table_open' => '<table class="table table-sm">']);
$table->setHeading(['','']);
$table->setFooting(['SV', sprintf($score_format, 10 + array_sum($score))]);
$tbody = [];
foreach($score as $key=>$val) {
	$tbody[] = [
		$key, 
		sprintf($score_format, $val)
	];
} 
echo $table->generate($tbody);