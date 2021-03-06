<?php namespace App\Views\Htm;

class Table {
const templates = [
'default' => [
	'table_open' => '<table class="table">'
],
'small' => [
	'table_open' => '<table class="table table-sm">'
],
'responsive' => [
	'table_open' => '<div class="table-responsive"><table class="table">',
	'table_close' => '</table></div>'
],
'bordered' => [
	'table_open' => '<div class="table-responsive"><table class="table table-bordered border-primary">',
	'table_close' => '</table></div>'
]
];

static function load($tkey = 'default') {
	return new \CodeIgniter\View\Table(self::templates[$tkey]);
}
	
}
