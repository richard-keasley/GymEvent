<?php $this->extend('default');

$this->section('content'); 

$downloads = $event->files;
$logo_src = ''; 
$pattern = 'logo.';
foreach($downloads as $file) {
	if(strpos($file->getPathname(), $pattern)) {
		$logo_src = substr($file->getPathname(), strlen(FCPATH));
	}
}
$downloads->removePattern($pattern);
?>
<section class="clearfix">
<p><?php $date = new DateTime($event->date); echo $date->format('j F Y');?></p>

<?php if($logo_src) { ?>
	<div class="item-image">
	<img src="<?php echo base_url($logo_src);?>">
	</div>
<?php } ?>

<div><?php echo $event->description;?></div>

<?php if($event->clubrets<2 && $event->payment) { ?>
<section>
<h4>Payment</h4>
<div><?php echo $event->payment;?></div>
</section>
<?php } ?>

<?php if($event->clubrets==1) { ?>
<section class="p-1 alert-success rounded">
<p><strong>We are accepting entries for this event</strong></p>
<p>You are advised to open an entry <em>as soon as possible</em> if you intend to enter this event. You can continue to make edits until entries are closed. We will use the details within your return at that point (there is no "submit" button).</p>
</section>
<?php } ?>

<?php if($event->clubrets==2) { ?>
<section class="p-1 alert-warning rounded">
<p><strong>Entries for this event are now closed</strong></p>
<p>If you find an error in the entries, <em>inform the event organisers as soon as possible</em>. There is no guarantee entries can be corrected if you wait too long.</p>
</section>
<?php } ?>

<?php if($event->music==1) { ?>
<section class="p-1 alert-success rounded">
<p><strong>You can now upload your music</strong></p>
<p>Please upload your music as soon as you can, give us time to check your music can be played! You can alter tracks as often as you like until the music service is closed.</p> 
</section>
<?php } ?>

</section>

<?php if($downloads) { ?>
<section><h4>Downloads</h4>
<ul class="list-group"><?php 
$pattern = '<li class="list-group-item">%s</li>';
foreach($downloads as $file) {
	printf($pattern, $event->file_link($file));
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
