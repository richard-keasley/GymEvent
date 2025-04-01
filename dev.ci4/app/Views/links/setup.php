<?php $this->extend('default');

$this->section('content'); 
$attrs = [
	'id' => "editform"
];
$hidden = [
	'save' => "1"
];
echo form_open('', $attrs, $hidden); ?>
<h4>Edit external links</h4>
<?php 

$input = [
	'type' => 'url',
	'class' => 'form-control',
	'style' => 'min-width:30em'
];
$tbody = []; 
foreach($links as $key=>$link) {
	$input['value'] = $link;		
	$input['name'] = $key;		
	$tbody[] = [
		sprintf('<label class="form-label">%s</label>', $key),
		form_input($input)
	];
}

$table = \App\Views\Htm\Table::load('responsive');
$table->setHeading(['', 'url']);
echo $table->generate($tbody);

?>

<div class="toolbar">
	<?php echo \App\Libraries\View::back_link("setup");?>
	<button type="submit" class="btn btn-primary">save</button>
	<a title="View client home page" class="btn btn-outline-secondary" href="/links"><span class="bi bi-eye"></span></a>
</div>

<?php 
echo form_close();
$this->endSection(); 
