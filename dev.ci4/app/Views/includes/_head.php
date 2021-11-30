<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="/public/ui/bootstrap.css">
<link rel="stylesheet" type="text/css" href="/public/ui/bootstrap-icons-1.5.0/bootstrap-icons.css">
<link rel="stylesheet" type="text/css" href="/public/ui/custom.css">
<script src="/public/ui/jquery/jquery-3.6.0.min.js" type="text/javascript"></script>
<title><?php echo $title;?></title>
<?php if(!empty($head)) echo $head;?>
</head>

<body class="container-fluid">
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
