<?php $this->extend('default');
$scoreboard = new \App\ThirdParty\scoreboard;
$exesets = $scoreboard->get_exesets();

$this->section('content');?>
<form name="selector" method="GET" class="input-group">
<label class="input-group-text">Select discipline to edit</label> 
<select name="disid" class="form-control"><?php 
foreach($entries as $dis) { 
	$selected = $filter['disid']===$dis->id ? 'selected="selected"' : '' ;
	printf('<option value="%s" %s>%s</option>', $dis->id, $selected, $dis->name);
}
?></select>
<script>
$(function() {
$('[name=disid]').change(function() { $('[name=selector]').submit(); });
});
</script>
</form>

<?php echo form_open(base_url(uri_string())); ?>
<input type="hidden" name="save" value="1">
<div class="toolbar sticky-top">
	<?php echo \App\Libraries\View::back_link("entries/view/{$event->id}");?>
	<button class="btn btn-primary" type="submit">save</button>
</div>
<?php
$exeset_opts = [0 => '[none]'];
if(!$exesets) {
	printf('<p class="p-2 alert-danger"><span class="bi bi-exclamation-triangle"></span> %s</p>', $scoreboard->error ? $scoreboard->error : 'Empty scoreboard data');
}
else {
	foreach($exesets as $exeset) {
		$exeset_opts[$exeset['SetId']] = $exeset['Name'];
	}
}

$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);
$tbody=[];

$row = []; $tr = [];
$inputs = [
	'name' => [
		'type' => 'text',
		'class' => 'form-control',
		'style' => 'min-width:8em;'
	], 
	'abbr' => [
		'type' => 'text',
		'class' => 'form-control',
		'style' => 'width:5em;'
	],  
	'sort' => [
		'type' => 'text',
		'class' => 'form-control',
		'style' => 'width:4em;'
	], 
	'exercises' => [
		'type' => 'select',
		'options' => $exeset_opts,
		'class' => 'form-control',
		'style' => 'min-width:5em;'
	], 
	'music' => [
		'type' => 'text',
		'class' => 'form-control',
		'style' => 'min-width:5em;'
	], 
	'videos' => [
		'type' => 'text',
		'class' => 'form-control',
		'style' => 'min-width:5em;'
	]
];
foreach($entries as $dis) { 
if($dis->id==$filter['disid']) { ?>
	<fieldset class="input-group my-3">
		<label class="input-group-text">Discipline</label>
		<?php echo form_input("dis{$dis->id}_name", $dis->name, 'class="form-control"');?> 
		<label class="input-group-text">abbreviated</label><?php echo form_input("dis{$dis->id}_abbr", $dis->abbr, 'class="form-control"');?>
	</fieldset>
	<?php 
	foreach($dis->cats as $cat) {
		foreach($inputs as $key=>$input) {
			$input['name'] = "cat{$cat->id}_{$key}";
			switch($input['type']) {
				case 'select':
					$input['selected'] = $cat->$key;
					$td = form_dropdown($input);
					break;
				default:
					$input['value'] = $cat->$key;
					if(is_array($input['value'])) {
						$input['value'] = implode(' ', $input['value']);
					}
					$td = form_input($input);
			}
			$tr[$key] = $td;
		}
		$count = count($cat->entries);
		$tr['last'] = $count ? $count : '<button class="btn btn-sm btn-danger bi-trash" type="button" onClick="delrow(this)"></button>'; 
		$tbody[] = $tr;
		
	}
} 
} 
$table->setHeading(['category','abbr','sort','exercise<br>set','music','videos','count']);
?>
<div class="table-responsive"><?php
echo $table->generate($tbody);
?></div>

<div id="newrow">
<button class="btn btn-success bi bi-plus-circle" type="button" onclick="newrow(1)"></button>
<?php 
$tr = [];
foreach($inputs as $key=>$input) {
	$input['name'] = "newrow_{$key}";
	if(isset($input['options'])) {
		$input['selected'] = 0;
		$tr[$key] = form_dropdown($input);
	}
	else {
		$input['value'] = '';
		$tr[$key] = form_input($input);
	}
}
$tr['last'] = '<button class="btn btn-danger bi bi-x-circle" type="button" onclick="newrow(0)"></button>';

$template = ['table_open' => '<table class="table d-none">'];
$table->setTemplate($template);
$table->setHeading('Add category');
echo $table->generate([$tr]);
?>
<script>
function delrow(el) {
	var tr = el.parentElement.parentElement;
	var inputs = tr.querySelectorAll('input');
	inputs.forEach(function(input) { 
		input.value = '';
	});
}

function newrow(show) {
	var table = document.querySelector('#newrow > table');
	var button = document.querySelector('#newrow > button');
	if(show) {
		table.classList.remove("d-none"); 
		button.classList.add("d-none"); 
	}
	else {
		table.classList.add("d-none"); 
		button.classList.remove("d-none"); 
	}
}
</script>
</div>

<section class="my-2">
<?php if($exesets) { ?>
<p><button class="ps-3 btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#sbdata" aria-expanded="false">
<span class="bi bi-arrows-expand"></span>
<span class="bi bi-arrows-collapse"></span>
</button> View scoreboard data (last updated on <?php echo $scoreboard->get_time('exerciseset', 'j F Y');?>)</p>
<div id="sbdata" class="collapse">
<h5>Exercise sets</h5>
<ul><?php 
foreach($exesets as $exeset) {
	$exe_names = array_column($exeset['children'], 'Name');
	printf('<li><strong>%u. %s:</strong> %s</li>', $exeset['SetId'], $exeset['Name'], implode(', ', $exe_names));
}
?></ul>
</div>
<?php } ?>
</section>

</form>

<?php $this->endSection(); 

$this->section('top'); ?>
<?php $this->endSection(); 
