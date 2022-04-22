<?php $this->extend('default');

$this->section('content');

$format = $format ?? 'plain' ;
$can_edit = \App\Libraries\Auth::check_path('admin/entries/edit');
if(!$can_edit) $format = 'plain';

if($can_edit) {
	$attr = [
		'class' => "toolbar nav sticky-top"
	];
	echo form_open(base_url(uri_string()), $attr);
	echo \App\Libraries\View::back_link("admin/events/view/{$event->id}");
	echo getlink("admin/entries/edit/{$event->id}", 'edit');
	foreach(['plain','full','dob'] as $link_format) {
		if($format!=$link_format) {
			echo getlink("admin/entries/view/{$event->id}/{$link_format}", $link_format);
		}
	}
	echo getlink("admin/entries/categories/{$event->id}", 'categories');
	echo getlink("admin/entries/clubs/{$event->id}", 'clubs');
	echo getlink("admin/entries/import/{$event->id}", 'import');
	echo getlink("admin/entries/export/{$event->id}", 'export');
	?>
 	<input type="hidden" name="renumber" value="0">
	<button class="btn btn-primary" name="chk_renumber" value="1" type="button">Renumber</button>
	<script>
	$('[name=chk_renumber]').click(function(){
		if(!confirm("Re-number all entries for this event.")) return;
		$(this).closest('form').find('[name=renumber]').val(1);
		$(this).closest('form').submit();
	});
	</script>
	<?php 
	echo form_close();
} 

?>
<div class="d-flex flex-wrap gap-4">
<?php 
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);

$edit_base = base_url("/admin/entries/edit/{$event->id}");
foreach($entries as $dis) { ?>
	<section class="mw-100">
	<h4><?php echo $dis->name;?></h4>
	<?php foreach($dis->cats as $cat) {
		$tbody = [];
		foreach($cat->entries as $entry) {
			$row = [
				$entry->num,
				$users[$entry->user_id]->abbr ?? '?',
				$entry->name
			];
			if($format!='plain') {
				$dob = strtotime($entry->dob);
				$row[] = date('d-M-Y', $dob);
			}
			$tbody[] = $row;
		}
		
		if($format=='plain') {
			if($tbody) {
				$table->autoHeading = false;
				printf('<h5>%s</h5>', $cat->name);
				printf('<div class="table-responsive">%s</div>', $table->generate($tbody)); 
			}
		}
		else {
			$table->setHeading(['num', 'club', 'name', 'DoB']);
			$params = [
				'disid' => $dis->id,
				'catid' =>$cat->id
			];
			$href = $edit_base . '?' . http_build_query($params);
			printf('<h5>%s</h5>', anchor($href, $cat->name, ['title' => 'Edit category']));
			if($tbody) {
				printf('<div class="table-responsive">%s</div>', $table->generate($tbody)); 
			}
			else {
				echo '<p class="alert-info">Empty category.</p>';
			}
		}
	} ?>
	</section>
<?php } ?>
</div>
<?php
# d($entries);
# d($event);
# d($users);
$this->endSection(); 
