<?php $this->extend('default');

$this->section('content');?>
<p>Setup screens for everything. Please make sure you know what you're doing! Ask Richard if you're not sure.</p>
<p>You are viewing device <code><?php echo $device;?></code>.</p>
<?php $this->endSection();

$this->section('sidebar');
$nav = [
	'admin/events', 
	'admin/users', 
	'admin/music', 
	'admin/teamtime', 
	'admin/general', 
	'admin/profile',
	'setup'
];
$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();

$this->endSection();
