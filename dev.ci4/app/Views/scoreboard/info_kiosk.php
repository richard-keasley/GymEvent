<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex,nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#600">
<link rel="apple-touch-icon" sizes="180x180" href="/app/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/app/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/app/icons/favicon-16x16.png">
<link rel="stylesheet" type="text/css" href="/app/gymevent.css">
<style><?php
$minifier = new MatthiasMullie\Minify\CSS(config('Paths')->viewDirectory . '/custom.css');
$minifier->add(__DIR__ . '/kiosk.css');
echo $minifier->minify();
?></style>
<title><?php echo $title;?></title>
</head>
<?php 
helper('html');

$link = 'x/info';

$img = [
    'src'   => 'app/scoreboard/qr-info.png',
    'alt'   => 'Event information',
    'style' => "max-width:100%;",
];

$anchor = [
	'title' => base_url($link),
	'class' => "d-block"
];

?>
<body id="kiosk">
<div id="container">

<div id="flex">

<div style="width:34vw">
<?php echo img($img);?>
</div>

<div style="width:50vw;">
<p>View information on today's event!</p>
</div>

</div>

<footer><?php 
echo base_url($link);
?></footer>

<img id="logo" src="/app/profile/logo.png">

</div>
</body>
</html>
