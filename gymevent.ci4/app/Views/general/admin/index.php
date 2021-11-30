<?php $this->extend('default');
 
$this->section('content');?>
<p>Discover more about General Gymnastics in the South-east region.</p>
<?php $this->endSection(); 

$this->section('sidebar');
$nav = [
	['general/intention', 'Intention sheets'],
	['admin/general/rules/fx', 'Floor rules']
];
echo view('includes/navbar', ['nav'=>$nav]);
$this->endSection(); 