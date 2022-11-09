<?php
use \App\Libraries\Teamtime as tt_lib;
$images = tt_lib::get_images();
?>
<div>
<div id="carousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
<div class="carousel-inner">
<?php foreach($images as $key=>$image) {
	$active = $key ? '' : 'active' ;
	printf('<div class="carousel-item %s"><img src="%s" class="d-block w-100"></div>', $active, $image);
} ?>
</div>
<button class="carousel-control-prev" type="button" data-bs-target="#carousel"  data-bs-slide="prev">
	<span class="carousel-control-prev-icon" aria-hidden="true"></span>
</button>

<button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next">
	<span class="carousel-control-next-icon" aria-hidden="true"></span>
</button>
</div>
<p>Image path: <?php echo tt_lib::get_var('settings', 'image_path');?></p>
<ol><?php foreach($images as $image) {
	printf('<li>%s</li>', basename($image));
} ?></ol>
</div>