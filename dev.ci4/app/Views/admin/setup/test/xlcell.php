<?php $this->extend('default');

$test_data = [
	[3, 1, '=[0,0]'],
	[3, 1, '=sum([-1,0]:[-1,20])'],
	[3, 2, '=rank([-1], [-1,-1]:[-1,19])'],
	[8, 1, '=sum([-5]:[-1])'],
	[3, 1, '=sum([nonsense])'],
	[3, 8, '=sum([,-4]:[,-1])']
];

$csv = new \App\Libraries\Csv;

foreach($test_data as $key=>$row) {
	$xlcell = new \App\Libraries\xlcell($row[0], $row[1], $row[2]);
	$test_data[$key]['address'] = $xlcell->address();
	$test_data[$key]['text'] = strval($xlcell);
}


$this->section('content');
$table = \App\Views\Htm\Table::load('responsive');
$table->setHeading(['x', 'y', 'content', 'address', 'text']);
echo $table->generate($test_data);
$this->endSection();

