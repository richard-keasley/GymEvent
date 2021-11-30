<?php $this->extend('default');

$this->section('content'); ?>
<p>Current returns for <?php echo $user->name;?>.</p>
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
