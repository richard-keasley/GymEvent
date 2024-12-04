<?php $this->extend('default');
 
$this->section('content'); ?>
<p><?php echo $ruleset->description;?>. 
	<span class="text-muted fst-italic">updated <?php 
	$time = new \CodeIgniter\I18n\Time($ruleset->version);
	echo $time->toLocalizedString('d MMM yyyy'); ?></span>
</p>

<?php
$accordion = new \App\Views\Htm\Accordion;
foreach($ruleset->exes as $exekey=>$exercise) {
	ob_start();
	$data = ['exe_rules' => $ruleset->$exekey];
	echo view("rulesets/view-{$data['exe_rules']['method']}", $data);
	$accordion->set_item($data['exe_rules']['name'], ob_get_clean());
}
echo $accordion;
$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar"><?php
echo \App\Libraries\View::back_link($back_link);
$attrs = [
	'class' => "btn btn-primary bi bi-pencil-square",
	'title' => "Edit routines",
	'href' => site_url("{$back_link}/routine"),
];
printf('<a %s></a>', stringify_attributes($attrs));

$names = array_keys($rule_options);
$key = array_search($ruleset->name, $names);
$stub = "{$back_link}/rules/";
if($key!==false) {
	$key_last = array_key_last($names);
	$key_first = array_key_first($names);	

	$name = $names[$key - 1] ?? $names[$key_last];
	$attrs = [
		'class' => "btn btn-outline-dark bi-chevron-left",
		'href' => site_url($stub . $name),
		'title' => $rule_options[$name]
	];
	printf('<a %s></a>', stringify_attributes($attrs));

	$name = $names[$key + 1] ?? $names[$key_first];
	$attrs = [
		'class' => "btn btn-outline-dark bi-chevron-right",
		'href' => site_url($stub . $name),
		'title' => $rule_options[$name]
	];
	printf('<a %s></a>', stringify_attributes($attrs));

}
?></div>	
<?php 
$this->endSection(); 

$this->section('bottom'); ?>
<p>Please tell Richard Keasley if you spot any errors.</p>
<?php $this->endSection(); 
