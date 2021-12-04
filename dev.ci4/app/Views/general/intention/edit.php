<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();

$this->section('content');
$attr = [
	'id' => "editform"
];
echo form_open(base_url(uri_string()), $attr);
echo form_hidden('routine', $intention->encode()); ?>

<header class="row">
<div class="col-auto">
<div class="input-group my-1">
  <label class="input-group-text">Name</label>
  <input type="text" class="form-control" placeholder="Gymnast name" data-name="name" value="<?php echo $intention->name;?>">
</div>
</div>

<div class="col-auto">
<div class="input-group my-1">
	<label class="input-group-text">Gender</label>
	<?php 
	$input = [
		'data-name' => 'gender',
		'class' => 'form-control',
		'options' => ['male'=>'Male', 'female'=>'Female'],
		'selected' => $intention->gender
	];
	echo form_dropdown($input);
	?>
</div>
<div class="input-group my-1">
	<label class="input-group-text">DoB</label>
	<input type="date" class="form-control" placeholder="date of birth" data-name="dob" value="<?php echo $intention->dob;?>">
</div>
</div>

<div class="col-auto">
<div class="input-group my-1">
	<label class="input-group-text">Exercise</label>
	<?php 
	$input = [
		'data-name' => 'exercise',
		'class' => 'form-control',
		'options' => ['FX'=>'Floor'],
		'selected' => $intention->exercise
	];
	echo form_dropdown($input);
	?>
</div>
<div class="input-group my-1">
	<label class="input-group-text">level</label>
	<?php 
	$input = [
		'data-name' => 'level',
		'class' => 'form-control',
		'options' => ['novice'=>'Novice','intermediate'=>'Intermediate','advanced'=>'Advanced','bronze'=>'Bronze','silver'=>'Silver','gold'=>'Gold'],
		'selected' => $intention->level
	];
	echo form_dropdown($input);
	?>
</div>
</div>

<div class="col-auto">
<?php echo view('general/intention/version'); ?>
</div>
</header>

<section id="routine" class="table-responsive">
<?php 
$inputs = [
	'skills' => [
		'data-name' => 'skills',
		'type' => 'hidden'
	],
	'specials' => [
		'data-name' => 'specials',
		'options' => $intention->rules->specials->options(),
		'class' => 'form-control'
	],
	'bonuses' => [
		'data-name' => 'bonuses',
		'options' => $intention->rules->bonuses->options(),
		'class' => 'form-control'
	],
];

$tbody = []; $tr = [];
foreach($intention->skills as $sk_num=>$sk_id) {
	$skill = $intention->rules->skills->get($sk_id);
	$inputs['skills']['value'] = $sk_id;
	$inputs['specials']['selected'] = strval($intention->specials[$sk_num]);
	$inputs['bonuses']['selected'] = strval($intention->bonuses[$sk_num]);
	foreach(['description','group','difficulty'] as $key) {
		$tr[$key] = sprintf('<button type="button" class="p-0 w-100 btn btn-light text-start eledit" style="min-height:2em;" title="click to change this skill" data-name="%s" data-row="%u">%s</button>', $key, $sk_num, $skill[$key]);
	}
	foreach(['specials','bonuses'] as $key) {
		$tr[$key] = form_dropdown($inputs[$key]);
	}
	$tr[$key] .= form_input($inputs['skills']);
	$tbody[] = $tr;
} 

$template = ['table_open' => '<table class="table" style="min-width:40em;">'];
$table->setTemplate($template);
$table->setHeading(['Skill','<abbr title="group">Grp</abbr>','<abbr title="difficulty">Dif</abbr>','<abbr title="special requirements">SRs</abbr>','Bonus']);
echo $table->generate($tbody);
?>
</section>

<div class="toolbar">
<button class="btn btn-primary bi bi-printer" title="print this routine intention" type="submit" name="view" value="print"> print</button>
<button class="btn btn-primary bi bi-journal-arrow-down" title="save this routine to your computer so it can be altered later" type="submit" name="view" value="store"> save</button>
<button class="btn btn-primary bi bi-journal-plus" title="make a copy of this routine to use on another gymnast" type="button" name="clone"> clone</button>
<button class="btn btn-primary bi bi-journal-check" title="re-check this routine after edits" type="submit"> update</button>
</div>
</form>
<?php #d($intention);
$this->endSection(); 

