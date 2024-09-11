<?php 
$can_edit = \App\Libraries\Auth::check_path("admin/events/edit/{$event->id}");
if(!$can_edit) return '';

$ok = $event->clubrets==2 & empty($event->deleted); 

?>
<div class="modal fade" id="populateForm" tabindex="-1"  aria-hidden="true">
<div class="modal-dialog">

<?php 
$attr = [
	'class' => "modal-content"
];
echo form_open(current_url(), $attr); ?>
<input type="hidden" name="populate" value="1">
<div class="modal-header">
	<h5 class="modal-title">Create entries for <?php echo $event->title;?></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<p>This clears all existing entries from the event and re-populates the entries from the club returns.
	Population of the event should happen once all club returns are complete (i.e. no more returns will be accepted).</p>
	<p>Any "post-population" edits to the entries (e.g. category name, exercises, music) will be deleted.</p>

	<?php if(!$ok) { ?>	
	<p class="alert alert-danger"><span class="bi bi-exclamation-triangle-fill"></span> Re-population can only be carried out on <strong>listed</strong> events with completed club returns (i.e. Event state 'clubrets' marked as 'view'). Ensure these are correctly set before populating the event's entries from club returns.</p>
	<?php } ?>	
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
	<?php if($ok) { ?>
	<button type="submit" class="btn btn-primary">Re-populate</button>
	<?php } ?>
</div>
</form>
</div>
</div>
