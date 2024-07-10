<div class="modal" id="dlgimport" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Uploaded file</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<?php
$list = []; $import = [];
foreach($exesets as $exeset) {
	$row = [
		'name' => $exeset->name ? 
			$exeset->name : 
			'<em class="text-body-secondary">[no name]</em>',
		'event' => $exeset->event ? 
			"({$exeset->event})" : 
			'(<em class="text-body-secondary">??</em>)'
	];
	$list[] = implode(' ', $row);
	$import[] = $exeset->export();
}
# d($import);
?>
<p class="mb-0">The uploaded file contains the following entries:</p>
<ul class="list-unstyled ms-1"><?php 
	foreach($list as $li) echo "<li>{$li}</li>";
?></ul>
<p>Replace existing data with routines held in file
<code><?php echo $file->getClientname();?></code>?</p>
</div>

<div class="modal-footer">
<?php
$attrs = [
	'type' => "button",
	'class' => "btn btn-success",
	'title' => "Use this data (replace existing data)",
	'onclick' => "importdata.run()"
];
$label = '<span class="bi bi-upload"></span> use these routines';
printf('<button %s>%s</button>', stringify_attributes($attrs), $label);
?>
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
</div>

</div>
</div>
</div>

<script>
const importdata = {

modal: null,
data: <?php echo json_encode($import);?>,

run: function() {
	exesets.storage.set(importdata.data);
	localStorage.setItem('mag-exesets-idx', 0);	
	window.location.assign('<?php echo base_url('ma2/routine');?>');
},
};
$(function() {
importdata.modal = new bootstrap.Modal('#dlgimport');
importdata.modal.show();
// console.log(importdata.data);
});
</script>