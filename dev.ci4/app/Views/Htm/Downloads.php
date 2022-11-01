<?php namespace App\Views\Htm;

class Downloads {

static $trimstart = 0; // used for item href
	
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
	if(!self::$trimstart) self::$trimstart = strlen(FCPATH);
}

public function htm() {
	if(!count($this->files)) return '';
	$retval = $this->template['items_start'];
	foreach($this->files as $key=>$file) {
		$retval .= $this->template['item_start'];
		$retval .= $this->item($key, $file);
		$retval .= $this->template['item_end'];
	}
	$retval .= $this->template['items_end'];
	return $retval;
}

public function item($key, $file) {
	$ext = $file->getExtension();
	
	$label = humanize(urldecode($file->getBasename(".{$ext}")));
	switch(strtolower($ext)) {
		case 'pdf': 
			$icon = '-pdf'; break;
		case 'docx':
			$icon = '-richtext'; break;
		case 'xlsx': 
		case 'csv':
			$icon = '-spreadsheet'; break;
		case 'png':
		case 'jpg':
		case 'svg':
			$icon = '-image'; break;
		case 'sql':
		case 'xml':
		case 'html':
			$icon = '-code'; break;
		case 'mp3':
		case 'wma':
		case 'wav':
		case 'm4a':
			$icon = '-music'; break;
		default:
			$icon = '';
	}
	$label = sprintf('<span class="bi bi-file%s pe-2"></span>%s', $icon, $label);
	
	$href = base_url(substr($file->getPathname(), self::$trimstart));
	return 
		sprintf($this->template['item_before'], $key) . 
		anchor($href, $label) .
		sprintf($this->template['item_after'], $key) ; 
}
	
}
