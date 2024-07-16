<?php $this->extend('default');
 
$this->section('content');?>
<p>Please direct any enquiries about this section to Richard Keasley.</p>
<?php $this->endSection(); 

$this->section('sidebar');

# ToDo: convert all this from ma2 to mag */
$ma2 = strpos(current_url(), '/ma2')!==false;
$root = $ma2 ? 'ma2' : 'mag' ;

$nav = [
	["{$root}/routine", 'Routine sheet']
];
$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();
?>

<h4>Rules</h4>
<?php

$nav = [];
foreach($index as $key=>$label) {
	$nav[] = ["{$root}/rules/{$key}", $label];	
}
echo $navbar->htm($nav);

$this->endSection(); 