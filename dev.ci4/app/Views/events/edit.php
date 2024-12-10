<?php $this->extend('default');
helper('inflector');

$this->section('content'); 
#d($event);

$attrs = [
	'id' => "editform"
];
$hidden = [
	'save' => '1',
	'cmd' => '',
	'key' => ''
];
echo form_open_multipart(current_url(), $attrs, $hidden); 
 
$acc = new \App\Views\Htm\Accordion;

ob_start(); // Details ?> 
<p><label>ID:</label> <?php echo $id;?></p>
<p class="input-group">
	<label class="input-group-text">title</label>
	<?php echo form_input("title", $event->title, 'class="form-control"');?>
</p>
<p class="input-group" style="max-width:20em;">
	<label class="input-group-text">date</label>
	<?php echo form_input("date", $event->date, 'class="form-control"', 'date');?>
</p>

<?php
$attrs = [
	'name' => 'description',
	'value' => $event->description
];
echo new \App\Views\Htm\Editor($attrs);

$acc->set_item('Details', ob_get_clean());

ob_start(); // staff 

$attrs = [
	'name' => 'staff',
	'value' => $event->staff
];
echo new \App\Views\Htm\Editor($attrs);

$edit_locked = $event->clubrets > 0;
if($edit_locked) { ?>
<p class="alert alert-danger">Staff categories can only be altered when the event state 'clubrets' is set to 'waiting'.</p>
<?php } ?>

<p>A comma separated list of all staff categories. E.g. <code>coach, judge, helper</code>. Items should not include spaces or special characters.</p>
<?php 

$input = [
	'name' => "staffcats",
	'value' => implode(', ', $event->staffcats),
	'class' => "form-control"
];
if($edit_locked) $input['readonly'] = "readonly";
echo form_input($input);

?>
<section class="my-2 p-1 border">
<div>Staff fee is applied to the club return fee calculation unless the user agrees all staffing requirements have been met.</div>
<div class="input-group">
<label class="input-group-text">Staff fee &pound;</label>
<?php
$input = [
	'name' => "stafffee",
	'value' => $event->stafffee,
	'type' => "number",
	'step' => "0.01",
	'class' => "form-control text-end",
	'style' => "max-width: 10em;"
];
if($edit_locked) $input['readonly'] = "readonly";
echo form_input($input);
?></div>
</section>

<?php
$acc->set_item('Staff', ob_get_clean());


ob_start(); // disciplines / categories 

$attrs = [
	'name' => 'participants',
	'value' => $event->participants
];
echo new \App\Views\Htm\Editor($attrs);

$edit_locked = $event->clubrets > 0;

if($edit_locked) { ?>
<p class="alert alert-danger">This section can only be altered while the event state 'clubrets' is set to 'waiting'.</p>
<?php } 

else { ?> 
<p>Only use alpha-numeric characters, dashes and under-scores in disciplines and categories. No spaces, &amp;, commas, etc.</p> 
<?php } ?>

<div id="discats">
<?php 
$discats = $event->discats;
if(!$discats) { // provide one blank row 
	$discats = [[
		'name'=>'', 'inf' => [], 'cats' => [], 'opts' => [] 
	]];
}

$input = [
'name' => [
	'name' => 'name',
	'class' => 'form-control',
	'style' => "min-width:4em;"
],
'inf' => [
	'name' => 'inf',
	'class' => 'form-control',
	'cols' => 5,
	'rows' => 5,
	'style' => "min-width:6em;"
],
'cats' => [
	'name' => 'cats',
	'class' => 'form-control',
	'cols' => 30,
	'rows' =>5,
	'style' => "min-width:8em;"
],
'opts' => [
	'name' => 'opts',
	'class' => 'form-control',
	'cols' => 5,
	'rows' =>5,
	'style' => "min-width:6em;"
]
];

if($edit_locked) {
	foreach($input as $key=>$field) {
		$input[$key]['readonly'] = "readonly";
	}
	$btn_del = '';
	$btn_add = '';
}
else {
	$btn_del = '<button type="button" name="del" class="btn bi-trash btn-danger" title="delete"></button>';
	$btn_add ='<button name="add" type="button" class="btn bi-plus-square btn-success" title="add row"></button>';
}

$tbody = [];


foreach($discats as $key=>$discat) {
	$input['name']['value'] = $discat['name'];
	
	$value = [];
	foreach($discat['inf'] as $key=>$val) $value[] = "{$key} = {$val}";
	$input['inf']['value'] = implode("\n", $value);
		
	$value = [];
	foreach($discat['cats'] as $row) $value[] = implode(', ', $row);
	$input['cats']['value'] = implode("\n", $value);
	
	$input['opts']['value'] = implode("\n", $discat['opts']);
		
	$tbody[] = [
		form_input($input['name']),
		form_textarea($input['inf']),
		form_textarea($input['cats']),
		form_textarea($input['opts']),
		$btn_del
	];
}

