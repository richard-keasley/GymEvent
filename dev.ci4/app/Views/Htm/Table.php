<?php namespace App\Views\Htm;

/*
$table = \App\Views\Htm\Table::load();
$table->setHeading(array_keys($tbody[0]));
echo $table->generate($tbody);
*/


class Table {
const templates = [
'default' => [
	'table_open' => '<table class="table">'
],
'small' => [
	'table_open' => '<table class="table table-sm">'
],
'striped' => [
	'table_open' => '<table class="table table-striped">'
],
'responsive' => [
	'table_open' => '<div class="table-responsive"><table class="table">',
	'table_close' => '</table></div>'
],
'bordered' => [
	'table_open' => '<div class="table-responsive"><table class="table table-bordered border-primary">',
	'table_close' => '</table></div>'
],
'fixed' => [
	'table_open' => '<table class="table text-center" style="table-layout:fixed;">'
]
];

static function load($tkey = 'default') {
	return new \CodeIgniter\View\Table(self::templates[$tkey]);
}

/* return table cells for formatting */

static function money($value) {
	// returns a table cell formatted as money
	return [
		'class' => "text-end",
		'data' => number_to_currency($value, 'GBP', null, 2),

	];
}

static function number($value) {
	// returns a table cell with formatted as integer (right aligned)
	return [
		'data' => intval($value),
		'class' => "text-end"
	];
}

static function centre($value) {
	return [
		'data' => $value,
		'class' => "text-center"
	];
}

static function email($value) {
	// returns a table cell with email as clickable link
	return $value ? mailto($value) : '' ;
}

static function time($value) {
	return $value ? date('d M Y H:i', strtotime($value)) : '' ;
}

static function date($value) {
	return $value ? date('d M Y', strtotime($value)) : '' ;
}

static function bool($value) {
	return $value ? 'yes' : 'no' ;
}
	
}
