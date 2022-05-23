<?php $this->extend('default');

$this->section('content'); 
$attr = [
	'id' => "editform"
];
$hidden = [
	'links' => '',
	'save' => 1
];
echo form_open(base_url(uri_string()), $attr, $hidden); ?>
<h4>Create scoreboard links</h4>
<p>These links appear on the scoreboard information pages.</p>
<?php 
$inputs = [
	[
		'type' => 'url',
		'class' => 'form-control',
		'style' => 'min-width:10em'
	],
	[
		'type' => 'text',
		'class' => 'form-control',
		'style' => 'min-width:6em;'
	]
];

if(!$links) $links = [[]];
$tbody = []; $trow = [];
foreach($links as $link) {
	foreach($inputs as $key=>$input) {
		$inputs[$key]['value'] = $link[$key] ?? '';
		$trow[$key] = form_input($inputs[$key]);
	}
	$trow['cmd'] = '<button name="del" type="button" class="btn bi-trash btn-danger btn-sm"></button>';
		
	$tbody[] = $trow;	
}

$table = \App\Views\Htm\Table::load('responsive');
$table->setHeading(['url', 'label', '']);
echo $table->generate($tbody);

?>
<button name="add" type="button" class="btn btn-success bi-plus-square"></button>

<div class="toolbar">
	<?php echo \App\Libraries\View::back_link("setup");?>
	<button type="button" onclick="pagesave()" class="btn btn-primary">save</button>
	<?php 
	$attr = [
		'class' => "nav-link",
		'title' =>"view scoreboard"
	];
	echo anchor(base_url('/scoreboard'), 'view', $attr);?>
</div>

<?php 
echo form_close();
$this->endSection(); 

$this->section('bottom');?>
<script>
const link_rows = '#editform tbody tr';

$(function() {

$(link_rows).find('[name=del]').click(function() {
	var $tr = $(this).closest('tr');
	$tr.find('input').val('');
	if($(link_rows).length>1) $tr.remove();
});

$('#editform [name=add]').click(function() {
	var $tr = $(link_rows).last();
	var $clone = $tr.clone(true);
	$clone.find('input').val('');
	$tr.after($clone);
});

});

function pagesave() {
	let appvar = [];
	let datarow = [];
		
	$(link_rows).each(function() {
		datarow = [];
		$(this).find('input').each(function() {
			datarow.push(this.value);
		});
		appvar.push(datarow);
	});
	// console.log(appvar);
	$('[name=links]').val(JSON.stringify(appvar));
	$('#editform').submit();
};
</script>
<?php $this->endSection();

