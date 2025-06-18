<?php namespace App\Views\js;

class timer implements \stringable {

static $done = 0;
static $defaults = [
	'duration' => 0,
	'reverse' => false,
	'started' => 0,
];

private $name = 'timer';
private $options = [];

public function __construct($name='timer', $options=[]) {
	$this->name = $name;
	foreach(array_keys(self::$defaults) as $key) {
		$this->options[$key] = $options[$key] ?? self::$defaults[$key];
	}
}

public function __toString() {

ob_start(); ?>
<script>
<?php 
$format = '%s = new timer(%s);';
printf($format, $this->name, json_encode($this->options));

if(!self::$done) {
self::$done = true;
?>
function timer(options) {
<?php
$format = 'this.%1$s = options.%1$s ?? %2$s; ' . "\n";
foreach(self::$defaults as $key=>$default) {
	printf($format, $key, json_encode($default));
}
?>
this.timer = null;
this.duration = this.duration * 1000;

this.reset = function() {
	this.started = 0;
	this.timer = null;
};

this.start = function(started=0, timer=null) {
	this.started = started ? started : Date.now();
	this.timer = timer;
};

this.elapsed = function() {
	var retval = this.started ? 
		Date.now() - this.started : 
		0 ;
	
	if(this.duration) {
		retval = Math.min(retval, this.duration);
		if(this.reverse) retval = this.duration - retval;
	}
	return retval;
},

this.format = function(format='minsec') {
	switch(format) {
		case 'raw':
		return Math.round(this.elapsed());
		
		case 'sec':
		return Math.round(this.elapsed() / 1000);
		
		case 'pc':
		if(!this.duration) return 0;
		return Math.floor(100 * this.elapsed() / this.duration);
		
		case 'minsec':
		default:
		var secs = Math.round(this.elapsed() / 1000);
		var mins = Math.floor(secs / 60); 
		secs = secs % 60;
		secs = secs < 10 ? `0${secs}` : `${secs}`;
		mins = mins < 10 ? `0${mins}` : `${mins}`;
		return(`${mins}:${secs}`);
	}
};

}
<?php } ?>
</script>
<?php return ob_get_clean();
}
	
}