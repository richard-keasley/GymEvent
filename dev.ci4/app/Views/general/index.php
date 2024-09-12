<?php $this->extend('default');
 
$this->section('content');
echo $this->include('general/intro');
$this->endSection(); 

$this->section('sidebar');
$nav = [
	["{$back_link}/routine", 'Routine sheets']
];

$def_rules = new \App\Libraries\Rulesets\Fv_gold;	
foreach($def_rules->exes as $exekey=>$exe) {
	$nav[] = ["general/skills/{$exekey}", "{$exe['name']} skills"];
}

$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();
?>

<h4>Rules</h4>
<?php

$nav = [];
foreach($rule_options as $key=>$label) {
	$nav[] = ["{$back_link}/rules/{$key}", $label];	
}
echo $navbar->htm($nav);

$this->endSection(); 