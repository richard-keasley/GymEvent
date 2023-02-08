<?php $this->extend('default');
 
$this->section('content');
echo $this->include('general/intro');
$this->endSection(); 

$this->section('sidebar');
$nav = [
	['general/intention', 'Intention sheets'],
	['general/skills/fx', 'Floor skills'],
	['general/rules/fx', 'Floor rules'],
	['admin/general', 'Admin']
];
$navbar = new \App\Views\Htm\Navbar();
echo $navbar->htm($nav);

$this->endSection(); 