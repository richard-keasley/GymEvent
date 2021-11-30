<?php $this->extend('default');
$images = $tt_lib::get_images();
	
$this->section('content'); ?>
<div id="carouselExampleControls" class="carousel slide carousel-fade" data-bs-ride="carousel">
<div class="carousel-inner">
<?php foreach($images as $key=>$image) {
	$active = $key ? '' : 'active' ;
	printf('<div class="carousel-item %s"><img src="%s" class="d-block w-100"></div>', $active, $image);
} ?>
</div>
<button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"  data-bs-slide="prev">
	<span class="carousel-control-prev-icon" aria-hidden="true"></span>
</button>

<button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
	<span class="carousel-control-next-icon" aria-hidden="true"></span>
</button>
</div>
<p>Image path: <?php echo $tt_lib::get_var('settings', 'image_path');?></p>
<ol><?php foreach($images as $image) {
	printf('<li>%s</li>', basename($image));
} ?></ol>

<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top">
<?php echo \App\Libraries\View::back_link($back_link); ?>
</div>
<?php $this->endSection(); 
