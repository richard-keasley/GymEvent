<?php $this->extend('default');
 
$this->section('content');
echo $this->include('general/intro');
$this->endSection(); 

$this->section('sidebar');
$nav = [
	['general/intention', 'Intention sheets'],
	['admin/general/rules/fx', 'Floor rules'],
	['general', 'Front end']
];
$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();

$this->endSection(); 