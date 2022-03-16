<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);

$this->section('content');
#d($users);
#d($filter, $entries);

$user_options = [];
foreach($users as $id=>$user) $user_options[$id] = $user->name;
?>

<form name="selector" method="GET">
<p>edit the names, number of entries, add new entries</p>
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
echo form_open(base_url(uri_string()), $attr, $hidden);

$filter_cat = $filter['catid'];
$cat_entries = [];
foreach($entries as $dis) {
	if($dis->id==$filter['disid']) {
		foreach($dis->cats as $cat) {
			if($cat->id===$filter_cat) {
				$cat_entries = $cat->entries;
			}
		}
	}
}
$flds = ['category_id','num','name','dob','club',''];
$table->setHeading($flds);
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
		'style' => 'max-width: 7em;'

	],
	'name' => [
		'class' => 'form-control'
	],
	'dob'=> [
		'class' => 'form-control',
		'type' => 'date'
	],
	'user_id' => [
		'class' => 'form-control',
		'options' => $user_options			
	]
];

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
	$tbody[] = [
		form_dropdown($inputs['category_id']),
		form_input($inputs['num']),
		form_input($inputs['name']),
		form_input($inputs['dob']),
		form_dropdown($inputs['user_id']),
		sprintf('<button class="btn btn-danger bi bi-trash" type="submit" name="delrow" value="%u"></button>', $entry->id)


	];
}
if($tbody) echo $table->generate($tbody);
?>

<?php if($filter['disid'] && $filter['catid']) { ?>

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
$table->setTemplate($template);
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

<?php } ?>

<div class="toolbar">
<?php echo \App\Libraries\View::back_link("entries/view/{$event->id}");?>
<?php if($filter['disid'] && $filter['catid']) { ?>
	<button type="submit" class="btn btn-primary">save</button>	
<?php } ?>
</div>

<?php
echo form_close();
$this->endSection(); 
