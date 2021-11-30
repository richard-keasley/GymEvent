<?php 
$index = '';
$images = \App\Libraries\Teamtime::get_images();
foreach($images as $image) {
	$basename = basename($image);
	$basename = substr($basename, 0, strpos($basename, '.'));
	if($basename=='index') $index = $image;	
}
if($index) { ?> 
	<img src="<?php echo $index;?>">
<?php } else { ?>
	<div style="font-size: 20vw">please wait&hellip;</div>
<?php } 
