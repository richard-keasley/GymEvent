<?php namespace App\Libraries\Ui;

class Accordion {
private $id = '';
private $num = 0;

const formats = [
	'start' => '<div class="accordion" id="%1$s">',
	'item_start' => '<div class="accordion-item">',
	'header_start' => '<div class="accordion-header" id="%1$s-heading-%2$u"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#%1$s-body-%2$u" aria-expanded="false" aria-controls="%1$s-body-%2$u">',
	'header_end' => '</button></div>',
	'body_start' => '<div id="%1$s-body-%2$u" class="accordion-collapse collapse" aria-labelledby="%1$s-heading-%2$u" data-bs-parent="#%1$s"><div class="accordion-body table-responsive">',
	'body_end' => '</div></div>',
	'item_end' => '</div>',
	'end' => '</div>'
];

function __get($format) {
	if(isset(self::formats[$format])) $format = self::formats[$format];
	return sprintf($format, $this->id, $this->num);
}

function start($id='acc') {
	$this->id = $id;
	$this->num = 0;
	return $this->start;
}

function end() {
	return $this->item_end() . $this->end;
}

function item_start($label) {
	$buffer = $this->num ? $this->item_end() : '';
	$this->num++;
	return $buffer . $this->item_start . 
	$this->header_start . $label . $this->header_end . 
	$this->body_start;
}

private function item_end() {
	return $this->num ? $this->body_end . $this->item_end : '' ;
}

}
