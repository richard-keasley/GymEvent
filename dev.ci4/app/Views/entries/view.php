<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);

$this->section('content');
if(\App\Libraries\Auth::check_path('admin/entries/edit')) {
	$attr = [
		'class' => "toolbar nav sticky-top"
	];
	echo form_open(base_url(uri_string()), $attr);
	echo \App\Libraries\View::back_link("admin/events/view/{$event->id}");
	echo getlink("admin/entries/edit/{$event->id}", 'edit');
	echo getlink("admin/entries/import/{$event->id}", 'import');
	echo getlink("admin/entries/categories/{$event->id}", 'categories');
	echo getlink("admin/entries/scoreboard/{$event->id}", 'scoreboard');
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

foreach($entries as $dis) { ?>
	<div class="vars">
	<h4><?php echo $dis->name;?></h4>
	<?php foreach($dis->cats as $cat) {
 		$table->setHeading(['num', 'name', 'club', 'DoB']);
		$tbody = [];
		foreach($cat->entries as $entry) {
			$dob = strtotime($entry->dob);
			$tbody[] = [
				'num' => $entry->num,
				'name' => $entry->name,
				'club' => $entry->club(),
				'DoB' => date('d-M-Y', $dob)
			];
		}
		printf('<h6>%s</h6>', $cat->name);
		echo $table->generate($tbody);
	} ?>
	</div>
<?php } 
#d($entries);
#d($event);
$this->endSection(); 
