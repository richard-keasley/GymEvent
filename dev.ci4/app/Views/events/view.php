<?php $this->extend('default');
$downloads = [];
$logo = '';
foreach($event->files as $file) {
	if(strpos($file, 'logo.')===0) {
		$logo = $file;
	}
	else {
		$downloads[] = $file;
	}
}

$this->section('content'); ?>
<section class="clearfix">
<p><?php $date = new DateTime($event->date); echo $date->format('j F Y');?></p>

<?php if($logo) { 
    printf('<img src="/public/events/%u/files/%s" style="float:right;width:15em;max-width:30%%;">', $event->id, $logo);
} ?>

<div><?php echo $event->description;?></div>
<?php if($event->clubrets<2 && $event->payment) {?>
<h4>Payment</h4>
<div><?php echo $event->payment;?></div>
<?php } ?>
</section>

<?php 
if($downloads) { ?>
<section><h4>Downloads</h4>
<ul class="list-group"><?php 
$pattern = '<li class="list-group-item">%s</li>';
foreach($downloads as $filename) {
	printf($pattern, $event->file_link($filename));
} ?></ul>
</section>
<?php }

$this->endSection(); 

$this->section('bottom'); ?>
<div class="toolbar nav"><?php
echo \App\Libraries\View::back_link($back_link);
echo $event->link('admin');
echo $event->link('clubrets');
echo $event->link('videos');
echo $event->link('music');
echo $event->link('player');
?></div>
<?php  $this->endSection(); 
