<?php $this->extend('default'); 
$table = \App\Views\Htm\Table::load('responsive');

$this->section('content');
#  d($event->discats);

// namestring textarea
$names_edit = [
	'class' => 'form-control',
	'data-field' => 'names',
	'style' => 'min-width:22em;white-space:pre;',
	'cols' => 30,
	'rows' => 1
];

if($clubret->id) { // existing
	$action = $clubret->url('edit');
}
else { // new
	$action = $clubret->url('add');
	foreach(['name', 'address', 'phone', 'other'] as $key) $clubret->$key = '';
}// discats for this view$discats = [];$dis_opts = [];foreach($event->discats as $discat) {	$key = $discat['name'];		$dis_opts[$key] = $discat['name'];		$cats = [];	foreach($discat['cats'] as $cat_key=>$cat_opts) {		foreach($cat_opts as $cat_name) {			$cats[$cat_key][$cat_name] = humanize($cat_name);		}	}			$discat['cats'] = $cats;		$discat['options'] = [];	foreach($discat['opts'] as $val) $discat['options'][$val] = humanize($val);		$discats[$key] = $discat;		# d($discat);}
$attr = ['id' => "clubret"];
$hidden = ['save' => "1"];
echo form_open($action, $attr, $hidden);

$tabs = new \App\Views\Htm\Tabs();

ob_start();?>
<fieldset><legend>User details</legend>
<div class="row mb-3">
	<label for="user_name" class="col-sm-2 col-form-label">Club name</label>
	<div class="col-sm-10">
	<?php echo form_input("user_name", $user->name, 'class="form-control"');?>
	<p class="d-none alert alert-warning m-0"><small>Careful if you change this; it is your username when you login. You may need to update your browser's password manager as well.</small></p>
	</div>
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
	<div class="col-sm-10"><?php 
	$input = [
		'name' => "phone",
		'value' => $clubret->phone,
		'class' => "form-control",
		'type' => 'tel'
	];
	echo form_input($input);
	?></div>
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
<p>Staff details should be entered as: <?php echo \App\Libraries\Namestring::hint;?>. Each piece of information is separated by a comma. Each staff member should be entered in a separate box.</p>
<?php echo $event->staff;

$staff = $clubret->staff;
# d($staff);
if(!$staff) { // provide one blank entry 
	$staff = [[
		'cat' => '', 'name' => ''
	]];
}

$options = [];
foreach($event->staffcats as $val) $options[$val] = humanize($val);

$inputs = [
	'cat' => [
		'options' => $options, 
		'class' => 'form-control'
	],
	'name' => $names_edit
];
foreach(array_keys($inputs) as $key) {
	$inputs[$key]['data-field'] = $key;
}

$tbody = []; 
foreach($staff as $rowkey=>$row) {
	$inputs['cat']['selected'] = $row['cat'];
	if($row['name']) {
		$namestring = new \App\Libraries\Namestring($row['name']);
		$inputs['name']['value'] = (string) $namestring;
	}
	else $inputs['name']['value'] = '' ;

	$tbody[] = [
		'#' => $rowkey + 1, 
		'cat' => form_dropdown($inputs['cat']),
		'name' => form_input($inputs['name']),
		'del' => '<button name="del" type="button" class="btn bi-trash btn-danger btn-sm"></button>'
	];
}

$table->setHeading(['', 'category', 'name', '']);

printf('<div class="clubent">%s</div>', $table->generate($tbody));
echo form_hidden('staff', json_encode($staff));
?>
<button name="add" type="button" class="btn btn-success bi-person-plus-fill"></button>
<?php echo $clubret->errors('staff'); ?>
</div>

<?php if($event->stafffee) { ?>
<p class="mt-2 form-check">
<?php 
$input = [
	'class' => "form-check-input",
	'name' => "stafffee",
	'type' => "checkbox",
	'value' => "1",
	'id' => "chkstafffee"
];
if($clubret->stafffee) $input['checked'] = "checked";
echo form_input($input);
?>
<label class="form-check-label" for="chkstafffee">This club will meet all staff requirements for this event (&pound;<?php echo $event->stafffee;?> will be added to your entry fee as "staff" if not).</label>
</p>
<?php } 

$tabs->set_item('Staff', ob_get_clean(), 'staff');
} 

