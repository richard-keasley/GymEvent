<?php $this->extend('default');
 
$this->section('content');?>
<p>Discover more about General Gymnastics in the South-east region.</p>

<h4>Downloads</h4>

<ul class="list-group"><?php 
$folder = 'app/general';
foreach(glob(FCPATH . $folder . '/*') as $file) {
	$download = "/{$folder}/" . basename($file);
	printf('<li class="list-group-item">%s</li>', \App\Libraries\View::download($download));
} 
?></ul>

<?php $this->endSection(); 

$this->section('sidebar');
$nav = [
	['general/intention', 'Intention sheets'],
	['general/skills/fx', 'Floor skills'],
	['admin/general/rules/fx', 'Floor rules']
];
$navbar = new \App\Views\Htm\Navbar();
echo $navbar->htm($nav);

$this->endSection(); 