<h5>Viewing <?php echo $option;?> events</h5>
<?php 
$format = '<a href="%s" class="btn btn-outline-primary">%s</a>';

if($events) { ?>
<p>Select the event you are interested in.</p>
<?php } else { ?>
<p class="alert-warning">There are no events to view. Please select to view from 
<mark><?php echo implode(', ', $options);?></mark> 
events.
</p>
<?php } ?>
<div class="toolbar">
<?php
foreach($options as $opt) {
	printf($format, base_url("/events?f={$opt}"), $opt);
}
?>
</div>
	