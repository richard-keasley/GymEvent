<?php $this->extend('default');
 
$this->section('content');?>
<p>Please direct any enquiries about this section to Richard Keasley.</p>
<?php $this->endSection(); 

$this->section('sidebar');
$nav = [
	["{$back_link}/routine", 'Routine sheets']
];
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