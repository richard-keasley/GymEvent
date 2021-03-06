<?php $this->extend('default');
$table = \App\Views\Htm\Table::load('default');

$this->section('content'); 

$track = new \App\Libraries\Track();
$track->event_id = $event->id;

# d($event);
# d($entries);
# d($filter, $cat_opts);

?>
<form method="GET" id="selector" class="mb-2 toolbar">
<?php 
echo getlink("admin/music/view/{$event->id}", 'admin');
echo form_dropdown('cat', $cat_opts, $filter['cat'], 'class="form-control"');
?>
<script>
$(function() {
	$('#selector [name=cat]').change(function(){
		$('#selector').submit();
	});
});
</script>
</form>

<?php if($event->music<2) { ?>
<p>Click on <span class="text-primary"><span class="bi bi-pencil"></span> edit</span> to upload new tracks for each entry.</p>
<?php } ?>

<?php
foreach($entries as $dis) { ?>
	<section><h4><?php echo $dis->name;?></h4>
	<?php foreach($dis->cats as $cat) {
		$tbody = [];
		$thead = ['#', 'Club', 'Name'];
		foreach($cat->entries as $key=>$entry) {
			$tr = [
				$entry->num,
				$users[$entry->user_id]->abbr ?? '?',
				$entry->name
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
