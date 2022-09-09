<?php $this->extend('default');

$self = base_url(sprintf('/%s?%s', uri_string(), http_build_query($filter)));
$user_options = [];
foreach($users as $id=>$user) $user_options[$id] = $user->name;

// select which entries to show
$cat_entries = [];
$exeset_id = 0;
foreach($entries as $dis) {
	foreach($dis->cats as $cat) {
		if($cat->id===$filter['catid']) {
			$exeset_id = $cat->exercises;
			$cat_entries = $cat->entries;
		}
	}
}

$this->section('content');
# d($users);
# d($filter);
# d($entries);
# d($self);

?>

<form name="selector" method="GET">
<p>Select the discipline and category; press 'GET'. Edit entry names, numbers, DoBs and add new entries.</p>
<div class="row">
<div class="col-auto"><?php 
	$selector = []; $opts = [];
	foreach($entries as $dis) { 
		$opts[$dis->id] = $dis->name;
		$selector[$dis->id] = [];
		foreach($dis->cats as $cat) {
			$selector[$dis->id][] = [$cat->id, $cat->name];
		}
	}
	echo form_dropdown('disid', $opts, $filter['disid'], 'class="form-control"');
?>
</div>
<div class="col-auto"><select class="form-control" name="catid"></select></div>
<div class="col-auto"><button type="submit" class="btn btn-primary">get</button></div>
<div class="col-auto"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#runorder">Run order</button></div>
<div class="col-auto"><div class="p-1 border border-secondary rounded">
<?php echo getlink("/admin/entries/categories/{$event->id}?disid={$filter['disid']}", 'discipline'); ?>
</div></div>
</div>
<script>
const selector = <?php echo json_encode($selector);?>;
const filter = <?php echo json_encode($filter);?>;
let $dis_sel = null;
let $cat_sel = null;
let dis_id = 0;
let selected = '';

$(function() {
$dis_sel = $('[name=disid]');
$cat_sel = $('[name=catid]');
update_selector();
$dis_sel.change(function() { update_selector(); });
});

function update_selector(dis_id) {
	dis_id = $dis_sel.val()
	$cat_sel.find('option').remove();
	$.each(selector[dis_id], function(idx, row) {
		//console.log({idx,row});
		selected = row[0]==filter.catid ? ' selected="selected"' : '' ;
        $cat_sel.append('<option value="'+row[0]+'"'+selected+'>'+row[1]+'</option>');
	});
}
</script>
</form>

<?php 
$attr = [];
$hidden = ['save'=>1];
echo form_open($self, $attr, $hidden);

$tbody=[]; $tr = [];
$arr = empty($selector[$filter['disid']]) ? [] : $selector[$filter['disid']];
$cat_opts = [];
foreach($arr as $row) $cat_opts[$row[0]] = $row[1];

$inputs = [
	'category_id' => [
		'class' => 'form-control',
		'options' => $cat_opts
	],
	'num' => [
		'class' => 'form-control',
		'type' => 'number',
		'style' => 'min-width:5em; max-width:7em;'
	],
	'user_id' => [
		'class' => 'form-control',
		'options' => $user_options,
		'style' => "min-width:5em;"
	],
	'name' => [
		'class' => 'form-control',
		'style' => 'min-width:10em;'
	],
	'dob'=> [
		'class' => 'form-control',
		'type' => 'date'
	]
];

