<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex,nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#600">
<?php
$icons = [192, 48, 32, 16];
foreach($icons as $icon) {
	$size = "{$icon}x{$icon}";
	$link_tag = [
		'rel' => "icon",
		'sizes' => $size, // optional and stripped out by CI
		'href' => "app/icons/favicon-{$size}.png",
		'type' => "image/png",
	];
	echo link_tag($link_tag); 
}

$link_tags = [
[
	'rel' => "shortcut icon",
	'type' => "image/ico",
	'href' => "app/icons/favicon.ico"
],

'app/gymevent.css'

];
foreach($link_tags as $link_tag) echo link_tag($link_tag); 
?>
<style><?php
$viewpath = __DIR__;
$minifier = new MatthiasMullie\Minify\CSS("{$viewpath}/custom.css");
$minifier->add("{$viewpath}/kiosk.css");
echo $minifier->minify();
?></style>
<title><?php echo $title;?></title>
</head>
<body id="kiosk">
<div id="container">
<div id="flex"><?php 
echo $this->renderSection('content');
?></div>

<footer><?php 
echo base_url($link);
?></footer>

<img id="logo" src="/app/profile/logo.png">

</div>
</body>
</html>
