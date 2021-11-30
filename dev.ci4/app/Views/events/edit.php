<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$acc = new \App\Libraries\Ui\Accordion;
helper('inflector');

$this->section('content'); 
#d($event);

$attr = [
	'id' => "editform"
];
echo form_open_multipart(base_url(uri_string()), $attr); ?>
<input type="hidden" name="save" value="1">
<input type="hidden" name="cmd" value="">
<input type="hidden" name="key" value="">

<?php 
echo $acc->start('accordion');

echo $acc->item_start('Details'); ?>
<p><label>ID:</label> <?php echo $id;?></p>
<p class="input-group">
	<label class="input-group-text">title</label>
	<?php echo form_input("title", $event->title, 'class="form-control"');?>
</p>
<p class="input-group">
  <label class="input-group-text">date</label>
	<?php echo form_input("date", $event->date, 'class="form-control"', 'date');?>
</p>
<p><label>Description [HTML]</label> <?php echo form_textarea("description", $event->description, 'class="form-control"');?></p>

<?php echo $acc->item_start('Payment'); ?>
<p><label>Payment info [HTML]</label>
<?php echo form_textarea("payment", $event->payment, 'class="form-control"'); ?>
</p>
<p>The text that explains how clubs should pay. Include bank details and any dead-lines.</p>

<?php echo $acc->item_start('disciplines / categories'); ?>
<p>Do not use spaces, commas or special characters within discipline names and  category names.</p>
<div id="discats">
<?php 
$tbody = [];
$discats = $event->discats;
if(!$discats) { // provide one blank row 
	$discats = [[
		'name'=>'', 'inf' => [], 'cats' => [] 
	]];
}

$input = [
'name' => [
	'name' => 'name',
	'class' => 'form-control'
],
'inf' => [
	'name' => 'inf',
	'class' => 'form-control',
	'cols' => 5,
	'rows' => 5
],
'cats' => [
	'name' => 'cats',
	'class' => 'form-control',
	'cols' => 30,
	'rows' =>5
]
];

foreach($discats as $key=>$discat) {
	$input['name']['value'] = $discat['name'];
	
	$value = [];
	foreach($discat['inf'] as $key=>$val) $value[] = "{$key} = {$val}";
	$input['inf']['value'] = implode("\n", $value);
		
	$rows = [];
	foreach($discat['cats'] as $row) $rows[] = implode(', ', $row);
	$input['cats']['value'] = implode("\n", $rows);
		
	$tbody[] = [
		form_input($input['name']),
		form_textarea($input['inf']),
		form_textarea($input['cats']),
		'<button type="button" name="del" class="btn bi-trash btn-danger" title="delete"></button>'
	];
}
$template = ['table_open' => '<table class="discats">'];
$table->setTemplate($template);
$table->setHeading('dis','inf','cats','');
echo $table->generate($tbody);
echo form_hidden('discats', '');?>
<button name="add" type="button" class="btn bi-plus-square btn-success" title="add row"></button>
</div>

<?php echo $acc->item_start('Staff'); ?>
<p>A comma separated list of all staff categories. E.g. <code>coach, judge, helper</code>. Items should not include spaces or special characters.</p>
<?php echo form_input('staffcats', implode(', ', $event->staffcats), 'class="form-control"'); ?>

<?php echo $acc->item_start('Event states'); 
$fieldnames = ['clubrets', 'music', 'videos'];
$colours = \App\Entities\Event::state_colours;
$input = ['class' => 'btn-check'];
foreach($fieldnames as $fieldname) { ?>
	<div class="btn-bar mb-2">
	<div class="btn-group">
	<label class="input-group-text"><?php echo $fieldname;?></label>
	<?php 
	$input['name'] = $fieldname;
	foreach(\App\Entities\Event::state_labels as $state=>$state_label) {
		$input['id'] = "{$fieldname}_{$state_label}";
		$input['checked'] = $event->$fieldname==$state;
		$input['value'] = $state;
		echo form_radio($input);
		printf('<label class="btn btn-outline-%s" for="%s">%s</label>', $colours[$state], $input['id'], $state_label);
	} ?>
	</div>
	</div>
<?php } ?>

<?php echo $acc->item_start('Downloads'); ?>
<ul class="list-group"><?php 
$pattern = '<li class="list-group-item">%s <button type="button" name="cmd" value="delfile" data-key="%u" class="btn btn-danger bi-trash"></button></li>';
foreach($event->files as $key=>$filename) {
	printf($pattern, $event->file_link($filename), $key);
} ?></ul>
<div class="row my-3">
<div class="col-auto">
	<input type="file" name="file" class="form-control">
</div>
<div class="col-auto">
	<button class="btn btn-primary" type="button" name="cmd" value="upload">upload</button>
</div>
</div>

<?php echo $acc->end(); ?> 
</form>
<?php $this->endSection(); 

$this->section('top') ?>
<div class="toolbar sticky-top">
	<?php echo \App\Libraries\View::back_link("admin/events/view/{$event->id}"); ?>
	<button class="btn btn-primary" type="button" name="cmd" value="save">save</button>
</div>
<?php $this->endSection(); 

$this->section('bottom') ?>
<script>
$(function() {

let activeTab = localStorage.getItem('activeTab');
if(activeTab) $(activeTab).collapse('show');
$('#accordion [data-bs-toggle=collapse]').on('click', function(e) {
	activeTab = e.target.getAttribute('data-bs-target');
	localStorage.setItem('activeTab', activeTab);
});	
	
$('#discats [name=add]').click(function() {
	var $tr = $('#discats tbody tr:last');
	var $clone = $tr.clone(true);
	$clone.find('input').val('');
	$clone.find('textarea').val('');
	$tr.after($clone);
});
$('#discats [name=del]').click(function() {
	var count = $('#discats tbody tr').length;
	console.log(count);
	if(count<2) return;
	var $tr = $(this).closest('tr');
	console.log($tr);	
	$tr.remove();
});
// submit form
$('[name=cmd]').click(function() {
	var discats = [];
	$('#discats tbody tr').each(function() {
		var discat = {
			name: $(this).find('[name=name]').val(),
			inf:  $(this).find('[name=inf]').val(),
			cats: $(this).find('[name=cats]').val()
		};	
		discats.push(discat);
	});
	$('[name=discats]').val(JSON.stringify(discats));
	
	$('input[name=cmd]').val($(this).val());
	if('key' in this.dataset) $('input[name=key]').val(parseInt(this.dataset.key));

	$('#editform').submit();
});


});
</script>
<?php $this->endSection(); 

