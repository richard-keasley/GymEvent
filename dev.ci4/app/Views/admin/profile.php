<?php $this->extend('default');

$this->section('content'); ?>
<ul class="nav"><?php 
$path =  '/public/profile';

foreach(glob(FCPATH . $path .'/*') as $file) {
	$basename = basename($file);
	if(strpos($basename, 'index.')!==0) {
		printf('<li><img src="%1$s/%2$s" title="%2$s"></li>', $path, basename($file));
	}
} 
?></ul>
<p>These images are stored in <?php echo $path;?>.</p>
<?php $this->endSection(); 
