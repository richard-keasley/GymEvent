<?php $this->extend('default');

$logo_src = '';
$files = \App\Libraries\General::files();
// extract logo if it's there
$pattern = 'logo.';
foreach($files as $file) {
	$filename = $file->getPathname();
	if(strpos($filename, $pattern)) {
		$files->removeFile($filename);
		$logo_src = substr($filename, strlen(FCPATH));
	}
}
 
$this->section('content');
if($logo_src) { ?>
	<div class="item-image">
	<img src="<?php echo base_url($logo_src);?>">
	</div>
<?php } ?>

<p>Discover more about General Gymnastics in the South-east region.</p>
<p>Please tell Kim (Brighton) or Dave (Pegasus) if you see a problem on these pages.</p>


<?php 
if(count($files)) {
	$downloads = new \App\Views\Htm\Downloads($files);
	echo '<h4>Downloads</h4>' . $downloads->htm() ;
}

$this->endSection(); 

$this->section('sidebar');
$nav = [
	['admin/general', '<span class="bi bi-gear"></span> admin'],
	["{$back_link}/routine", 'Routine sheets'],
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