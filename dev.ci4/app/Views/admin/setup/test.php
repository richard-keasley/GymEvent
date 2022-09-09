<?php $this->extend('default');

$this->section('content');
	
$style = 'background:#F99;text-align:right;width:5em;';
$data = [
	['item', ['data'=>'count', 'style'=>$style]],
	['shirt', ['data'=>2, 'style'=>$style]],
	['hat', ['data'=>1, 'style'=>$style]]
];
$footing = ['total', ['data'=>3, 'style'=>$style]];
$templates = [
	[],
	[
		'footing_cell_start' => '<th>',
		'footing_cell_end' => '</th>',
		'heading_cell_start' => '<td>',
		'heading_cell_end' => '</td>'
	],
	[
		'heading_cell_start' => '<td>',
		'heading_cell_end' => '</td>'
	]
];

foreach($templates as $tkey=>$template) {
	printf('<h4>Template %s</h4>', $tkey);
	printf('<pre>%s</pre>', htmlentities   (print_r($template, 1)));
	$table = new \CodeIgniter\View\Table($template);
	$table->setFooting($footing);
	echo $table->generate($data);
}

$this->endSection();

$this->section('top'); ?>
<p class="alert alert-light">Random page to allow you to test ideas.</p>
<?php $this->endSection();