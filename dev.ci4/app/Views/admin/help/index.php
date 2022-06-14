<?php $this->extend('default');

$this->section('content');?>
<div class="item-image"><img src="<?php echo base_url('/app/profile/logo.png');?>"></div>
<p>All the help files are listed here. Please ask Richard if there is something more you need to know.</p>

<?php $this->endSection();

$this->section('sidebar');
$nav = [];
foreach($stubs as $key=>$stub) {
	$nav[] = ["/admin/help/view/{$key}", $stub];
}
$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();
$this->endSection();
