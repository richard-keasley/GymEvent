<?php $this->extend('default');
helper('html');

$this->section('content'); ?>
<h4>Outstanding updates to live version</h4>

<?php  
$dest_roots = [];
foreach($this->data['datasets'] as $dataset) { 
$dest_roots[] = $dataset['dest'];
?>
<section>
<div class="border">
From: <code><?php echo $dataset['source'];?></code><br>
To: <code><?php echo $dataset['dest'];?></code>
</div> 
<?php echo ul($dataset['log'], ['class'=>'list-unstyled']);?>
</section>
<?php 
} 
$this->endSection();

$this->section('top');
$attr = [
	'class' => "toolbar"
];
$hidden = [];
echo form_open(base_url(uri_string()), $attr, $hidden); 
echo \App\Libraries\View::back_link("setup"); 
?>
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dlgUpdate">apply</button>
<button type="submit" class="btn btn-secondary bi-arrow-clockwise" title="re-test"></button>

<div class="modal fade" id="dlgUpdate" tabindex="-1" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title" id="exampleModalLabel">Update live version</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<p><strong>Careful!</strong> This will overwrite files in the live version, synchronising it with the development version. Be sure the source version is stable before updating.</p>
	<p class="alert-light p-1">Files in the root folders (<code><?php echo implode(', ', $dest_roots);?></code>) are not updated. Update these manually if necessary; they contain some installation specific settings.</p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	<button type="submit" name="cmd" value="update" class="btn btn-primary">Update</button>
</div>
</div>
</div>
</div>
</form>
<?php 
echo form_close(); 
$this->endSection(); 