<?php namespace App\Views\Htm;

class Navbar {
public $id = '';
public $items = [];
public $template = [
	'items_start' => '<ul class="nav flex-column">',
	'item_start' => '<li class="nav-item">',
	'a_attr' => ['class'=>'nav-link'],
	'item_end' => '</li>',
	'items_end' => '</ul>'
];

public function __construct($items=[]) {
	$this->items = $items;
}

public function htm($items = false) {
	if($items) $this->items = $items;
	$retval = $this->template['items_start'];
	foreach($this->items as $item) {
		if(is_array($item)) {
			$href = trim($item[0], '/');
			$label = $item[1];
		}
		else {
			$href = trim($item, '/');
			$label = ucfirst(basename($href));
		}
		if($href=='home') $href = '/';
		if($href && \App\Libraries\Auth::check_path($href)) {
			$retval .= $this->template['item_start'] . 
				anchor(base_url($href), $label, $this->template['a_attr']) . 
				$this->template['item_end'] ;
		}
	}
	$retval .= $this->template['items_end'];
	return $retval;
}

}

