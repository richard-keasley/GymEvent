<?php $this->extend('default');
 
$this->section('content');?>
<p>Please direct any enquiries about this section to Richard Keasley.</p>
<?php $this->endSection(); 

$this->section('sidebar');
$nav = [
	['mag/routine', 'Routine sheet']
];
$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();
?>

<h4>Rules</h4>
<?php

$nav = [];
foreach($index as $key=>$label) {
	$nav[] = ["mag/rules/{$key}", $label];	
}
echo $navbar->htm($nav);

$this->endSection(); 