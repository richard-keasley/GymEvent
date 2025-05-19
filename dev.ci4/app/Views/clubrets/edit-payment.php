<?php

if($event->dates['clubrets_closes']) { 
	$dt_closes = new \datetime($event->dates['clubrets_closes']);
	$dt_now = new \datetime();
	$past = $dt_closes <= $dt_now;
	if($past) {
		$class = 'alert alert-warning';
		$datestring = 'as soon as possible';
	} else {
		$class = '';
		$datestring = 'by ' . $dt_closes->format('l j F');
	}
	$format = '<p class="%s">All entries must be completed <strong>%s</strong>.</p>';
	printf($format, $class, $datestring);
}

?>
<div><?php echo $event->payment;?></div>
<?php echo $clubret->fees('htm');?>
<p><strong>NB:</strong> Save any changes to update the fees calculation.</p>
