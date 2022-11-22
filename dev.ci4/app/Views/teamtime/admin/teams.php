<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;

$this->section('content'); 
$mdl_events = new \App\Models\Events;
$event = $mdl_events->find($event_id);
$title = $event->title ?? '' ;
echo "<h2>{$title}</h2>";

echo tt_lib::view_html('teams');
$this->endSection(); 

$this->section('top'); 
$attr = ['class' => "toolbar sticky-top"];
$hidden = ['reload' => 1];
echo form_open(current_url(), $attr, $hidden);
echo \App\Libraries\View::back_link('control/teamtime'); ?>
<button class="btn btn-primary bi bi-pencil" title="edit team names" type="button" data-bs-toggle="modal" data-bs-target="#pageModal"></button>
<?php 
echo getlink("admin/entries/view/{$event_id}", 'entries');
echo form_close();
$this->endSection(); 


$this->section('bottom'); ?>
<div class="modal fade" id="pageModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog">
<?php 
$attr = ['class' =>"modal-content"];
echo form_open(current_url(), $attr); ?>
<div class="modal-header">
	<h5 class="modal-title">Edit team names</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<?php 
	$value = [];
	foreach(tt_lib::get_value('teams') as $row) {
		$value[] = implode("\t", $row);
	}

	$input = [
		'class' => "form-control",
		'rows' => "20",
		'name' => "teams",
		'value' => implode("\n", $value)
	];
	echo form_textarea($input);
	?>
</div>

<div class="modal-footer">
<button class="btn me-4 btn-primary bi bi-arrow-clockwise" title="reload from event entries" type="submit" name="reload" value="1"></button>
<button class="btn btn-primary" type="submit" name="save" value="1">Save</button>
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>

<?php echo form_close(); ?>
</div>
</div>
<?php $this->endSection(); 

