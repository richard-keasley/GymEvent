<meta charset="UTF-8">
<meta name="robots" content="noindex,nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#600">
<title><?php echo empty($title) ? 'GymEvent' : $title ; ?></title>
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

];

$stylesheets = $stylesheets ?? ['gymevent.css?v=1'];
foreach($stylesheets as $stylesheet) {
	$link_tags[] = "app/{$stylesheet}";
}

foreach($link_tags as $link_tag) echo link_tag($link_tag);

$viewpath = config('Paths')->viewDirectory;
$minifier = new MatthiasMullie\Minify\CSS("{$viewpath}/custom.css");
if(ENVIRONMENT!='production') { 
	$minifier->add("{$viewpath}/debug.css");
}
$minifier->add($style ?? '');
$buffer = $minifier->minify();
if($buffer) echo "<style>{$buffer}</style>";

echo \App\ThirdParty\jquery::script();
