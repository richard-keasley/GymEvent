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
<title>Play track</title>

</head>

<body id="kiosk">
<div id="playtrack" class="w-100 d-flex flex-column">
<?php
$track = new \App\Libraries\Track();
$track->event_id = $event_id;
$track->entry_num = $entry_num;
$track->exe = $exe;
$src = $track->url();

$attrs = [
	'controls' => "controls",
	'preload' => "auto",
	'class' => "w-100"
];
if($src) $attrs['src'] = site_url($src);
if($autoplay) $attrs['autoplay'] = "autoplay";
$format = '<audio %s></audio>';
printf($format, stringify_attributes($attrs));

$message = [
	intval($entry_num),
	$exe
];
if($src) {
	$alert = 'success';
	$url = parse_url($src,  PHP_URL_PATH);
	$message[] = '(' . pathinfo($url,  PATHINFO_EXTENSION) . ')';
	$prefix = '';
}
else {
	if($event_id) {
		$alert = 'danger';	
		$prefix = 'No music found ';
	}
	else {
		$alert = 'light';
		$prefix = 'ready&hellip;';
		$message = [];
	}
}
$format = '<p class="p-1 my-0 alert alert-%s">%s%s</p>';
printf($format, $alert, $prefix, implode(' ', $message));

?>
</div>

</body>
</html>

