<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$table->setTemplate(['table_open' => '<table class="clubent table">']);

$this->section('content'); 
#d($event->discats);

if($clubret->id) { // existing
	$action = $clubret->url('edit');
}
else { // new
	$action = $clubret->url('add');
	foreach(['name', 'address', 'phone', 'other'] as $key) $clubret->$key = '';
}

$attr = [
	'id' => "clubret"
];
echo form_open(base_url($action), $attr); ?>
<input name="save" value="1" type="hidden">
<?php 
$discats = [];
$dis_opts = [];
foreach($event->discats as $discat) {
	$dis_opts[$discat['name']] = $discat['name'];
	$cats = [];
	foreach($discat['cats'] as $key=>$cat_opts) {
		foreach($cat_opts as $cat_name) $cats[$key][$cat_name] = $cat_name;
	}		
	$discat['cats'] = $cats;
	$discats[$discat['name']] = $discat;
}

$tabs = new \App\Libraries\Ui\Tabs();

ob_start();?>
<fieldset><legend>User details</legend>
<div class="row mb-3">
	<label for="user_name" class="col-sm-2 col-form-label">Club name</label>
	<div class="col-sm-10"><?php echo form_input("user_name", $user->name, 'class="form-control"');?></div>
</div>
<div class="row mb-3">
	<label for="user_email" class="col-sm-2 col-form-label">Email</label>
	<div class="col-sm-10"><?php echo form_input("user_email", $user->email, 'class="form-control"', 'email');?></div>
</div>
</fieldset>
<fieldset><legend>Contact details</legend>
<div class="row mb-3">
	<label for="name" class="col-sm-2 col-form-label">name</label>
	<div class="col-sm-10"><?php echo form_input("name", $clubret->name, 'class="form-control"');?></div>
</div>
<div class="row mb-3">
	<label for="address" class="col-sm-2 col-form-label">address</label>
	<div class="col-sm-10"><?php echo form_textarea(['name'=>"address", 'value'=>$clubret->address, 'rows'=>"4", 'class'=>"form-control"]);?></div>
</div>
<div class="row mb-3">
	<label for="phone" class="col-sm-2 col-form-label">phone</label>
	<div class="col-sm-10"><?php echo form_input("phone", $clubret->phone, 'class="form-control"');?></div>
</div>
<div class="row mb-3">
	<label for="other" class="col-sm-2 col-form-label">other</label>
	<div class="col-sm-10"><?php echo form_textarea(['name'=>"other", 'value'=>$clubret->other, 'class'=>"form-control", 'rows'=>5]);?></div>
</div>
</fieldset>
<?php 
$tabs->set_item('Club details', ob_get_clean(), 'club');

if(!empty($event->staffcats[0])) {
ob_start();?>
<div id="staff">
<p>Staff details should be entered as: <code>Name1, Name2, BG number, <abbr title="Date of birth as dd/mm/yy">DoB</abbr></code>. Each piece of information is separated by a comma. Place place each staff member in a separate box. Try copying and pasting the information directly from <a href="https://www.british-gymnastics.org/gymnet/clubs/members" target="bg">BG GymNet</a>.</p>
<div class="table-responsive">
<?php
$staff = $clubret->staff;
#d($staff);
if(!$staff) { // provide one blank entry 
	$staff = [[
		'cat' => '', 'name' => ''
	]];
}

$options = [];
foreach($event->staffcats as $val) $options[$val] = $val;
$inputs = [
	'cat' => [
		'options' => $options, 
		'class' => 'form-control',
		'data-field' => 'cat'
	],
	'name' => [
		'class' => 'form-control',
		'data-field' => 'name',
		'style' => 'min-width:20em;'
	]
];

$tbody = []; 
foreach($staff as $rowkey=>$row) {
	$inputs['cat']['selected'] = $row['cat'];
	$namestring = new \App\Entities\namestring($row['name']);
	$inputs['name']['value'] = $namestring->csv;
	$tbody[] = [
		'#' => $rowkey + 1, 
		'cat' => form_dropdown($inputs['cat']),
		'name' => form_input($inputs['name']),
		'del' => '<button name="del" type="button" class="btn bi-trash btn-danger btn-sm"></button>'
	];
}

$table->setHeading(['','category','name','']);
echo $table->generate($tbody);
echo form_hidden('staff', json_encode($staff));
?>
</div>
<button name="add" type="button" class="btn btn-success bi-person-plus-fill"></button>
<?php echo $clubret->errors('staff'); ?>
</div>
<?php 
$tabs->set_item('Staff', ob_get_clean(), 'staff');
} 

