<?php
try {
	// Autoload with no CI framework
	include __DIR__ . '/../paths.php';
	$paths = new \Config\Paths;
	$appdir = realpath($paths->appDirectory);
	$pattern = "{$appdir}/Libraries/Sse/*.php";
	foreach(glob($pattern) as $include) include $include;
	
	$channel = filter_input(INPUT_GET, 'ch');
	$delay = floatval(filter_input(INPUT_GET, 'd')); // seconds
	$ttl = intval(filter_input(INPUT_GET, 't')); // seconds

	$stream = new \App\Libraries\Sse\Stream($channel);
	$stream->send($delay, $ttl);
}
catch(\throwable $ex) {
	die ($ex->getMessage());
}
