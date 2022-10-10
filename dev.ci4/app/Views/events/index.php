<?php $this->extend('default');
 
$this->section('content'); ?>
<div class="item-image"><img src="<?php echo base_url('/app/profile/logo.png');?>"></div>
<?php
echo $this->include("events/{$body}");
$this->endSection(); 

$this->section('bottom'); ?>
<div class="toolbar"><?php 
echo \App\Libraries\View::back_link($back_link);
echo getlink('admin/events/add', 'create new event');
?></div>
<?php $this->endSection(); 

$this->section('sidebar');
$nav = [];
foreach($events as $event) { 
	$date = new DateTime($event->date);
	$deleted_at = $event->deleted_at ? '<span class="bi bi-x-circle text-danger" title="this event is not listed"></span>' : '' ;
	$nav[] = [
		sprintf('%s/%u', $base_url, $event->id),
		sprintf('%s: %s %s', $date->format('j-M-y'), $event->title, $deleted_at)
	];
}
$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();
	
$this->endSection(); 
