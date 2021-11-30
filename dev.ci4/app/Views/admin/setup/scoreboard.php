<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();

$this->section('content'); 
$attr = [
	'id' => "editform"
];
echo form_open(base_url(uri_string()), $attr); ?>
<h4>Create scoreboard links</h4>
<div class="table-responsive">
<?php 
echo form_hidden('links');
echo form_hidden('save', 1);

if(!$links) $links = [['label'=>'', 'url' =>'']];

$inputs = [
	'label'=> [
		'type' => 'text',
		'data-field' => 'label',
		'class' => 'form-control',
		'style' => 'min-width:6em'
	],
	'url' => [
		'type' => 'text',
		'data-field' => 'url',
		'class' => 'form-control',
		'style' => 'min-width:10em;'
	]
];


$tbody = [];
foreach($links as $link) {
	$inputs['label']['value'] = $link['label'];
	$inputs['url']['value'] = $link['url'];
	$tbody[] = [
		form_input($inputs['label']),
		form_input($inputs['url']),
		'<button name="del" type="button" class="btn bi-trash btn-danger btn-sm"></button>'
	];
}
$template = ['table_open' => '<table class="table data">'];
$table->setTemplate($template);
$table->setHeading(['label', 'url', '']);
echo $table->generate($tbody);
?>
</div>
<button name="add" type="button" class="btn btn-success bi-plus-square"></button>

<div class="toolbar">
	<?php echo \App\Libraries\View::back_link("setup");?>
	<button type="button" onclick="pagesave()" class="btn btn-primary">save</button>
</div>

</form>

<?php $this->endSection(); 

$this->section('bottom');?>
<script>
const link_vars = ['label','url'];
const link_rows = '#editform .data tbody tr';

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
	var appvar = [];
	$(link_rows).each(function() {
		var data = {};
		for(idx in link_vars)	{
			var item = link_vars[idx];
			data[item] = $(this).find('[data-field='+item+']').val();
		};
		appvar.push(data);
	});
	$('[name=links]').val(JSON.stringify(appvar));
	$('#editform').submit();
};
</script>
<?php $this->endSection();

