<?php
helper('form');
helper('html');

$code = intval($code ?? 500);

$title = match($code) {
	401 => 'Unauthorised',
	403 => 'Forbidden',
	404 => 'Not Found',
	423 => 'Locked',
	500 => 'Server error',
	default => 'Error'
};
$alert = match($code) {
	401 => 'primary',
	default => 'danger'
};
	
// make it less scary to login
if($code==401) $heading = 'Please log-in';
	
if(empty($message) || $message=='(null)') $message = "Sorry! We can't do that!";
$messages = [[$message, $alert]];

$breadcrumbs = [['', '<span class="bi-house-fill"></span>']];

include(config('Paths')->viewDirectory . '/includes/_head.php'); 
