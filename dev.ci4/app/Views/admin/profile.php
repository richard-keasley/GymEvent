<?php $this->extend('default');

$this->section('content'); ?>
<ul class="nav">
<li><img src="/favicon.ico" title="favicon"></li>
<?php 
$path =  '/app/profile';
foreach(glob(FCPATH . $path .'/*') as $file) {
	$basename = basename($file);
	if(strpos($basename, 'index.')!==0) {
		printf('<li><img src="%1$s/%2$s" title="%2$s"></li>', $path, basename($file));
	}
} 
?></ul>

<p><span class="p-1 bg-light bg-opacity-50">These images are stored in <code><?php echo $path;?></code>.</span></p>
<?php $this->endSection(); 
