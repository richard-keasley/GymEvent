<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex,nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#600">
<?php
helper('html'); 
$links = [
[
	'rel' => "shortcut icon",
	'type' => "image/ico",
	'href' => "app/icons/favicon.ico"
],
[
	'rel' => "apple-touch-icon",
	'type' => "image/png",
	'href' => "app/icons/apple-touch-icon.png"
],
[
	'rel' => "icon",
	'type' => "image/png",
	'href' => "app/icons/favicon-32x32.png"
],
[
	'rel' => "icon",
	'type' => "image/png",
	'href' => "app/icons/favicon-16x16.png"
],
	'app/gymevent.css'
];
foreach($links as $link) echo link_tag($link) . "\n"; 

echo \App\ThirdParty\jquery::script();

?> 
<style><?php
$minifier = new MatthiasMullie\Minify\CSS(config('Paths')->viewDirectory . '/custom.css');
echo $minifier->minify();
?></style> 
<title><?php echo $title;?></title>
<?php if(!empty($head)) echo $head;?>
</head>

<body class="container">

<header>
<h1><?php echo empty($heading) ? $title : $heading;?></h1>
<?php
$session = \Config\Services::session();
$flashdata = $session->getFlashdata('messages');
if($flashdata) {
	$messages = array_merge($messages, $flashdata);
	$session->setFlashdata('messages', null);
}
$flashdata = $session->getFlashdata('error');
if($flashdata) $messages[] = [$flashdata, 'danger'];

if(!empty($messages)) { ?>
<div class="messages"><?php 
foreach($messages as $row) {
	if(is_array($row)) {
		$text = $row[0]; $class = $row[1];
	}
	else {
		$text = $row; $class = 'danger';
	}
	printf('<div class="alert alert-%s alert-dismissible show fade" role="alert">%s<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>', $class, $text);
}
?></div>
<?php } ?>
</header>
