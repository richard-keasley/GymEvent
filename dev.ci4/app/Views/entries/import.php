<?php $this->extend('default');

$this->section('content');
	
echo form_open(current_url());
?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link("admin/entries/view/{$event->id}"); ?>
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_delete" title="Import entries"><span class="bi bi-file-arrow-down"></span> Import</button>
</div>

<ul class="alert alert-info">
<li>Ensure dataset is sorted by discipline, category.</li>
<li>Use columns: <code><?php echo implode(', ', $columns);?></code>.</li>
<li>First row of dataset (column headings) is ignored.</li>
</ul>
<p class="alert alert-danger">Warning: Importing deletes all existing entries from this event.</p>

<textarea name="csv" class="form-control" style="height:20em;"></textarea>


<div id="modal_delete" class="modal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Import entries</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<p>Importing deletes all existing entries for this event. Are you sure you want to do this?</p>
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary bi-x-circle-fill" data-bs-dismiss="modal" title="cancel"></button>
<button type="submit" class="btn btn-danger bi-file-arrow-down" title="proceed"></button>
</div>

</div></div></div>

<?php echo form_close();

# d($import);
# d($entries);
# d($event);

$this->endSection(); 
