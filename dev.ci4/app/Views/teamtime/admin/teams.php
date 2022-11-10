<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;

$this->section('content'); 
$mdl_events = new \App\Models\Events;
$event = $mdl_events->find($event_id);
$title = $event->title ?? '' ;
echo "<h2>{$title}</h2>";

echo tt_lib::view_html('teams');
$this->endSection(); 

$this->section('top'); 
$attr = ['class' => "toolbar sticky-top"];
$hidden = ['reload' => 1];
echo form_open(current_url(), $attr, $hidden);
echo \App\Libraries\View::back_link('control/teamtime'); ?>
<button class="btn btn-primary bi bi-arrow-clockwise" title="reload team names" type="submit" name="refresh"></button>
<?php 
echo getlink("admin/entries/view/{$event_id}", 'entries');
echo form_close();
$this->endSection(); 

