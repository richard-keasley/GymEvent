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
$admin = strpos(current_url(), '/admin/');

$nav = [];
foreach($events as $event) {
	if($event->deleted_at) {
		$icon = \App\Entities\Event::icons['hidden'];
	}
	elseif($event->private) {
		$icon = \App\Entities\Event::icons['private'];
	}
	else {
		$icon = match(intval($event->clubrets)) {
			0 => \App\Entities\Event::icons['future'],
			3 => \App\Entities\Event::icons['past'],
			default => \App\Entities\Event::icons['current']
		};
	}
	
	$label = $event->title;
	if($admin) {
		$music = realpath($event->filepath('music'));
		if($music) $label .= '<i class="ms-1 bi bi-music-note text-primary"></i>';
	}
	
	
	$date = (new DateTime($event->date))->format('j-M-y');
	$nav[] = [
		sprintf('%s/%u', $base_url, $event->id),
		sprintf('%s %s: %s', $icon, $date, $label)
	];
}
// navbar won't display private events unless permitted
echo new \App\Views\Htm\Navbar($nav);
	
$this->endSection(); 
