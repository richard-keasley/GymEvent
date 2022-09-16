<?php $this->extend('default');

$this->section('content');
if($export) {
	include __DIR__ . "/export-{$layout}.php";
}
$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar"><?php 
echo \App\Libraries\View::back_link("entries/view/{$event->id}");

$formats = [
	'csv' => ['class' => "bi-file-spreadsheet", 'title' => "Download scoreboard spreadsheet"],
	'scoretable' => ['class' => "bi-table", 'title' => "Download score tables"],
	# 'sql' => ['class' => "bi-file-code", 'title' => "Download SQL script"],
	'default' => ['class' => "bi-list", 'title' => "View scoreboard data"],
	'run' => ['class' => "bi-list-ol", 'title' => "View running order"]
];
foreach($formats as $req=>$attribs) {
	if($req!=$layout) {
		$href = "/admin/entries/export/{$event->id}/{$req}";
		echo getlink($href, sprintf('<span %s></span>', stringify_attributes($attribs)));
	}
}
?></div>
<?php $this->endSection(); 

