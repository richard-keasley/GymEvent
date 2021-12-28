<?php namespace App\Libraries;

class View {

static function back_link($href) {
	$label = '<span class="bi bi-box-arrow-left"></span>';
	$attr = ['class'=>"btn btn-outline-secondary", 'title'=>"close"];
	return anchor(base_url($href), $label, $attr);
} 

static function download($filename) {
	switch(pathinfo($filename, PATHINFO_EXTENSION)) {
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
		default:
			$icon = '';
	}
	$label = humanize(urldecode(pathinfo($filename, PATHINFO_FILENAME)));
	$label = sprintf('<span class="bi bi-file%s pe-2"></span>%s', $icon, $label);
	return anchor(base_url($filename), $label);
}	

} 
