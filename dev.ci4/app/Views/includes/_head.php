<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#600">
<link rel="apple-touch-icon" sizes="180x180" href="/app/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/app/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/app/icons/favicon-16x16.png">
<link rel="stylesheet" type="text/css" href="/app/gymevent.css">
<link rel="stylesheet" type="text/css" href="/app/custom.css">
<?php echo \App\ThirdParty\jquery::script(); ?>
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
