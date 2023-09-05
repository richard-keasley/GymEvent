<?php namespace App\Views\Htm;

class Editor implements \stringable {
const version = '6.7.0';
static $script_done = false;

private $attrs = [
	'name' => 'html',
	'value' => '',
	'class' => 'form-control'
];

public $label = true;

public function __construct($attrs=[]) {
	foreach($attrs as $key=>$val) $this->attrs[$key] = $val;
}

public function __get($key) {
	return $this->attrs[$key] ?? null;
}

public function __toString() {
	ob_start();
	$id = "{$this->attrs['name']}-editor";
	printf('<div id="%s">', $id);
	if($this->label) printf('<label>%s</label>', humanize($this->name));
	echo form_textarea($this->attrs);
	?>
	<script>
	$(function(){
	tinymce.init({
		relative_urls: false,
		remove_script_host: false,
		document_base_url: '<?php echo base_url();?>',
		selector: '#<?php echo $id;?> textarea',
		promotion: false,
		branding: false,
		menubar: false,
		browser_spellcheck: true,
		plugins: 'link code lists help',
		toolbar: 'Undo Redo | Blocks | Bold Italic bullist | link code | help',
		content_css: '<?php echo site_url('/app/gymevent.css');?>'
	});
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
