<?php namespace App\Views\Htm;

class Editor {
const version = '6.3.2'; 

public $attr = [
	'name' => 'html',
	'value' => '',
	'class' => 'form-control'
];
public $label = true;
public $src = '';

public function __construct($attr=[]) {
	foreach($attr as $key=>$val) $this->attr[$key] = $val;
	$this->src = sprintf('/app/tinymce_%s/tinymce/js/tinymce/tinymce.min.js', self::version);
} 

public function htm() {
	$id = "{$this->attr['name']}-editor";
	printf('<div id="%s">', $id);
	if($this->label) printf('<label>%s</label>', humanize($this->attr['name']));
	echo form_textarea($this->attr);
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
	<script src="<?php echo $this->src;?>"></script>
	<?php
	echo '</div>';
}

}
