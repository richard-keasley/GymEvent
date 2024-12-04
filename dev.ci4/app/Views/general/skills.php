<?php $this->extend('default');
 
$this->section('content');
$include = __DIR__ . "/skills-{$skills['method']}.php";
if(is_file($include)) include $include;
else printf('<p class="alert alert-danger">%s not found</p>', $skills['method']);
$this->endSection(); 

$this->section('top');?>
<div class="toolbar sticky-top"><?php 
echo \App\Libraries\View::back_link($back_link);

$attrs = [
	'class' => "btn btn-primary bi bi-pencil-square",
	'title' => "Edit routines",
	'href' => site_url("{$back_link}/routine"),
];
printf('<a %s></a>', stringify_attributes($attrs));

$names = array_keys($rule_options);
$key = array_search($ruleset->name, $names);
$stub = "{$back_link}/skills/{$exekey}/";
if($key!==false) {
	$key_last = array_key_last($names);
	$key_first = array_key_first($names);	

	$name = $names[$key - 1] ?? $names[$key_last];
	$attrs = [
		'class' => "btn btn-outline-dark bi-chevron-left",
		'href' => site_url($stub . substr($name, 3)),
		'title' => $rule_options[$name]
	];
	printf('<a %s></a>', stringify_attributes($attrs));

	$name = $names[$key + 1] ?? $names[$key_first];
	$attrs = [
		'class' => "btn btn-outline-dark bi-chevron-right",
		'href' => site_url($stub . substr($name, 3)),
		'title' => $rule_options[$name]
	];
	printf('<a %s></a>', stringify_attributes($attrs));

}

$href = [$back_link, 'skills', '__', substr($ruleset->name, 3)];
$format = str_replace('__', '%s', site_url($href));
$def_rules = new \App\Libraries\Rulesets\Fv_gold;	
foreach($def_rules->exes as $key=>$exe) {
	if($key==$exekey) continue;
	$attrs = [
		'class' => "btn btn-outline-dark",
		'href' => sprintf($format, $key),
		'title' => $exe['name']
	];
	printf('<a %s>%s</a>', stringify_attributes($attrs), $key);
}

?></div>
<?php 
$this->endSection(); 
