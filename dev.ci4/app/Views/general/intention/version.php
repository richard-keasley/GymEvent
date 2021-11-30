<ul class="list-unstyled"><?php 
$format = '<li><strong>%s:</strong> %s</li>'; 
foreach(\App\Libraries\General\Intention::versions() as $label=>$date) {
	$time = strtotime($date);
	printf($format, $label, date('j M y', $time));
} 
?></ul>