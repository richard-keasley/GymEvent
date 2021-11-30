<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table compact">'];
$table->setTemplate($template);

$this->section('content'); 

$track = new \App\Libraries\Track();
$track->event_id = $event->id;

#d($event);
#d($entries);
#d($filter, $cat_opts, $filter['cat']);

?>
<form method="GET" id="selector" class="mb-2">
<?php echo form_dropdown('cat', $cat_opts, $filter['cat'], 'class="form-control"');?>
<script>
$(function() {
	$('[name=cat]').change(function(){$('#selector').submit();});
});
</script>
</form>

<p>Click on <span class="text-primary"><span class="bi bi-pencil"></span> edit</span> to upload new tracks for each entry.</p>

<?php
foreach($entries as $dis) { ?>
	<section><h4><?php echo $dis->name;?></h4>
	<?php foreach($dis->cats as $cat) {
		$tbody = [];
		$thead = ['num', 'name', 'club'];
		foreach($cat->entries as $key=>$entry) {
			$tr = [
				$entry->num,
				$entry->name,
				$entry->club()
			];
			$track->entry_num = $entry->num;
			foreach($entry->music as $exe=>$check_state) {
				$track->exe = $exe;
				$track->check_state = $check_state;
				$tr[] = $track->view();
				if(!$key) $thead[] = $track->exe;
			}
			if($entry->perm('music', 'edit')) {
				$tr[] = getlink($entry->url('music'), '<span class="bi bi-pencil"></span>');
			}
			$tbody[] = $tr;
		}
		$thead[] = '';
		printf('<h6>%s</h6>', $cat->name);
		$table->setHeading($thead);
		echo $table->generate($tbody);
	}
	?>
	</section>
<?php } ?>
<?php 
$this->endSection(); 

