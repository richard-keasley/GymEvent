<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$views_var = $tt_lib::get_var('views');
$view_opts = [];
foreach($views_var->value as $key=>$view) {
	if($key) $view_opts[$key] = $view['title'];
}
	
$this->section('content');
$attr = [
	'id' => "editform"
];
echo form_open(base_url(uri_string()), $attr);
echo form_hidden('save', 1);
echo form_hidden('displays', '');
echo form_hidden('views', '');
?>
<section id="displays" class="mb-3">
<h5>Displays</h5>
<p>Each display represents a physical screen. Display #0 (the first  one) is used on the main control panel. Each display's view will be used when the control panel view is set to default.</p>
<div class="table-responsive">
<?php 
$inputs = [
	'title'=> [
		'type' => 'text',
		'data-field' => 'title',
		'class' => 'form-control',
		'style' => 'min-width:12em'
	],
	'tick' => [
		'type' => 'number',
		'data-field' => 'tick',
		'class' => 'form-control',
		'style' => 'min-width:6em;'
	],
	'view' => [
		'data-field' => 'view',
		'class' => 'form-control',
		'options' => $view_opts,
		'style' => 'min-width:8em;'
	],
	'style' => [
		'data-field' => 'style',
		'class' => 'form-control',
		'style' => 'min-width:12em; height:5em;' 
	]
];

$tbody = [];
$get_var = $tt_lib::get_var('displays');
foreach($get_var->value as $ds_id=>$display) {
	$inputs['title']['value'] = $display['title'];
	$inputs['tick']['value'] = $display['tick'];
	$inputs['view']['selected'] = strval($display['view']);
	$inputs['style']['value'] = $display['style'];
	$tbody[] = [
		$ds_id,
		form_input($inputs['title']),
		form_input($inputs['tick']),
		form_dropdown($inputs['view']),
		form_textarea($inputs['style']),
		'<button name="del" type="button" class="btn bi-trash btn-danger btn-sm"></button>'
	];
}

$template = ['table_open' => '<table class="table data">'];
$table->setTemplate($template);
$table->setHeading(['#','title','tick [ms]','view','style [css]','']);
echo $table->generate($tbody);
?>
</div>
<button name="add" type="button" class="btn btn-success bi-plus-square"></button>
</section>

<section id="views">
<h5>Views</h5>
<p>Each display can be assigned a separate default view. "info" and "images" determine how long [seconds] the view pauses at each frame.</p>
<div class="table-responsive">
<?php
$inputs = [
	'title'=> [
		'type' => 'text',
		'data-field' => 'title',
		'class' => 'form-control',
		'style' => 'width:8em;'
	],
	'info' => [
		'type' => 'number',
		'data-field' => 'info',
		'class' => 'form-control',
		'style' => 'width:4.5em;'
	],
	'images' => [
		'data-field' => 'images',
		'type' => 'number',
		'class' => 'form-control',
		'style' => 'width:4.5em;'
	],
	'html' => [
		'data-field' => 'html',
		'class' => 'form-control',
		'style' => 'min-width:12em; height:5em;'
	]
];
$tbody = [];
foreach($views_var->value as $vw_id=>$view) {
	if($vw_id) { // don't show default
		$inputs['title']['value'] = $view['title'];
		$inputs['info']['value'] = $view['info'];
		$inputs['images']['value'] = $view['images'];
		$inputs['html']['value'] = $view['html'];
		$tbody[] = [
			$vw_id,
			form_input($inputs['title']),
			form_input($inputs['info']),
			form_input($inputs['images']),
			form_textarea($inputs['html']),
			'<button name="del" type="button" class="btn bi-trash btn-danger btn-sm"></button>'
		];
	}
}
$template = ['table_open' => '<table class="table data">'];
$table->setTemplate($template);
$table->setHeading(['#', 'title','info [s]','image [s]','HTML','']);
echo $table->generate($tbody); ?>
</div>
<button name="add" type="button" class="btn btn-success bi-plus-square"></button>
</section>
</form>
<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top">
	<?php echo \App\Libraries\View::back_link('control/teamtime'); ?>
	<button onclick="pagesave()" type="button" class="btn btn-primary">save</button>
</div>
<?php $this->endSection();

$this->section('bottom');?>
<script>
let displayrows = '#displays .data tbody tr';
let viewrows = '#views .data tbody tr';

$(function() {

$(displayrows).find('[name=del]').click(function(){
	if($(displayrows).length<2) return;
	$(this).closest('tr').remove();
});
$(viewrows).find('[name=del]').click(function(){
	if($(viewrows).length<2) return;
	$(this).closest('tr').remove();
});
$('#displays [name=add]').click(function() {
	var $tr = $(displayrows).last();
	var $clone = $tr.clone(true);
	$clone.find('input').val('');
	$clone.find('textarea').val('');
	$tr.after($clone);
});
$('#views [name=add]').click(function() {
	var $tr = $(viewrows).last();
	var $clone = $tr.clone(true);
	$clone.find('input').val('');
	$clone.find('textarea').val('');
	$tr.after($clone);
});

});

const display_vars = ['title','tick','view','style'];
const view_vars = ['title','info','images','html'];
function pagesave() {
	var appvar = [];
	$(displayrows).each(function() {
		var data = {};
		for(idx in display_vars)	{
			var item = display_vars[idx];
			data[item] = $(this).find('[data-field='+item+']').val();
		};
		appvar.push(data);
	});
	$('[name=displays]').val(JSON.stringify(appvar));

	var appvar = [];
	$(viewrows).each(function() {
		var data = {};
		for(idx in view_vars)	{
			var item = view_vars[idx];
			data[item] = $(this).find('[data-field='+item+']').val();
		};
		appvar.push(data);
	//	console.log(appvar);
	
	});
	$('[name=views]').val(JSON.stringify(appvar));
	//console.log(appvar);
	$('#editform').submit();
};
</script>
<?php $this->endSection();