if($event->discats) {
ob_start();	?>
<div id="participants">
<p>Gymnasts' details should be entered as: <code>Name1, Name2, BG number, <abbr title="Date of birth as dd/mm/yy">DoB</abbr></code>. Each piece of information is separated by a comma.<br>Place each gymnast on one separate line. If your entry comprises multiple gymnasts (e.g. Acro and Team-gym), place all gymnasts in that entry in the same box.<br>Try copying and pasting the information directly from <a href="https://www.british-gymnastics.org/gymnet/clubs/members" target="bg">BG GymNet</a>.</p>
<div class="table-responsive">
<?php
$tbody = []; $tr = []; 
$participants = $clubret->participants;
if(!$participants) { // provide one blank participant 
	$participants = [[
		'dis'=>'', 'cat' => [], 'team'=>'', 'names' => [] 
	]];
}
$inputs = [
	'dis' => [
		'options' => $dis_opts,
		'class' => 'form-control',
		'data-field' => 'dis',
		'style' => 'min-width:4em;'
	],
	'cat' => [
		'class' => 'form-control',
		'data-field' => 'cat',
		'style' => 'min-width:4em;'
	],
	'team' => [
		'class' => 'form-control',
		'data-field' => 'team',
		'style' => 'min-width:8em;'
	],
	'names' => [
		'class' => 'form-control',
		'data-field' => 'names',
		'style' => 'min-width:20em;',
		'cols' => 30,
		'rows' => 1
	]
];

foreach($participants as $rowkey=>$row) {
	$inputs['dis']['selected'] = $row['dis'];
	$inputs['team']['value'] = $row['team'];
	$inputs['names']['value'] = implode("\n", $row['names']);
			
	$tr[0] = $rowkey+1;
	
	$tr['discat'] = '<div class="input-group">';
	$tr['discat'] .= form_dropdown($inputs['dis']);
	foreach($discats as $discat) {
		$inputs['cat']['data-dis'] = $discat['name'];
		foreach($discat['cats'] as $cat_key=>$options) {
			$inputs['cat']['options'] = $options;
			$inputs['cat']['selected'] = isset($row['cat'][$cat_key]) ? $row['cat'][$cat_key] : '-' ;
			$tr['discat'] .= form_dropdown($inputs['cat']);
		}
	}
	$tr['discat'] .= '</div>';
	
	$tr['team'] = form_input($inputs['team']);
	$tr['names'] = form_textarea($inputs['names']); 

	$tr['del'] = '<button name="del" type="button" class="btn bi-trash btn-danger btn-sm"></button>';
	$tbody[] = $tr;	
}

$table->setHeading(['','category','','names','']);
echo $table->generate($tbody);
echo form_hidden('participants', json_encode($participants));
?>
</div>
<button name="add" type="button" class="btn btn-success bi-person-plus-fill"></button>
<?php echo $clubret->errors('participants');?>
</div>
<?php
$tabs->set_item('Participants', ob_get_clean(), 'participants');

} 

if(1) {
ob_start(); ?>
<div><?php echo $event->payment;?></div>
<?php echo $clubret->fees('htm');?>
<p><strong>NB:</strong> Save any changes to update the fees calculation.</p>
<?php 
$tabs->set_item('Payment', ob_get_clean(), 'payment');
} 

echo $tabs->htm();

?>

<div class="toolbar">
<?php 
// href dependant on new or existing record
$back_link = $clubret->id ?
	$clubret->url('view') : 
	"events/view/{$event->id}" ;
echo \App\Libraries\View::back_link($back_link);
?>
<button name="cmd" class="btn btn-primary" value="save" type="button">save</button> 
</div>

</form>
<script>
let partrows = '#participants .clubent tbody tr';
let staffrows = '#staff .clubent tbody tr';

$(function() {

$(partrows).find('[data-field=dis]').change(function() { update_partrows(); });
$(partrows).find('[name=del]').click(function(){
	if($(partrows).length<2) return;
	$(this).closest('tr').remove();
});
$(staffrows).find('[name=del]').click(function(){
	if($(staffrows).length<2) return;
	$(this).closest('tr').remove();
});
$('#participants [name=add]').click(function() {
	var $tr = $(partrows).last();
	var $clone = $tr.clone(true);
	$clone.find('input').val('');
	$clone.find('textarea').val('');
	$tr.after($clone);
});
$('#staff [name=add]').click(function() {
	var $tr = $(staffrows).last();
	var $clone = $tr.clone(true);
	$clone.find('input').val('');
	$clone.find('textarea').val('');
	$tr.after($clone);
});

$('[name=cmd]').click(function() {
	var participants = [];
	$(partrows).each(function() {
		//console.log(this);
		var dis = $(this).find('[data-field=dis]').val();
		var cat = [];
		$(this).find('[data-field=cat][data-dis='+dis+']').each(function() {
			cat.push($(this).val());
		});
		participants.push({
			dis: dis,
			cat: cat,
			team: $(this).find('[data-field=team]').val(), 
			names: $(this).find('[data-field=names]').val().split("\n")
		});
	});
	$('[name=participants]').val(JSON.stringify(participants));
	
	var staff = [];
	$(staffrows).each(function() {
		staff.push({
			cat: $(this).find('[data-field=cat]').val(),
			name: $(this).find('[data-field=name]').val()
		});
	});
	$('[name=staff]').val(JSON.stringify(staff));
	//console.log({staff, participants});
	$('#clubret').submit();
});

update_partrows();

});

const discats = <?php echo json_encode($discats);?>;
function update_partrows() {
	//console.log(partrows);
	$(partrows).each(function() {
		//console.log(this);
		
		var dis = $(this).find('[data-field=dis]').val();
			
		$(this).find('[data-field=cat]').each(function() {
			if($(this).attr('data-dis')==dis) $(this).show();
			else $(this).hide();
		});
			
		var n = parseInt(discats[dis]['inf'].n);
		if(isNaN(n) || n<1) n = 1;
		this.querySelector('[data-field=names]').rows = n;
				
		var team = parseInt(discats[dis]['inf'].team);
		if(isNaN(team)) team = 0;
		if(team) $(this).find('[data-field=team]').show();
		else $(this).find('[data-field=team]').hide();
	});
}
</script>

<?php $this->endSection();
