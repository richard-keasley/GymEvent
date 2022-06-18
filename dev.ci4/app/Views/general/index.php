<?php $this->extend('default');
$can_edit = \App\Libraries\Auth::check_path('admin/general');
$app_path = 'app/general';
 
$this->section('content');
?>
<div class="item-image"><img src="<?php echo base_url("{$app_path}/logo.png");?>"></div>
<p>Discover more about General Gymnastics in the South-east region.</p>
<p>Please tell Kim (Brighton) or Dave (Pegasus) if you see a problem on these pages.</p>

<h4>Downloads</h4>
<?php 
$files = new \CodeIgniter\Files\FileCollection();
$files->addDirectory(FCPATH . $app_path);
$files->removePattern('#logo\.#i');
if(!$can_edit) $files->removePattern('#\.xls#i');

$downloads = new \App\Views\Htm\Downloads($files);
echo $downloads->htm();

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