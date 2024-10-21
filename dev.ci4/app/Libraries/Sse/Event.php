<?php
namespace App\Libraries\Sse;

class Event implements \stringable {

private $attributes = [];
	
function __construct($attrs) {
	$this->attributes = $attrs;
}

function __toString() {
	$arr = [];	
	foreach($this->attributes as $key=>$attr) {
		if($key=='comment') $key = '';
		if(!is_array($attr)) $attr = [$attr];
		foreach($attr as $val) $arr[] = "{$key}:{$val}";
	}
	return implode("\n", $arr) . "\n\n";
}

function __get($key) {
	return $this->attributes[$key] ?? null ;
}

function __toArray() {
	return $this->attributes;
}

}