$this->section('bottom');?>
<div class="modal fade" id="skillModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel">Select skill</h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	</div>
	<div class="modal-body">
	
	<?php 
	$grouped = $intention->rules->skills->get_grouped();
	$acc = new \App\Libraries\Ui\Accordion;
	
	$tab_data = [];
	foreach($grouped as $grp_id=>$group) {
		ob_start();

		/* start difficulty accordion */
		echo $acc->start("acc-elg{$grp_id}");
		foreach($group as $dif=>$skills) { 
			echo $acc->item_start($dif); ?>
			<div class="d-grid"><?php 
			foreach($skills as $sk_id=>$skill) {
				$attribs = [];
				foreach(\App\Libraries\General\Skills::attributes as $attr) {
					if($skill[$attr]) $attribs[] = sprintf('<span class="badge bg-info">%s</span>', $attr);
				}
				printf('<button data-bs-dismiss="modal" class="btn text-start" name="sk_id" value="%u" type="button">%s %s</button>', $sk_id, $skill['description'], implode(' ', $attribs));
			}
			?></div>
			<?php
		}
		echo $acc->end();
		/* end difficulty accordion */
	
		$tab_data[$grp_id] = [
			'heading' => "Group {$grp_id}",
			'content' => ob_get_clean()
		];
	}
	$tabs = new \App\Libraries\Ui\T2($tab_data, 'elg');
	echo $tabs->htm();
	?>
	
	</div>
	<div class="modal-footer">
	<button data-bs-dismiss="modal" class="btn btn-danger bi bi-file-x" name="sk_id" value="0" type="button" title="Remove this skill from the routine"></button>
	<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
	</div>
</div>
</div>
</div>

<script>
$(function() {

const skills = <?php 
/* use a blank line when no skill selected */
$list = $intention->rules->skills->list;
$list[0] = \App\Libraries\General\Skills::blank;
echo json_encode($list);?>;
const skillModal = new bootstrap.Modal(document.getElementById('skillModal'));
let sk_num = 0;

$('#routine [data-name=specials]').change(function() {
	var $new_select = $(this);
	var new_val = $new_select.val();
	if(new_val!='0') {
		$('#routine [data-name=specials]').each(function() {
			var $test_select = $(this);
			if($test_select.val()==new_val) $test_select.val(0);
		
		});
		$new_select.val(new_val);
	}
});

$('#routine [data-name=bonuses]').change(function() {
	var $new_select = $(this);
	var new_val = $new_select.val();
	if(new_val!='0') {
		$('#routine [data-name=bonuses]').each(function() {
			var $test_select = $(this);
			if($test_select.val()==new_val) $test_select.val(0);
		
		});
		$new_select.val(new_val);
	}
});

$('#routine .eledit').click(function() {
	$('#skillModal button[name=sk_id]').show();
	$('#routine [data-name=skills]').each(function() {
		var sk_id = this.value;
		if(sk_id!='0') {
			$('#skillModal button[name=sk_id][value='+sk_id+']').hide();
		}
	});
	sk_num = this.dataset.row;
	skillModal.show();
});
	
$('#skillModal button[name=sk_id]').click(function() {
	var sk_id = this.value;
	//console.log(sk_id, skills);
	var skill = skills[sk_id];
	var tr = $('#routine table tbody tr')[sk_num];
	tr.querySelector('[data-name=skills]').value = sk_id;
	tr.querySelector('[data-name=description]').innerHTML = skill['description'];
	tr.querySelector('[data-name=group]').innerHTML = skill['group'];
	tr.querySelector('[data-name=difficulty]').innerHTML = skill['difficulty'];
});

$('#editform button[name=clone]').click(function() {
	var form = $('#editform')[0];
	var name_field = $('#editform [data-name=name]');
	var name = name_field.val();
	form.target = '_blank';
	name_field.val('copied');
	$('#editform').submit();
	form.target = '_self';
	name_field.val(name);
	$('#editform').submit();
});

$('#editform header select').change(function() {
	$('#editform').submit();
});

$('#editform').submit(function(e) { 
	var routine = {};
	<?php 
	$str_keys = [];
	$arr_keys = [];
	foreach(\App\Libraries\General\Intention::filter as $key=>$filter) {
		if($filter['flags'] & FILTER_FORCE_ARRAY) $arr_keys[] = $key;
		else $str_keys[] = $key;
	}
	?> 
	var keys = <?php echo json_encode($str_keys);?>;
	keys.forEach(function(key) {
		routine[key] = $('#editform [data-name='+key+']').val();
	});
	var keys = <?php echo json_encode($arr_keys);?>;
	keys.forEach(function(key) {
		routine[key] = [];
		$('#editform [data-name='+key+']').each(function() {
			routine[key].push(this.value);
		});
	});
	$('#editform [name=routine]').val(JSON.stringify(routine));
	
	//console.log(routine);
	//e.preventDefault();
});

let navGroups = localStorage.getItem('navGroups');
if(navGroups) {
	try {
		var el = document.getElementById(navGroups);
		var tab = new bootstrap.Tab(el);
		tab.show();
	}
	catch(ex) {
		console.error(ex);
	}
}

$('#navGroups [data-bs-toggle=tab]').on('click', function(e) {
	localStorage.setItem('navGroups', e.target.id);
});	

});
</script>

<?php 
if(\App\Libraries\Auth::check_role('superuser')) d($intention);
echo view('general/intention/sv_table', ['intention'=>$intention]); ?>

<?php $this->endSection(); 
