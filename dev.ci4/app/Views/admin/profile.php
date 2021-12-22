<?php $this->extend('default');

$this->section('content'); ?>
<ul class="nav">
<li><img src="/favicon.ico" title="favicon"></li>
<?php 
$path =  '/public/profile';
foreach(glob(FCPATH . $path .'/*') as $file) {
	$basename = basename($file);
	if(strpos($basename, 'index.')!==0) {
		printf('<li><img src="%1$s/%2$s" title="%2$s"></li>', $path, basename($file));
	}
} 
?></ul>
<p><span class="p-1 bg-light">These images are stored in <?php echo $path;?>.</span></p>
<?php $this->endSection(); 