$run_inputs = [];
foreach($cat_entries as $entry) {		
	foreach($inputs as $key=>$input) {
		$inputs[$key]['name'] = "ent{$entry->id}_$key";
		if(isset($input['options'])) {
			$inputs[$key]['selected'] = $entry->$key;
		}
		else {
			$inputs[$key]['value'] = $entry->$key;
		}
	}
	
	foreach($entry->runorder as $key=>$val) {
		$value = $val ? $val : ''; // allow placeholder to show
		$input = [
			'class' => 'form-control',
			'value' => $value,
			'name' => "ent{$entry->id}_run_{$key}",
			'placeholder' => $key
		];
		$run_inputs[$key] = form_input($input);
	}
		
	$tbody[] = [
		form_dropdown($inputs['category_id']),
		form_input($inputs['num']),
		form_dropdown($inputs['user_id']),
		form_input($inputs['name']),
		form_input($inputs['dob']),
		sprintf('<div style="width:9em" class="input-group">%s</div>', implode(' ', $run_inputs)),
		sprintf('<button class="btn btn-sm btn-danger bi bi-trash" type="button" onClick="delrow(this)"></button>', $entry->id)
	];
}

if($tbody) {
$table = \App\Views\Htm\Table::load('responsive');
$table->setHeading(['Category', 'Num', 'Club', 'Name', 'DoB', 'Run order', '']);
echo $table->generate($tbody); ?>
<script>
function delrow(el) {
	var tr = el.parentElement.parentElement;
	tr.querySelectorAll('input').forEach(function(input) { 
		input.value = '#delrow';
	});
	tr.style.display = "none";
}
</script>
<?php } 

if($filter['disid'] && $filter['catid']) { ?>

<div id="newrow">
<button class="btn btn-success bi bi-plus-circle" type="button" onclick="newrow(1)"></button>
<?php 
$tr = [];
foreach($inputs as $key=>$input) {
	if($key!='category_id') {
		$input['name'] = "newrow_{$key}";
		$input['placeholder'] = $key;
		if(isset($input['options'])) {
			$input['selected'] = 0;
			$tr[$key] = form_dropdown($input);
		}
		else {
			$input['value'] = '';
			$tr[$key] = form_input($input);
		}
	}
}
$tr['last'] = '<button class="btn btn-danger bi bi-x-circle" type="button" onclick="newrow(0)"></button>';

$template = ['table_open' => '<table class="table d-none bg-light">'];
$table = new \CodeIgniter\View\Table($template);
$table->autoHeading = false;
echo $table->generate([$tr]);
?>
<script>
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

<p><strong>Exercise set for this category: </strong><?php 
$scoreboard = new \App\ThirdParty\scoreboard;
foreach($scoreboard->get_exesets() as $exeset) {
	if($exeset['SetId']==$exeset_id) {
		$exe_names = array_column($exeset['children'], 'Name');
		printf('%u. %s: <em>(%s)</em>', $exeset['SetId'], $exeset['Name'], implode(', ', $exe_names));
		
		# d($exeset);
	}
}
?></p>

<?php } ?>

<div class="toolbar">
<?php echo \App\Libraries\View::back_link("entries/view/{$event->id}");?>
<?php if($filter['disid'] && $filter['catid']) { ?>
	<button type="submit" class="btn btn-primary">save</button>	
<?php } ?>
</div>

<?php
echo form_close();
?>

<div class="modal fade" id="runorder">
<div class="modal-dialog modal-sm">
<?php 
$attr = [
	'class' => "modal-content"
];
$hidden = ['runorder' => 1];
echo form_open($self, $attr, $hidden); 
?>
<div class="modal-header">
	<h5 class="modal-title" id="exampleModalLabel">Running order</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<?php 
$fields = $run_inputs ?? [];
if($fields) { ?>
	<p>Set global running order parameters for this category.</p>
	<?php
	$input = [
		'class' => 'form-control'
	];
	foreach(array_keys($fields) as $key) { 
		$input['name'] = $key;
		?>
		<div class="row my-1">
		<div class="col-4 text-end"><label class="form-label">
			<?php echo $key;?>
		</label></div>
		<div class="col-8">
		<?php echo form_input($input); ?>
		</div>
		</div>
	<?php }
} else { ?>
	<p class="alert alert-warning">No running order parameters are available. Is this an empty category?"</p>
<?php } ?>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
	<button type="submit" class="btn btn-primary">Update</button>
</div>

<?php echo form_close();?>
</div>
</div>

<?php
$this->endSection(); 
