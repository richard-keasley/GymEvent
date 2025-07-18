<?php $this->extend('default');

$test_data = [
	[0, 0, '=[0,0]'],
	[3, 1, '=[0,0]'],
	[3, 1, '=sum([-1,0]:[-1,20])'],
	[3, 2, '=rank([-1], [-1,$-1]:[-1,$19])'],
	[3, 3, '=rank([-1], [-1,$-2]:[-1,$18])'],
	[8, 1, '=sum([-5]:[-1])'],
	[3, 1, '=sum([nonsense])'],
	[3, 8, '=sum([,-4]:[,-1])'],
	[3, 8, '=RANK([-1,$-4]:[-1,$1])']
];

$formatter = new \App\Format\CSVFormatter;

$this->section('content');

foreach($test_data as $key=>$row) {
	$xlcell = new \App\Format\xlcell($row[0], $row[1], $row[2]);
	$test_data[$key]['address'] = $xlcell->address();
	$test_data[$key]['text'] = strval($xlcell);
}

$table = \App\Views\Htm\Table::load('responsive');
$table->setHeading(['x', 'y', 'content', 'address', 'text']);
echo $table->generate($test_data);
$this->endSection();

