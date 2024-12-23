<?php namespace App\Views\Htm;

class Pretty implements \Stringable {

private $data = '';
	
function __construct($data) {
	$this->data = $data;
}
	
function __toString() {
	$translate = [];
	
	$success = preg_match_all('#\d\/\d#', $this->data, $out);
	$matches = $out[0] ?? 0;
	foreach($matches as $match) {
		$translate[$match] = self::fraction($match);
	}
	
	# d($translate);	
	return strtr($this->data, $translate);
}
	
static function fraction($val) {
	$arr = explode('/', $val);
	if(count($arr)!=2) return $val;
	
	$format = '<span class="fraction"><span class="fran">%s</span>%s<span class="frad">%s</span></span>';
	return sprintf($format, $arr[0], '&frasl;', $arr[1]);
}

}