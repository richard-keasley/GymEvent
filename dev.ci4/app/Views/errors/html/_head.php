<?php
helper('form');

if(empty($code)) $code = 500;

$title = match($code) {
	401 => 'Unauthorised',
	403 => 'Forbidden',
	404 => 'Not Found',
	423 => 'Locked',
	500 => 'Server error',
	default => 'Error'
};
	
if(empty($message) || $message=='(null)') $message = "Sorry! We can't do that!";
if($code==401) {
	// make it less scary to login
	$heading = 'Please log-in';
	$message = [$message, 'primary'];
}
$messages = [$message];

include(config('Paths')->viewDirectory . '/includes/_head.php'); 
?>
<ul class="breadcrumb">
	<li class="breadcrumb-item active"><a href="<?php echo base_url();?>"><span class="bi-house-fill"> home</span></a></li>
</ul>
