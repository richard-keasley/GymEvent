<?php namespace App\Views\Htm;

class Breadcrumbs {
public $items = [];
public $template = [
	'items_start' => '<nav><ul class="breadcrumb">',
	'item_start' => '<li class="breadcrumb-item">',
	'a_attr' => [],
	'item_end' => '</li>',
	'items_end' => '</ul></nav>'
];

public function __construct($items=[]) {
	$this->items = $items;
}

public function htm($items = false) {
	if($items) $this->items = $items;
	$last = array_key_last($this->items);
	$retval = $this->template['items_start'];

	foreach($this->items as $key=>$item) {
		if(is_array($item)) {
			$link = $item[0];
			$label = $item[1];
		}
		else {
			$link = $item;
			$label = basename($item);
			if(is_numeric($label)) $label = basename(dirname($item));
			$pos = strrpos($label, "_");
			if($pos) $label = substr($label, $pos + 1);
		}
		$retval .= $this->template['item_start'] . 
			anchor($link, $label, $this->template['a_attr']) . 
			$this->template['item_end'] ;
	}
	
	$retval .= $this->template['items_end'];
	return $retval;
}

}
