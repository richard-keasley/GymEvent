<?php $this->extend('default');

$this->section('content'); ?>

<p>Scoreboard needs a lot of tidying up.</p>
<ul>
<li>App variable 'scoreboard.links' is no longer needed (use home.links instead)</li>
<li>Scoreboard documentation is not needed (ie. how to set-up score displays)</li>
<li>The data pages (below) are not changing when different tables are selected</li>
<li>Remove shortcut ~/follow (routes)</li>
<li>Upate QR code for "~/follow" to "~/x/follow"</li>
</ul>
<p>Scoreboard needs to be reduced to a simple documentation page. Maybe make it editable from UI.</p>

<div class="toolbar">
	<?php echo \App\Libraries\View::back_link("setup");?>
	<?php 
	$attr = [
		'class' => "nav-link",
		'title' =>"view scoreboard"
	];
	echo anchor('scoreboard', 'view', $attr);
	$attr['title'] = "View scoreboard data";
	echo anchor('setup/scoreboard/data', 'data', $attr);
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

