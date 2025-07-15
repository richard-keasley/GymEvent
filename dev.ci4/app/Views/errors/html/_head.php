<!DOCTYPE html>
<html lang="en">
<head><?php 
$viewpath = config('Paths')->viewDirectory;

$code = intval($code ?? 500);
$title = \App\Exceptions\Exception::get_reason($code);

include("{$viewpath}/includes/html-head.php"); 

// make it less scary to login
if($code==401) $heading = 'Please log-in';
$alert = ($code==401) ? 'primary' : 'danger' ;
	
if(empty($message) || $message=='(null)') $message = "Sorry! We can't do that!";
$messages = [[$message, $alert]];

$breadcrumbs = [['', '<span class="bi-house-fill"></span>']];

?></head>
<body class="container">
<?php 

include("{$viewpath}/includes/html-body-header.php"); 
