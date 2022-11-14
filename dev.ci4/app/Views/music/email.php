<?php 
$eventdate = new \CodeIgniter\I18n\Time($event->date);
$closing = $eventdate->subDays(8);
$url = base_url("events/view/{$event->id}");
?>
<p>Dear club,</p>
<p>This is a reminder you must complete your music upload for the 
<em><?php echo $event->title;?></em>
before <strong><?php echo $closing->toLocalizedString('eeee d MMMM Y');?></strong> when the service will close.</p>
<p>Please upload your remaining music as soon as you can.</p>
<p><?php echo anchor($url);?></p>
<p>Please contact Richard if you are struggling to do this.</p>
<p>Richard</p>

