<?php 
use \App\Libraries\Teamtime as tt_lib;
$images = tt_lib::get_images();

$index = '';
foreach($images as $image) {
	$basename = basename($image);
	$basename = substr($basename, 0, strpos($basename, '.'));
	if($basename=='index') $index = $image;	
}
if($index) { ?> 
	<img src="<?php echo $index;?>">
<?php } else { ?>
	<div style="font-size: 15vw">please wait&hellip;</div>
<?php } 
