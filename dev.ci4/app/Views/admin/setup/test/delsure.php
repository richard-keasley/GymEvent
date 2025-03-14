<?php $this->extend('default');

$this->section('content');

$delsure = [
	'icon' => "arrow-up",
	'title' => "reveal this",
	'message' => 'reveual this item?',
	'button' => 'primary',
];

$delsure = new \App\Views\Htm\Delsure($delsure);
$del_id = $delsure->request;
if($del_id) {
	echo "deleted {$del_id} ";
}

echo $delsure->button(2);
echo $delsure->button(4);

echo $delsure->form();


$this->endSection();
