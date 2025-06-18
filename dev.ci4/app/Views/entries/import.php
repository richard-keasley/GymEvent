<?php $this->extend('default');

$this->section('content');
	
echo form_open();
?>
<div class="toolbar"><?php 
echo \App\Libraries\View::back_link("admin/entries/view/{$event->id}");
echo $delsure->button('csv');
?></div>

<ul class="list-unstyled alert alert-info">
<li>Ensure dataset is sorted by discipline, category.</li>
<li>Use columns: <code><?php echo implode(', ', $columns);?></code>.</li>
<li>First row of dataset (column headings) is ignored.</li>
</ul>
<p class="alert alert-danger">Warning: Importing deletes all existing entries from this event.</p>

<textarea name="csv" class="form-control" style="height:20em;" form="<?php echo $delsure->id;?>"></textarea>


<?php echo form_close();

# d($import);
# d($entries);
# d($event);

$this->endSection();

$this->section('bottom');
echo $delsure->form();
$this->endSection();
