<?php 
$buffer = [];
foreach($skills['skills'] as $skill) {
	$buffer[$skill['group']][$skill['id']] = [$skill['description'], $skill['tariff']];
}
# d($buffer);

$table = \App\Views\Htm\Table::load();
foreach($buffer as $grp=>$grp_skills) {
	$grp_label = $exe_rules['group_labels'][$grp] ?? '' ;
	if($grp_label) $grp_label = ": {$grp_label}";
	echo "<h4>Group {$grp}{$grp_label}</h4>";
	$table->autoHeading = false;
	echo $table->generate($grp_skills);
}

/*
$data = $skills['skills'];
$table = \App\Views\Htm\Table::load('bordered');
$row = current($data);
$table->setHeading(array_keys($row));
echo $table->generate($data);
*/