if($event->discats) {
/* participants tab */
ob_start();	?>
<div id="participants">
<ul>
<li>Gymnasts' details are entered as: <?php echo \App\Libraries\Namestring::hint;?>. Each piece of information is separated by a comma.</li>
<li>Each gymnast occupies a separate line.</li>
<li>Each entry is in a separate box.</li>
<li>If an entry comprises more than one gymnast (e.g. Acro and Team-gym), enter each gymnast on a separate line <em>within the same box</em>.</li>
</ul>
<?php 
echo $event->participants; 

$tbody = []; $tr = []; 
$participants = $clubret->participants;
if(!$participants) { // provide one blank participant 
	$participants = [[
		'dis'=>'', 'cat'=>[], 'team'=>'', 'names'=>[] 
	]];
}

$inputs = [
	'dis' => [
		'options' => $dis_opts,
		'class' => 'form-control',
		'style' => 'min-width:4em;'
	],
	'cat' => [
		'class' => 'form-control',
		'style' => 'min-width:4em;'
	],
	'team' => [
		'class' => 'form-control',
		'placeholder' => 'Team name',
		'style' => 'min-width:8em;'
	],
	'names' => $names_edit,
	'opt' => [
		'class' => 'form-control',
		'style' => 'min-width:5em;'
	]
];

foreach(array_keys($inputs) as $key) {
	$inputs[$key]['data-field'] = $key;
}

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
			$inputs['cat']['selected'] = $row['cat'][$cat_key] ?? '-' ;
			$tr['discat'] .= form_dropdown($inputs['cat']);
		}
	}
	$tr['discat'] .= '</div>';
	
	$tr['team'] = form_input($inputs['team']);
	$tr['names'] = form_textarea($inputs['names']); 
	$tr['opt'] = '';

	$input = $inputs['opt'];
	foreach($discats as $discat) {
		$input['data-dis'] = $discat['name'];
		$input['options'] = $discat['options'];
		$input['selected'] = $row['opt'] ?? '';
		if($input['options']) $tr['opt'] .= form_dropdown($input);
	}

	$tr['del'] = '<button name="del" type="button" class="btn bi-trash btn-danger btn-sm"></button>';
	$tbody[] = $tr;	
}

$table->setHeading(['','category','',"Gymnasts' details",'']);
printf('<div class="clubent">%s</div>', $table->generate($tbody));
echo form_hidden('participants', json_encode($participants));

?>
<button name="add" type="button" class="btn btn-success bi-person-plus-fill"></button>
<?php 

echo $clubret->errors('participants', 6);

echo $this->include('events/_terms');

?>
</div>
<?php

$tabs->set_item('Participants', ob_get_clean(), 'participants');
} 


/* payment tab */
ob_start();

if($event->dates['clubrets_closes']) { 
	$dt_closes = new \datetime($event->dates['clubrets_closes']);
	$dt_now = new \datetime();
	$past = $dt_closes <= $dt_now;
	if($past) {
		$class = 'alert alert-warning';
		$datestring = 'as soon as possible';
	} else {
		$class = '';
		$datestring = 'by ' . $dt_closes->format('l j F');
	}
	$format = '<p class="%s">All entries must be completed <strong>%s</strong>.</p>';
	printf($format, $class, $datestring);
}

?>
<div><?php echo $event->payment;?></div>
<?php echo $clubret->fees('htm');?>
<p><strong>NB:</strong> Save any changes to update the fees calculation.</p>
<?php 
$tabs->set_item('Payment', ob_get_clean(), 'payment');


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

<script>
let partrows = '#participants .clubent tbody tr';
let staffrows = '#staff .clubent tbody tr';

$(function() {

$('[name=user_name]').focus(function() {
	var hidden = this.parentElement.querySelector('.d-none');
	if(hidden) hidden.classList.remove("d-none");
});

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
		var opt = $(this).find('[data-field=opt][data-dis='+dis+']').val();
		if(typeof opt =='undefined') opt = '';

		participants.push({
			dis: dis,
			cat: cat,
			opt: opt,
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
		
		$(this).find('[data-field=opt]').each(function() {
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

<?php 
echo form_close();
$this->endSection();

$this->section('bottom');
if($event->terms && !$clubret->terms) {
	// show nag-screen every page load
	echo $this->include('events/_terms_modal');
}
$this->endSection();
