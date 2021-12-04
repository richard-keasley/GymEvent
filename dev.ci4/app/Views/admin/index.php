<?php $this->extend('default');

$this->section('content');?>
<p>Setup screens for everything. Please make sure you know what you're doing! Ask Richard if you're not sure.</p>
<p>You are viewing device <code><?php echo $device;?></code>.</p>
<?php $this->endSection();

$this->section('sidebar');
$nav = [];
foreach(['events', 'users', 'music', 'teamtime', 'general', 'profile'] as $controller) {
	$nav[] = "admin/{$controller}";
}
$nav[] = 'setup';
echo view('includes/navbar', ['nav'=>$nav]);

$this->endSection();
