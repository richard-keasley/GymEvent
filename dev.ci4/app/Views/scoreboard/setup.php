<?php $this->extend('default');

$this->section('content'); 
echo form_open();
$attrs = [
	'name' => 'html',
	'value' => $html
];
echo new \App\Views\Htm\Editor($attrs);

?>

<div class="toolbar">
<?php echo \App\Libraries\View::back_link("setup");?>
<button class="btn btn-primary">save</button>

<?php 
$attrs = [
	'class' => "nav-link",
	'title' => "scoreboard data"
];
echo anchor('setup/scoreboard/data', 'data', $attrs);

$attrs = [
	'class' => "nav-link",
	'title' => "view scoreboard"
];
echo anchor('scoreboard', 'view scoreboard', $attrs);

?>
</div>

<?php
echo form_close();
$this->endSection(); 

$this->section('bottom');?>
<script>
const link_rows = '#editform tbody tr';

$(function() {

$(link_rows).find('[name=del]').click(function() {
	var $tr = $(this).closest('tr');
	$tr.find('input').val('');
	if($(link_rows).length>1) $tr.remove();
});

$('#editform [name=add]').click(function() {
	var $tr = $(link_rows).last();
	var $clone = $tr.clone(true);
	$clone.find('input').val('');
	$tr.after($clone);
});

});

function pagesave() {
	let appvar = [];
	let datarow = [];
		
	$(link_rows).each(function() {
		datarow = [];
		$(this).find('input').each(function() {
			datarow.push(this.value);
		});
		appvar.push(datarow);
	});
	// console.log(appvar);
	$('[name=links]').val(JSON.stringify(appvar));
	$('#editform').submit();
};
</script>
<?php $this->endSection();

