<?php
try {
	// Autoload with no CI framework
	include __DIR__ . '/../paths.php';
	$paths = new \Config\Paths;
	$appdir = realpath($paths->appDirectory);
	$pattern = "{$appdir}/Libraries/Sse/*.php";
	foreach(glob($pattern) as $include) include $include;
	
	$channel = filter_input(INPUT_GET, 'ch');
	$tick = intval(filter_input(INPUT_GET, 'tk')); // milliseconds
	$ttl = intval(filter_input(INPUT_GET, 'tl')); // seconds

	$stream = new \App\Libraries\Sse\Stream($channel);
	$stream->send($tick, $ttl);
}
catch(\throwable $ex) {
	die ($ex->getMessage());
}
