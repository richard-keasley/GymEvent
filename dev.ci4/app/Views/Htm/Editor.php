<?php namespace App\Views\Htm;

class Editor implements \stringable {
const version = '7.9.1'; # '7.8.0' 
static $script_done = false;

private $attrs = [
	'name' => 'html',
	'value' => '',
	'label' => true,
];

private $params = [
	'license_key' => 'gpl',
	'relative_urls' => false,
	'remove_script_host' => false,
	'promotion' => false,
	'branding' => false,
	'menubar' => false,
	'browser_spellcheck' => true,
	'plugins' => 'link code lists help fullscreen',
	'toolbar' => 'Undo Redo | Blocks | Bold Italic bullist | link code fullscreen | help',
];

public function __construct($attrs=[], $params=[]) {
	foreach($attrs as $key=>$val) $this->attrs[$key] = $val;
	
	$this->params['document_base_url'] = base_url();
	$this->params['content_css'] = site_url('/app/gymevent.css?v=1');
	foreach($params as $key=>$val) $this->params[$key] = $val;
}

public function __get($key) {
	return $this->attrs[$key] ?? null;
}

public function __toString() {
ob_start();
$id = "{$this->attrs['name']}-editor";
$this->params['selector'] = "#{$id} textarea";
printf('<div id="%s">', $id);
if($this->label) printf('<label>%s</label>', humanize($this->name));
echo form_textarea($this->attrs);
?>
<script>
$(function() {
tinymce.init(<?php echo json_encode($this->params);?>);
});
</script>
<?php
echo '</div>';

if(!self::$script_done) {
	$attrs = [
		'src' => sprintf('/app/tinymce_%s/tinymce/js/tinymce/tinymce.min.js', self::version)
	];
	printf('<script %s></script>', stringify_attributes($attrs));
}
self::$script_done = true;

return ob_get_clean();
}

}
