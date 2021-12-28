<?php namespace App\Views\Htm;

class Vartable {
public $items = [];
public $footer = null;
public $template = [
	'htm_start' => '<table class="table table-sm table-borderless">',
	'items_start' => '<tbody>', 
	'item' => '<tr><th class="py-1 text-end">%s</th><td>%s</td></tr>',
	'items_end' => '</tbody>', 
	'footer_start' => '<tfoot>',
	'footer_end' => '</tfoot>',
	'htm_end' => '</table>'
];

public function __construct($items=[]) {
	$this->items = $items;
}

public function htm($items = false) {
	if($items) $this->items = $items;
	$retval = $this->template['htm_start'];
	
	$retval .= $this->template['items_start'];
	foreach($this->items as $key=>$item) {		
		$retval .= sprintf($this->template['item'], $key, self::item_td($item));
	}
	$retval .= $this->template['items_end'];
	
	if($this->footer) {
		$retval .= $this->template['footer_start'];
		$key = $this->footer[2] ?? 'Total'; 
		$retval .= sprintf($this->template['item'], $key, self::item_td($this->footer));
		$retval .= $this->template['footer_end'];
	}
	
	$retval .= $this->template['htm_end'];
	return $retval;
}

static function item_td($item) {
	if(is_array($item)) {
		$value = $item[0];
		$type = $item[1];
	}
	else {
		$value = $item;
		$type = '';
	}
	switch($type) {
		case 'time' : 
			return $value ? date('d M Y H:i', strtotime($value)) : '' ;
			break;
		case 'date' : 
			return $value ? date('d M Y', strtotime($value)) : '' ;
			break;
		case 'bool':
			return $value ? 'yes' : 'no' ;
			break;
		case 'money':
			return sprintf('<div class="text-end">&pound; %.2f</div>', $value);
			break;
	}
	return $value;
}

}
