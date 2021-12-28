<?php namespace App\Libraries;

class View {

static function vartable($data=[]) {
	$table = [];
	foreach($data as $key=>$row) {
		if(!is_array($row)) $row = [$row];
		$value = $row[0];
		$label = empty($row[1]) ? $key : $row[1];
		$type = empty($row[2]) ? '' : $row[2];
		$table_part = ($type && $type[0]=='*') ? 'tfoot' : 'tbody' ;
		if($table_part=='tfoot') $type = substr($type, 1);
		switch($type) {
			case 'time' : 
				$value = $value ? date('d M Y H:i', strtotime($value)) : '' ;
				break;
			case 'date' : 
				$value = date('d M Y', strtotime($value));
				break;
			case 'bool':
				$value = $value ? 'yes' : 'no' ;
				break;
			case 'money':
				$value = sprintf('<div class="text-end">&pound; %.2f</div>', $value);
				break;
		}
		$table[$table_part][] = [$label, $value];
	}
	$htm = '<table class="table compact table-borderless">';
	foreach(['tbody', 'tfoot'] as $table_part) {
		if(!empty($table[$table_part])) {
			$htm .= "<{$table_part}>";
			foreach($table[$table_part] as $tr) {
				$htm .= sprintf('<tr><th class="py-1 text-end">%s</th><td>%s</td></tr>', $tr[0], $tr[1]);
			}
			$htm .= "</{$table_part}>";
		}
	}
	$htm .= '</table>';
	return $htm;
}

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
