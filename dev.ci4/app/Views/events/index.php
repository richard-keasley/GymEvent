<?php $this->extend('default');
 
$this->section('content'); ?>
<div class="item-image"><img src="<?php echo base_url('/app/profile/logo.png');?>"></div>
<?php
if(!empty($body)) echo $this->include("events/index-{$body}");
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
	$icon = $event->deleted_at ?
		\App\Entities\Event::icons['hidden'] :
		match(intval($event->clubrets)) {
			0 => \App\Entities\Event::icons['future'],
			3 => \App\Entities\Event::icons['past'],
			default => \App\Entities\Event::icons['current']
		};
	
	$date = new DateTime($event->date);
	$nav[] = [
		sprintf('%s/%u', $base_url, $event->id),
		sprintf('%s %s: %s', $icon, $date->format('j-M-y'), $event->title)
	];
}
$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();
	
$this->endSection(); 
