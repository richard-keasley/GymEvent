<?php 
$can_edit = \App\Libraries\Auth::check_path("admin/events/edit/{$event->id}");
if($can_edit) { ?>
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#populateForm">populate</button>
<?php }
