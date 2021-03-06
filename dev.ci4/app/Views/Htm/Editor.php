<?php namespace App\Views\Htm;

class Editor {
public $attr = [
	'name' => 'html',
	'value' => '',
	'class' => 'form-control'
];
public $label = true;

public function __construct($attr=[]) {
	foreach($attr as $key=>$val) $this->attr[$key] = $val;
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
		relative_urls : false,
		selector: '#<?php echo $id;?> textarea',
		branding: false,
		menubar: false,
		browser_spellcheck: true,
		plugins: 'code',
		toolbar: 'Undo Redo | Blocks | Bold Italic | code',
		content_css: '/app/bootstrap.css'
	});
	});
	</script>
	<script src="/app/tinymce_6.0.0/tinymce/js/tinymce/tinymce.min.js"></script>
	<?php
	echo '</div>';
}

}
