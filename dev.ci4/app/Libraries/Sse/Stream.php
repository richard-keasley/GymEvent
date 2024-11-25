<?php
namespace App\Libraries\Sse;

class Stream {
const min_delay = 1000;
private $attrs = [];

function __construct($channel) {
	// CI not required
	$pattern = __DIR__ . '/channel/%s.php';
	$include = sprintf($pattern, $channel);
	if(!is_file($include)) {
		$channel = 'none';
		$include = sprintf($pattern, $channel);
	}
	include_once $include;
	$class = "\\App\\Libraries\\Sse\\channel\\{$channel}";
	$this->attrs = [
		'channel' => new $class
	];
}

function __get($key) {
	return $this->attrs[$key] ?? null ;
}

function send($delay=0, $ttl=0) {
try {
	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache');
	header('Access-Control-Allow-Origin: *');
	header("X-Accel-Buffering: no");
	header('Connection: Keep-Alive');
	while(ob_get_level()) {
		ob_end_flush();
	}
	flush();
	
	$delay = max($delay, 1); // 1 second minimum
	if(!$ttl) $ttl = 1800; // seconds 1800=30 minutes
	$poke = 600; // keep alive signal (seconds)
	
	$event = ['data' => [
		"Channel:{$this->channel->name}",
		"TTL:{$ttl}",
		"delay:{$delay}",
		"poke:{$poke}",
	]];
	echo (string) new \App\Libraries\Sse\Event($event);	
	
	$usleep = $delay * 1000000; // seconds -> microseconds
	$retry = null;
	$last_id = 0;
	$end = time() + $ttl; // stop here
	$poke_next = time() + $poke; // next poke
	
	while(1) {
		if(connection_aborted()) break;
		
		$event = $this->channel->read();
		if($event && ($event->id !== $last_id)) {
			echo (string) $event;
			$last_id = $event->id;
			$retry = $event->retry ?? null;
		}
		if($retry) break;
		
		$now = time();
		if($now > $end) {
			$event = [
				'id' => 0,
				'retry' => $delay * 1000, // seconds -> milliseconds
				'data' => "retry in {$delay}s"
			];
			echo (string) new \App\Libraries\Sse\Event($event);
			break;
		}
		if($now > $poke_next) {
			$event = [
				'comment' => "poke",
				// 'data' => "poke" // no data => silent for receiver
			];
			echo (string) new \App\Libraries\Sse\Event($event);
			$poke_next += $poke;
		}
		
		flush();
		usleep($usleep);
	}

} // try finished

catch(\throwable $ex) {
	$msg = $ex->getMessage() ?? null;
	$event = [
		'id' => 0,
		'event' => "alert",
		'data' => $msg ? $msg : 'error'
	];
	echo (string) new \App\Libraries\Sse\Event($event);
}

flush();
die;
}

static function database() {
	// database with no CI framework
	$paths = new \Config\Paths(); // loaded in /apx/sse.php
	$appdir = realpath($paths->appDirectory);
	$include = dirname($appdir) . '/.env';
	if(!is_file($include)) die("{$include} not found");
	
	$db_conn = [];
	$section = 'database.default';
	$strlen = strlen($section) + 1;
	foreach(file($include) as $line) {
		if(strpos($line, $section)===0) {
			$line = substr($line, $strlen);
			$arr = explode('=', $line, 2);
			$key = trim($arr[0] ?? '');
			$val = trim($arr[1] ?? '');
			$db_conn[$key] = $val;
		}
	}
	# var_dump($db_conn);
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	return new \mysqli(
		$db_conn['hostname'] ?? '#', 
		$db_conn['username'] ?? '#', 
		$db_conn['password'] ?? '#', 
		$db_conn['database'] ?? '#'
	);
}

}
