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

$score = [];

switch($exe_rules['method']) {
	case 'tariff':
		$score['tariff'] = floatval($exercise['elements'][0][0]);
		break;
	case 'routine':
	default:
		$dismount_grp = 4;
		$routine_rules = $exeset->ruleset->routine;
		$score['diff'] = 0;
		$group_count = [];
		foreach(array_keys($routine_rules['group_vals']) as $grp_key) {
			$score["EGR {$grp_key}"] = 0;
			$group_count[$grp_key] = 0;
		}
		$element_count = 0;
		$dismount_num = count($exercise['elements']) - 1; 
		foreach($exercise['elements'] as $elnum=>$element) {
			if(isset($routine_rules['group_vals'][$element[1]])) {
				if($elnum==$dismount_num) {
					$grp_key = $dismount_grp;
				}
				else {
					$grp_key = $element[1]==$dismount_grp ? null : $element[1];
				}
			}
			else $grp_key = null;
			if($grp_key) {
				$group_vals = $routine_rules['group_vals'][$grp_key];
				
				$diff = $element[0];
				$el_value = $routine_rules['values'][$diff] ?? 0 ;
				
				if($el_value && $group_vals) {
					$element_count++;
					$group_count[$element[1]]++;
					$score['diff'] += $el_value;
					foreach($group_vals as $grp_diff=>$grp_worth) {
						$value = $routine_rules['values'][$grp_diff];
						if($el_value>=$value) {
							if($grp_key==$dismount_grp) $group_count[$grp_key] = 1;
							$score["EGR {$grp_key}"] = $grp_worth;
						}
					}
				}
			}
		}
		foreach($group_count as $grp_key=>$count) {
			if($count > $routine_rules['max_group']) {
				printf('<p class="alert-danger">Too many elements in group %s</p>', $grp_key);

			}
		}

		$score['pen'] = 0 ;
		$short = $routine_rules['short'][$element_count] ?? 0 ;
		$score['pen'] -= $short;
		foreach($exercise['neutrals'] as $nkey=>$nval) { 
			if(!$nval) {
				$neutral = $exe_rules['neutrals'][$nkey]; 
				$score['pen'] -= $neutral['deduction'];
			}
		}
		// end routine
}

$table = new \CodeIgniter\View\Table();
$table->setTemplate(['table_open' => '<table class="table table-compact">']);
$table->setHeading(['','']);
$table->setFooting(['SV', 10 + array_sum($score)]);
$tbody = [];
foreach($score as $key=>$val) {
	$tbody[] = [$key, number_format($val, 1)];
} 
echo $table->generate($tbody);
