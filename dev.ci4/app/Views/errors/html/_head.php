<?php
helper('form');

if(empty($message) || $message=='(null)') $message = "Sorry! We can't do that!";

if(empty($code)) $code = 500;

if($code==401) {
	$title = 'Unauthorised';
	$heading = 'Please log-in';
	$message = [$message, 'primary'];
}
if($code==403) $title = 'Forbidden';
if($code==404) $title = 'Not Found';
if($code==423) $title = 'Locked';
if($code==500) $title = 'Server error';
if(empty($title)) $title = "Error";
$messages = [$message];

include(config('Paths')->viewDirectory . '/includes/_head.php'); 
?>
<ul class="breadcrumb">
	<li class="breadcrumb-item active"><a href="<?php echo base_url();?>"><span class="bi-house-fill"> home</span></a></li>
</ul>
