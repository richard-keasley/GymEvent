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

$manifest = (ENVIRONMENT=='development') ? 'manifest-dev' : 'manifest';

$link_tags = [
[
	'rel' => "apple-touch-icon",
	'type' => "image/png",
	'href' => "app/icons/favicon-180x180.png"
],
[
	'rel' => "shortcut icon",
	'type' => "image/ico",
	'href' => "app/icons/favicon.ico"
],

[
	'rel' => "manifest",
	'href' => "app/{$manifest}.json",
	'type' => "application/manifest+json",
],

'app/gymevent.css'

];

foreach($link_tags as $link_tag) echo link_tag($link_tag) . "\n";

echo \App\ThirdParty\jquery::script();

?> 
<style><?php
$minifier = new MatthiasMullie\Minify\CSS(config('Paths')->viewDirectory . '/custom.css');
echo $minifier->minify();
?></style> 
<title><?php echo $title; ?></title>
<?php if(!empty($head)) echo $head; ?>
</head>

<body class="container">

<header>
<?php 
if($showhelp ?? false) {
	// look for help entry
	$html = (new \App\Models\Htmls)->find_path();
	if($html) {
		$this->setVar('html', $html);
		echo $this->include('html/popup');
	}
}
?>
<h1><?php echo empty($heading) ? $title : $heading;?></h1>
<?php
include(__DIR__ . '/breadcrumbs.php');
include(__DIR__ . '/messages.php');
?>
</header>
