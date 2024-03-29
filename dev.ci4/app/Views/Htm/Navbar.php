<?php namespace App\Views\Htm;

class Navbar implements \Stringable {
public $items = [];
public $template = [
	# was class="nav flex-column"
	'items_start' => '<ul class="navbar-nav">',
	'item_start' => '<li class="nav-item">',
	'a_attr' => ['class'=>'nav-link'],
	'item_end' => '</li>',
	'items_end' => '</ul>'
];

public function __construct($items=[]) {
	$this->items = $items;
}

public function __toString() {
	return $this->htm();
}

public function htm($items = false) {
	if($items) $this->items = $items;
	$retval = $this->template['items_start'];
	foreach($this->items as $item) {
		if(is_array($item)) {
			$href = $item[0] ?? '' ;
			$label = $item[1] ?? '';
		}
		else {
			$href = $item;
			$label = '';
		}
		$href = trim($href, '/');
		if($href) {
			if(!$label) $label = ucfirst(basename($href));
			if($href=='home') $href = '/';
				
			if(!parse_url($href, PHP_URL_SCHEME)) {
				// check local links
				if(!\App\Libraries\Auth::check_path($href)) $href =  null;
			}
		}
		if($href) {
			$retval .= $this->template['item_start'] . 
				anchor($href, $label, $this->template['a_attr']) . 
				$this->template['item_end'] ;
		}
	}
	$retval .= $this->template['items_end'];
	return $retval;
}

}
