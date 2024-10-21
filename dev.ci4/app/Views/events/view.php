<?php $this->extend('default');

$this->section('content'); 

$files = $event->files;
// extract logo if it's there
$logo_src = ''; 
foreach($files as $file) {
	$filename = $file->getPathname();
	if(strpos($filename, 'logo.')) {
		$logo_src = substr($filename, strlen(FCPATH));
		$files->removeFile($filename);
	}
	if($logo_src) break;
}

?>
<section class="clearfix">
<p><?php $date = new \DateTime($event->date); echo $date->format('j F Y');?></p>
<?php if($logo_src) { ?>
	<div class="item-image">
	<img src="<?php echo base_url($logo_src);?>">
	</div>
<?php } ?>

<div><?php echo $event->description;?></div>
</section>

<?php if($event->clubrets==0 && $event->dates['clubrets_opens']) { ?>
<p class="alert alert-info">We will start accepting entries <?php 
$dt_opens = new \datetime($event->dates['clubrets_opens']);
$dt_now = new \datetime();
echo $dt_opens < $dt_now ? 'soon' : 'on ' . $dt_opens->format('j F');
?>.</p>
<?php } ?>

<?php if($event->clubrets==1) { ?>
<p class="alert alert-success"><strong>We are accepting entries for this event.</strong><br>
You are advised to open an entry <em>as soon as possible</em> if you intend to enter this event. You can continue to make edits until entries are closed 
<?php 
if($event->dates['clubrets_closes']) {
	$date = new \datetime($event->dates['clubrets_closes']);
	printf('<strong>(8:00pm on %s)</strong>', $date->format('j F'));
} ?>.
We will use the details within your return at that point (there is no "submit" button).</p>
<?php } ?>

<?php if($event->clubrets==1 && $event->payment) { ?>
<section>
<h4>Payment</h4>
<div><?php echo $event->payment;?></div>
</section>
<?php } ?>

<?php if($event->clubrets==2) { ?>
<p class="alert alert-warning"><strong>Entries for this event are now closed.</strong><br>
If you find an error in the entries, <em>inform the event organisers as soon as possible</em>. There is no guarantee entries can be corrected if you wait too long.</p>
<?php } ?>

<?php if($event->music==0 && $event->dates['music_opens']) { ?>
<p class="alert alert-info">We will start accepting music <?php 
$dt_opens = new \datetime($event->dates['music_opens']);
$dt_now = new \datetime();
echo $dt_opens < $dt_now ? 'soon' : 'on ' . $dt_opens->format('j F');
?>.</p>
<?php } ?>

<?php if($event->music==1) { ?>
<p class="alert alert-success"><strong>You can now upload your music.</strong><br>
Please upload your music as soon as you can, give us time to check your music can be played! You can alter tracks as often as you like until the music service is closed
<?php 
if($event->dates['music_closes']) {
	$date = new \datetime($event->dates['music_closes']);
	printf('<strong>(8:00pm on %s)</strong>', $date->format('j F'));
} ?>. 
</p>
<?php } ?>

<?php if($event->music==2) { ?>
<p class="alert alert-warning"><strong>Music upload for this event is now complete.</strong><br>
If you need to change your music, place the track on a USB stick and bring it to us <em>as soon as you possibly can</em>.</p>
<?php } ?>

<?php if(count($files)) { ?>
<section><h4>Downloads</h4>
<?php
$downloads = new \App\Views\Htm\Downloads($files);
echo $downloads->htm();
?>
</section>
<?php }

$this->endSection(); 

$this->section('bottom'); ?>
<div class="toolbar nav"><?php
echo \App\Libraries\View::back_link($back_link);
echo $event->link('admin');
echo $event->link('clubrets');
echo $event->link('music');
echo $event->link('player');
echo $event->link('teamtime');
?></div>
<?php  $this->endSection(); 
