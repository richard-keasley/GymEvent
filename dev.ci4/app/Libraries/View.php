<?php namespace App\Libraries;

class View {

static function back_link($href) {
	$label = '<span class="bi bi-box-arrow-left"></span>';
	$attrs = ['class'=>"btn btn-outline-secondary", 'title'=>"close"];
	return anchor($href, $label, $attrs);
} 

} 
