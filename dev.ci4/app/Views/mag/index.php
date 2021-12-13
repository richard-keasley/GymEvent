<?php $this->extend('default');
 
$this->section('content');?>
<p>Please direct any enquiries about this section to Richard Keasley.</p>
<?php $this->endSection(); 

$this->section('sidebar');
$nav = [
	['mag/routine', 'Routine sheet']
];
echo view('includes/navbar', ['nav'=>$nav]);
?>
<h4>Rules</h4>
<?php

$nav = [];
foreach($index as $label) {
	$nav[] = ["mag/rules/{$label}", $label];	
}
echo view('includes/navbar', ['nav'=>$nav]);

$this->endSection(); 