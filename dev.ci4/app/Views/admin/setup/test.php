<?php $this->extend('default');

$this->section('content');


$style = 'background:#F99;text-align:right;width:5em;';
$data = [
	['item', ['data'=>'count', 'style'=>$style]],
	['shirt', ['data'=>2, 'style'=>$style]],
	['hat', ['data'=>1, 'style'=>$style]]
];
$footing = ['total', ['data'=>3, 'style'=>$style]];

echo '<p>Use default template</p>';
$table = new \CodeIgniter\View\Table();
$table->setFooting($footing);
echo $table->generate($data);

echo '<p>Update template footer to use {th} rather then {td}.</p>';
$template = [
	'footing_cell_start' => '<th>',
	'footing_cell_end' => '</th>'
];
$table = new \CodeIgniter\View\Table($template);
$table->setFooting($footing);
echo $table->generate($data);


$this->endSection();

$this->section('top'); ?>
<p class="alert alert-light">Random page to allow you to test ideas.</p>
<?php $this->endSection();