$format = '%1$s <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#help-%1$s"><span class="bi-info-lg"></span></button>';

$thead = [
	sprintf($format, 'dis'),
	sprintf($format, 'inf'),
	
	'cats ' . new \App\Views\Htm\Popover('Comma separated lines of single words. Each line is a set of options.', 'Category options'),
		
	'options ' . new \App\Views\Htm\Popover('Single words, each on a separate line', 'Entry options')
];

$table = \App\Views\Htm\Table::load('responsive');
$table->setHeading($thead);
echo $table->generate($tbody);

echo $btn_add;

echo form_hidden('discats', '');

?>
</div>

<div class="modal" id="help-inf" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Discipline / Category inf</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
<p>Format like a regular <code>ini</code> file.</p>
<dl>
<dt>fe</dt><dd>Fee per entry</dd>
<dt>fg</dt><dd>Fee per gymnast</dd>
<dt>team</dt><dd>Request team name</dd>
<dt>n</dt><dd>Number of lines per entry (size of text area)</dd>
<dt>cat</dt><dd>Format for DoB appended to entry's category (e.g. <code>cat = y-m</code>).</dd>
</dl>	
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

<div class="modal" id="help-dis" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Discipline abbreviations</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
<p>Use the same abbreviations as used in score-board as much as possible.</p>
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
<?php 

$acc->set_item('Participants', ob_get_clean());

ob_start(); // Payment
$attrs = [
	'name' => 'payment',
	'value' => $event->payment
];
echo new \App\Views\Htm\Editor($attrs);
?> 
<p>This explains how clubs should pay. Include bank details and any dead-lines.</p>
<?php
$acc->set_item('Payment', ob_get_clean());


ob_start(); // Event states
?>

<div class="form-check form-switch my-2">
<label class="form-check-label" title="Private events will not appear on the public website"><?php
$input = [
	'name' => "private",
	'value' => "1",
	'class' => "form-check-input",
	'type' => "checkbox",
	'role' => "switch"
];
if($event->private) $input['checked'] = "checked";
echo form_input($input);
?> make this event private
</label>
</div>

<?php
$colours = \App\Entities\Event::state_colours;
$input = ['class' => 'btn-check'];
foreach($states as $fieldname) { ?>
	<div class="btn-bar mb-2">
	<div class="btn-group">
	<label class="input-group-text"><?php echo $fieldname;?></label>
	<?php 
	$input['name'] = $fieldname;
	foreach(\App\Entities\Event::states as $state_label=>$state) {
		$input['id'] = "{$fieldname}_{$state_label}";
		$input['checked'] = $event->$fieldname==$state;
		$input['value'] = $state;
		echo form_radio($input);
		printf('<label class="btn btn-outline-%s" for="%s">%s</label>', $colours[$state], $input['id'], $state_label);
	} ?>
	</div>
	</div>
<?php }

?>
<section class="my-2 row" style="max-width:45em"><?php
# d($event->dates);
foreach($event->dates as $key=>$date) {
	echo '<div class="input-group my-2" style="max-width:20em;">';
	printf('<label class="input-group-text">%s</label>', humanize($key));
	
	$fldname = "dates_{$key}";
	$input = [
		'type' => "date",
		'class' => "form-control",
		'name' => $fldname,
		'value' => $date
	];
	echo form_input($input);
	echo '</div>';
	
}
?></section>
<?php

$acc->set_item('Event states', ob_get_clean());


ob_start(); // Downloads
?>
<p><strong>NB:</strong> Upload an image named 'logo.*' to use it as an image on the main event page.</p>

<?php 
$downloads = new \App\Views\Htm\Downloads($event->downloads);
$downloads->template['item_after'] = ' <button type="button" name="cmd" value="delfile" data-key="%1$u" class="btn btn-sm btn-danger bi-trash"></button>';
echo $downloads->htm();
?>

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

# d($event->discats);

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
	if(count<2) return;
	var $tr = $(this).closest('tr');
	$tr.remove();
});

// submit form
$('button[name=cmd]').click(function() {
	var discats = [];
	$('#discats tbody tr').each(function() {
		var discat = {
			name: $(this).find('[name=name]').val(),
			inf:  $(this).find('[name=inf]').val(),
			cats: $(this).find('[name=cats]').val(),
			opts: $(this).find('[name=opts]').val()
		};	
		discats.push(discat);
	});
	$('input[name=discats]').val(JSON.stringify(discats));
	
	$('input[name=cmd]').val($(this).val());	
	$('input[name=key]').val(this.dataset.key ?? '');

	$('#editform').submit();
});


});
</script>
<?php $this->endSection(); 
