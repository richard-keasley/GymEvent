<?php $this->extend('default');

$this->section('content');

$can_edit = \App\Libraries\Auth::check_path('admin/entries/edit');
if($can_edit) {
	$format = $format ?? 'plain' ;
}
else {
	$format = 'plain';
}

if($can_edit) {
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

$base_edit = "/admin/entries/edit/{$event->id}";
foreach($entries as $dis) { ?>
	<section class="mw-100">
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
		if($can_edit) {
			$params = [
				'disid' => $dis->id,
				'catid' =>$cat->id
			];
			$href = base_url($base_edit .'?' . http_build_query($params));
			$label = anchor($href, $cat->name, ['title' => 'Edit category']);
		}
		else {
			$label = $cat->name;
		}
				
		printf('<h5>%s</h5>', $label);
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
