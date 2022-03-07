<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
helper('inflector');

$this->section('content'); 
#d($event);

$attr = [
	'id' => "editform"
];
$hidden = [
	'save' => '1',
	'cmd' => '',
	'key' => ''
];
echo form_open_multipart(base_url(uri_string()), $attr, $hidden); 
 
$acc = new \App\Views\Htm\Accordion;

ob_start(); // Details ?> 
<p><label>ID:</label> <?php echo $id;?></p>
<p class="input-group">
	<label class="input-group-text">title</label>
	<?php echo form_input("title", $event->title, 'class="form-control"');?>
</p>
<p class="input-group">
	<label class="input-group-text">date</label>
	<?php echo form_input("date", $event->date, 'class="form-control"', 'date');?>
</p>

<?php
$attr = [
	'name' => 'description',
	'value' => $event->description
];
$editor = new \App\Views\Htm\Editor($attr);
echo $editor->htm();

$acc->set_item('Details', ob_get_clean());

ob_start(); // Payment
$attr = [
	'name' => 'payment',
	'value' => $event->payment
];
$editor = new \App\Views\Htm\Editor($attr);
echo $editor->htm();
?> 
<p>This explains how clubs should pay. Include bank details and any dead-lines.</p>
<?php
$acc->set_item('Payment', ob_get_clean());

ob_start(); // disciplines / categories 

if($event->clubrets) { ?>
<p>This section should only be altered while the event sate 'clubrets' is set to 'waiting'.</p>
<?php } 

else { ?> 
<p>Do not use spaces, commas or special characters within discipline and categories. Try to use the same abbreviations as <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#sbdis">scoreboard</button>.</p>
<div class="modal" id="sbdis" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Discipline abbreviations</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<ul class="list-group">
	<?php
	$scoreboard = new \App\ThirdParty\scoreboard;
	foreach($scoreboard->get_discats() as $category) { ?>
		<li class="list-group-item">
		<strong><?php echo $category['Description']; ?></strong>
		<ul><?php 
		foreach($category['disciplines'] as $dis) {
			printf('<li><strong>%s</strong> %s</li>', $dis['ShortName'], $dis['Name']);
		} 
		?></ul>
		</li>
	<?php } ?>
	</ul>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

<?php } ?>

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

if($event->clubrets) {
	foreach($input as $key=>$field) {
		$input[$key]['readonly'] = "readonly";
	}
}

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
<?php 
$acc->set_item('disciplines / categories', ob_get_clean());

ob_start(); // Staff ?>
<p>A comma separated list of all staff categories. E.g. <code>coach, judge, helper</code>. Items should not include spaces or special characters.</p>
<?php 
$input = [
	'name' => "staffcats",
	'value' => implode(', ', $event->staffcats),
	'class' => "form-control"
];
if($event->clubrets) $input['readonly'] = "readonly";
echo form_input($input);


# echo form_input('staffcats', implode(', ', $event->staffcats), 'class="form-control"'); 
$acc->set_item('Staff', ob_get_clean());

ob_start(); // Event states
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
<?php } 
$acc->set_item('Event states', ob_get_clean());

ob_start(); // Downloads
?>
<p><strong>NB:</strong> Upload an image named 'logo.*' to use it as an image on the main event page.</p>
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
<?php
$acc->set_item('Downloads', ob_get_clean());

echo $acc->htm(); 

echo form_close(); 

d($event);

$this->endSection(); 

$this->section('top') ?>
<div class="toolbar sticky-top">
	<?php echo \App\Libraries\View::back_link("admin/events/view/{$event->id}"); ?>
	<button class="btn btn-primary" type="button" name="cmd" value="save">save</button>
</div>
<?php $this->endSection(); 

$this->section('bottom') ?>
<script>
$(function() {
	
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
