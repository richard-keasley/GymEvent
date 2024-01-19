<?php namespace App\Views\Htm;

class Popover implements \Stringable {
	
private $attributes = [];
public $label = '<span class="bi bi-info-lg"></span>';
static $count = 0;

public function __construct($content, $title=null, $label=null, $attributes=[]) {
	if($label) $this->label = $label;
	
	$this->attributes = [
		'tabindex' => "0",
		'role' => "button",
		'class' => "popover-dismiss btn btn-sm btn-info",
		'title' => $title ?? 'help',
		'data-bs-toggle' => "popover",
		'data-bs-trigger' => "focus",
		'data-bs-content' => $content
	];
	foreach($attributes as $key=>$val) {
		$this->attributes["data-bs-{$key}"] = $val;
	}
}

public function __get($key) {
	return $this->attributes[$key] ?? '' ;
}
public function __toString() {

ob_start();

printf('<span %s>%s</span>', stringify_attributes($this->attributes), $this->label);

if(!self::$count) {
echo "<script>
$(function() {
const popoverTriggers = document.querySelectorAll('[data-bs-toggle=popover]');
const popovers = [...popoverTriggers].map(popoverEl => new bootstrap.Popover(popoverEl));
});
</script>";
}
self::$count++;

return ob_get_clean();	
}

}
