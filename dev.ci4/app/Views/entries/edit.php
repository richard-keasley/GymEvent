<?php $this->extend('default');

$this->section('content');
$self = sprintf('%s?%s', current_url(), http_build_query($filter));

$selector = []; $dis_options = []; $cat_options = [];
$this_cat = [];
foreach($entries as $dis) { 
	$dis_options[$dis->id] = $dis->name;
	$selector[$dis->id] = [];
	foreach($dis->cats as $cat) {
		$selector[$dis->id][] = [$cat->id, $cat->name];
		if($cat->id==$filter['catid']) $this_cat = $cat;
	}
	if($dis->id==$filter['disid']) {
		foreach($selector[$dis->id] as $row) {
			$cat_options[$row[0]] = $row[1];
		}
	}
}

# d($user_options);
# d($filter);
# d($entries);
# d($self);
?>

<form name="selector" method="GET">
<p>Select the discipline and category; press 'GET'. Edit entry names, numbers, DoBs and add new entries.</p>
<div class="row">
<div class="col-auto"><?php 
	echo form_dropdown('disid', $dis_options, $filter['disid'], 'class="form-control"');
?>
</div>
<div class="col-auto"><select class="form-control" name="catid"></select></div>
<div class="col-auto"><button type="submit" class="btn btn-primary">get</button></div>
<div class="col-auto">
	<button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#runorder">Run order</button>
	<button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#catmerge">Merge</button>
</div>
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

$inputs = [
	'category_id' => [
		'class' => 'form-control',
		'options' => $cat_options
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
	],
	'opt' => [
		'class' => 'form-control',
		'style' => 'min-width:3em;'
	]
];

$tbody = []; 
$run_inputs = [];
foreach($cat_entries as $entry) {
	$tr = [];
	foreach($inputs as $key=>$input) {
		$input['name'] = "ent{$entry->id}_$key";
		if(isset($input['options'])) {
			$input['selected'] = $entry->$key;
			$tr[] = form_dropdown($input);
		}
		else {
			$input['value'] = $entry->$key;
			$tr[] = form_input($input);
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
	$tr[] = sprintf('<div style="width:9em" class="input-group">%s</div>', implode(' ', $run_inputs));
	
	$tr[] = sprintf('<button class="btn btn-sm btn-danger bi bi-trash" type="button" onClick="delrow(this)"></button>', $entry->id);
			
	$tbody[] = $tr;
	
}

if($tbody) {
$table = \App\Views\Htm\Table::load('responsive');
$table->setHeading(['Category', 'Num', 'Club', 'Name', 'DoB', 'Opt', 'Run order', '']);
echo $table->generate($tbody); 
?>
<script>
function delrow(el) {
	var tr = el.parentElement.parentElement;
	tr.querySelectorAll('input').forEach(function(input) { 
		input.value = '#delrow';
	});
	tr.style.display = "none";
}
</script>
<?php } ?>

<div id="newrow" class="table-responsive">
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

$template = ['table_open' => '<table class="table d-none bg-light" style="min-width:50em">'];
$table = new \CodeIgniter\View\Table($template);

$table->autoHeading = false;
echo $table->generate([$tr]);
?>
<script>
function newrow(show) {
	var table = document.querySelector('#newrow table');
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

<div class="toolbar">
<?php echo \App\Libraries\View::back_link("entries/view/{$event->id}");?>
<?php if($filter['disid'] && $filter['catid']) { ?>
	<button type="submit" class="btn btn-primary">save</button>	
<?php } ?>
</div>

<?php echo form_close(); ?>

<?php
$scoreboard = new \App\ThirdParty\scoreboard;

$exeset_opts = [0 => '[none]'];
foreach($scoreboard->get_exesets() as $exeset) {
	$exeset_opts[$exeset['SetId']] = $exeset['Name'];
}
$input = [
	'name' => 'exercises',
	'type' => 'select',
	'options' => $exeset_opts,
	'selected' => $this_cat->exercises ?? 0,
	'class' => 'float-start me-1 form-control',
	'style' => 'width:12em;',
	'onChange' => "$('#exeset').submit();"
];

$attr = ['id' => "exeset"];
$hidden = ['update_exeset' => '1'];
echo form_open($self, $attr, $hidden); 
?>
<p>

<?php echo form_dropdown($input); ?>

<strong>Exercises for this category:</strong> 

<em><?php 
foreach($scoreboard->get_exesets() as $exeset) {
	if($exeset['SetId']==$exeset_id) {
		$exe_names = array_column($exeset['children'], 'Name');
		echo implode(', ', $exe_names);
	}
} ?></em>

</p>
<?php echo form_close(); ?>

<?php 
$music = $this_cat->music;
if($music) { ?>
<p><strong>Music required for this category:</strong>
<?php echo implode(' ', $music);?>.</p>
<?php } 


$this->endSection(); 

$this->section('bottom'); ?>

<div class="modal fade" id="catmerge">
<div class="modal-dialog modal-sm">
<?php 
$attr = ['class' => "modal-content"];
$hidden = ['batch' => 'catmerge'];
echo form_open($self, $attr, $hidden); 
?>
<div class="modal-header">
	<h5 class="modal-title">Merge category</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
	<p>Move <em>all</em> these entries to another category?</p>
	<p><?php
	$input = [
		'name' => 'category_id',
		'selected' => $filter['catid'],
		'options' => $cat_options,
		'class' => "form-control"
	];
	echo form_dropdown($input);
	?></p>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
	<button type="submit" class="btn btn-primary">Update</button>
</div>

<?php echo form_close();?>
</div>
</div>

<div class="modal fade" id="runorder">
<div class="modal-dialog modal-sm">
<?php 
$attr = ['class' => "modal-content"];
$hidden = ['batch'=>'runorder'];
echo form_open($self, $attr, $hidden); 
?>
<div class="modal-header">
	<h5 class="modal-title">Running order</h5>
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

 
<?php $this->endSection(); 

