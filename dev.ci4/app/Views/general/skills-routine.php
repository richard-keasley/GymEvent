<?php 

$buffer = [];
foreach($skills['skills'] as $skill) {
	$buffer[$skill['group']][$skill['difficulty']][$skill['id']] = $skill['description'];
}
foreach($buffer as $grp=>$grp_skills) {
	$grp_label = $exe_rules['group_labels'][$grp] ?? '' ;
	if($grp_label) $grp_label = ": {$grp_label}";
	
	echo "<h4>Group {$grp}{$grp_label}</h4>";

	echo '<div class="row">';
	foreach($grp_skills as $diff=>$diff_skills) {
		echo '<div class="col-auto" style="max-width:23em">';
		printf('<h5>%s</h5><ul class="list list-unstyled">', $diff);
		foreach($diff_skills as $id=>$diff_skill) {
			printf('<li class="my-2">%s</li>', new \App\Views\Htm\Pretty($diff_skill));
		}
		echo '</ul></div>';
	}
	echo '</div>';
}
