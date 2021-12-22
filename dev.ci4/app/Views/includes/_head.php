<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#600">
<link rel="apple-touch-icon" sizes="180x180" href="/public/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/public/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/public/icons/favicon-16x16.png">
<link rel="stylesheet" type="text/css" href="/public/bootstrap.css">
<link rel="stylesheet" type="text/css" href="/public/custom.css">
<script src="/public/jquery-3.6.0.min.js" type="text/javascript"></script>
<title><?php echo $title;?></title>
<?php if(!empty($head)) echo $head;?>
</head>

<body class="container">
<header>
<h1><?php echo empty($heading) ? $title : $heading;?></h1>

<?php
$session = \Config\Services::session();
$flashdata = $session->getFlashdata('messages');
if($flashdata) $messages = $flashdata;
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
