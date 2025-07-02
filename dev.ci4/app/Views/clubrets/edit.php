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
$attrs = ['id' => "clubret"];
$hidden = ['save' => "1"];
echo form_open($action, $attrs, $hidden);

$tabs = new \App\Views\Htm\Tabs();

ob_start();
include __DIR__ . '/edit-club.php';
$tabs->set_item('Club details', ob_get_clean(), 'club');

if(!empty($event->staffcats[0])) {
	ob_start();
	include __DIR__ . '/edit-staff.php';
	$tabs->set_item('Staff', ob_get_clean(), 'staff');
} 

if($event->discats) {
	ob_start();	
	include __DIR__ . '/edit-participants.php';
	$tabs->set_item('Participants', ob_get_clean(), 'participants');
} 

ob_start();
include __DIR__ . '/edit-payment.php';
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

<button class="btn btn-primary" value="save" type="button" onclick="editform.submit()">save</button> 

</div>

<script>
$(function() {

$('[name=user_name]').focus(function() {
	var hidden = this.parentElement.querySelector('.d-none');
	if(hidden) hidden.classList.remove("d-none");
});

editform.partrows().find('[data-field=dis]').change(function() { 
	editform.init(); 
});

editform.init();

});

const editform = {

partrows: function() {
	return $('#participants .clubent tbody tr');
},
staffrows: function() {
	return $('#staff .clubent tbody tr');
},
discats: <?php echo json_encode($discats);?>,

init: function() {
	editform.partrows().each(function() {
		// console.log(this);
		
		var dis = $(this).find('[data-field=dis]').val();
			
		$(this).find('[data-field=cat]').each(function() {
			if($(this).attr('data-dis')==dis) $(this).show();
			else $(this).hide();
		});
		
		$(this).find('[data-field=opt]').each(function() {
			if($(this).attr('data-dis')==dis) $(this).show();
			else $(this).hide();
		});
			
		var n = parseInt(editform.discats[dis]['inf'].n);
		if(isNaN(n) || n<1) n = 1;
		this.querySelector('[data-field=names]').rows = n;
				
		var team = parseInt(editform.discats[dis]['inf'].team);
		if(isNaN(team)) team = 0;
		if(team) $(this).find('[data-field=team]').show();
		else $(this).find('[data-field=team]').hide();	
	});
},

addstaff: function() {
	var $tr = editform.staffrows().last();
	var $clone = $tr.clone(true);
	$clone.find('input').val('');
	$clone.find('textarea').val('');
	$clone.find('.pid').html('<span class="fw-bold text-success bi bi-plus-circle"></span>');
	$tr.after($clone);
},

addpart: function() {
	var $tr = editform.partrows().last();
	var $clone = $tr.clone(true);
	$clone.find('input').val('');
	$clone.find('textarea').val('');
	$clone.find('.pid').html('<span class="fw-bold text-success bi bi-plus-circle"></span>');
	$tr.after($clone);
},

delstaff: function(btn) {
	if(editform.staffrows().length<2) return;
	$(btn).closest('tr').remove();
},

delpart: function(btn) {
	if(editform.partrows().length<2) return;
	$(btn).closest('tr').remove();
},

submit: function() {
	var participants = [];
	editform.partrows().each(function() {
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
	editform.staffrows().each(function() {
		staff.push({
			cat: $(this).find('[data-field=cat]').val(),
			name: $(this).find('[data-field=name]').val()
		});
	});

	$('[name=staff]').val(JSON.stringify(staff));
	//console.log({staff, participants});
	$('#clubret').submit();
},
	
};

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
