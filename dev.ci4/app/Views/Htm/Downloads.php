<?php namespace App\Views\Htm;

class Downloads {

static $trimstart = null; // used for item href
	
public $template = [
	'items_start' => '<ul class="list-group">',
	'item_start'  => '<li class="list-group-item">',
	'item_before' => '',
	'item_after'  => '',
	'item_end'    => '</li>',
	'items_end'   => '</ul>'
];

public $files = [];

public function __construct($files=[]) {
	$this->files = $files;
	if(!self::$trimstart) {
		// initialise global settings
		self::$trimstart = strlen(FCPATH);
	}
}

public function htm() {
	if(!count($this->files)) return '';
	ob_start();
	echo $this->template['items_start'];
	foreach($this->files as $key=>$file) {
		echo $this->template['item_start'];
		echo $this->item($key, $file);
		echo $this->template['item_end'];
	}
	echo $this->template['items_end'];
	return ob_get_clean();
}

public function item($key, $file) {
	$ext = $file->getExtension();
	$mime = array_pad(explode('/', $file->getMimeType()), 2, '');
	$icon = match($mime[0]) {
		'audio' => 'music',
		'image' => 'image',
		'text' => match($mime[1]) {
			'csv' => "spreadsheet",
			'html' => "code",
			default => 'text'
		},
		'application' => match($ext) {
			'docx' => "richtext",
			'pdf' => "pdf",
			'xls' => "spreadsheet",
			'xlsx' => "spreadsheet",
			default => ''
		},
		default => ''
	};
	if($icon) $icon = "-{$icon}"; 

	$label = humanize(urldecode($file->getBasename(".{$ext}")));
	$label = sprintf('<span class="bi-file%s pe-2"></span>%s', $icon, $label);
	
	$href = base_url(substr($file->getPathname(), self::$trimstart));
	return 
		sprintf($this->template['item_before'], $key) . 
		anchor($href, $label, ['download'=>""]) .
		sprintf($this->template['item_after'], $key) ; 
}
	
}
