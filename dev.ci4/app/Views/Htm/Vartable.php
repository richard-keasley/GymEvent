<?php namespace App\Views\Htm;

class Vartable {
public $items = [];
public $footer = null;
public $template = [
	'htm_start' => '<div class="table-responsive"><table class="table table-sm table-borderless">',
	'items_start' => '<tbody>', 
	'item_start' => '<tr>',
	'item_varname' => '<th class="py-1 text-end">%s</th>',
	'item_value' => '<td>%s</td>',
	'item_end' => '</tr>',
	'items_end' => '</tbody>', 
	'footer_start' => '<tfoot>',
	'footer_end' => '</tfoot>',
	'htm_end' => '</table></div>'
];

public function __construct($items=[]) {
	$this->items = $items;
}

public function htm($items = false) {
	if($items) $this->items = $items;
	$retval = $this->template['htm_start'];
	
	$retval .= $this->template['items_start'];
	foreach($this->items as $key=>$item) {		
		$retval .= $this->htm_item($key, $item);
	}
	$retval .= $this->template['items_end'];
	
	if($this->footer) {
		if(is_array($this->footer)) {
			$key = $this->footer[1] ?? ''; 
			$item = $this->footer[0] ?? ''; 
		}
		else {
			$key = '';
			$item = $this->footer;
		}
		if($item) {
			$retval .= $this->template['footer_start'];
			$retval .= $this->htm_item($key, $item);
			$retval .= $this->template['footer_end'];
		}
	}
	
	$retval .= $this->template['htm_end'];
	return $retval;
}

private function htm_item($varname, $varvalue) {
	$retval = $this->template['item_start'];
	$retval .= sprintf($this->template['item_varname'], $varname);
		
	$pattern = $this->template['item_value'];
	if(is_array($varvalue)) {
		if(preg_match('/(<\w+)/i', $this->template['item_value'], $matches)) {
			$search = $matches[0];
		}
		else {
			$search = null;
		}
		if($search) {
			$attrs = [];
			foreach($varvalue as $key=>$val) {
				if($key !== 'data') $attrs[$key] = $val;
            }
			$replace = $search . ' ' . stringify_attributes($attrs);
			$pattern = str_replace($search, $replace, $this->template['item_value']);
		}
		$data = $varvalue['data'] ?? '' ;
	}
	else {
		$data = $varvalue;
	}
	$retval .= sprintf($pattern, $data);
	
	$retval .= $this->template['item_end'];
	return $retval;
}

}
