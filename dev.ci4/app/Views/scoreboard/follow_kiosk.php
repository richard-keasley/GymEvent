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
<title>Follow scores</title>

<style>
#kiosk {
	width: 100vw;
	height: 56.22vw; /* force aspect ratio */
	position: relative;
}
#kiosk #container {
	position: absolute;
	left: 4vw;
	right: 4vw;
	bottom: 4vw;
	top: 4vw;
}
#kiosk #flex {
	display:flex; 
	gap:8vw;
}
#kiosk #flex p {
	font-size: 5vw;
	line-height: 1.2;
}
#kiosk footer {
	margin: 4vw 0 0 0;
	font-size: 4.5vw;
	font-weight: bold;
	color: #600;
}

#kiosk #logo {
	position:absolute;
	right:0; 
	bottom:0;
	width:10vw;
}
</style>

</head>
<?php 

helper('html');

$link = 'x/follow';

$img = [
    'src'   => '/app/scoreboard/qr-follow.png',
    'alt'   => 'Follow scores',
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
<p>Follow scores during today's event!</p>
<p>You need the entry number to see the scores.</p>
</div>

</div>

<footer><?php 
echo base_url($link);
?></footer>

<img id="logo" src="/app/profile/logo.png">

</div>
</body>
</html>
