<p>Dear club,</p>
<p>This is a reminder you must complete your music upload for the 
<em><?php echo $event->title;?></em>
before <strong>8:00pm on 
<?php echo $event->dates['music_closes']->format('l j F');?>
</strong> when the service will close.</p>
<p>Please upload your remaining music as soon as you can.</p>
<p><?php echo anchor("events/view/{$event->id}");?></p>
<p>Please contact Richard if you are struggling to do this.</p>
<p>Richard</p>
