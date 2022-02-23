<?php $this->extend('default');

$this->section('content'); ?>
<p>These are the current club returns for <?php echo $user->name;?>. 
You may edit any of these returns before the event's closing date.</p>
<?php $this->endSection(); 

$this->section('sidebar'); ?>
<nav class="nav flex-column"><?php 
foreach($clubrets as $clubret) {
	$event = $clubret->event();
	if($event) {
		echo getlink($clubret->url('view'), $event->title);
	}
} ?></nav>

<?php $this->endSection(); 
