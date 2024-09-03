<?php $this->extend('default');

$this->section('sidebar');
$nav = [
	['general', 'General'],
	['general/routine', 'Routine sheets'],
];

foreach($def_rules->exes as $exekey=>$exe) {
	$nav[] = ["admin/general/edit/{$exekey}", "{$exe['name']} skills"];
}

$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();
$this->endSection(); 

$this->section('content');
echo $this->include('general/intro');
$this->endSection(); 