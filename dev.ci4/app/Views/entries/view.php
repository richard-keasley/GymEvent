<?php $this->extend('default');

$this->section('content');
$format = $format ?? 'full' ;
if(\App\Libraries\Auth::check_path('admin/entries/edit')) {
	$link_format = $format=='plain' ? 'full' : 'plain' ;
	$attr = [
		'class' => "toolbar nav sticky-top"
	];
	echo form_open(base_url(uri_string()), $attr);
	echo \App\Libraries\View::back_link("admin/events/view/{$event->id}");
	echo getlink("admin/entries/edit/{$event->id}", 'edit');
	echo getlink("admin/entries/view/{$event->id}/{$link_format}", $link_format);
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

foreach($entries as $dis) { ?>
	<section>
	<h4><?php echo $dis->name;?></h4>
	<?php foreach($dis->cats as $cat) {
		if($format=='full') {
			$table->setHeading(['num', 'club', 'name', 'DoB']);
		}
		else {
			$table->autoHeading = false;
		}
		$tbody = [];
		foreach($cat->entries as $entry) {
			$row = [
				$entry->num,
				$users[$entry->user_id]->abbr ?? '?',
				$entry->name
			];
			if($format=='full') {
				$dob = strtotime($entry->dob);
				$row[] = date('d-M-Y', $dob);
			}
			$tbody[] = $row;
		}
		printf('<h5>%s</h5>', $cat->name);
		printf('<div class="table-responsive">%s</div>', $table->generate($tbody)); 
	} ?>
	</section>
<?php } ?>
</div>
<?php
# d($entries);
# d($event);
# d($users);
$this->endSection(); 
