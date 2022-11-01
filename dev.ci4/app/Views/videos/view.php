<?php $this->extend('default');
$table = \App\Views\Htm\Table::load('default');
$video = new \App\Libraries\Video;
$video->event_id = $event->id;

$this->section('content'); 
#d($entries);
#d($event);
?>

<form method="GET" id="selector">
<?php echo form_dropdown('catid', $cat_opts, $filter['catid'], 'class="form-control"');?>
<script>
$(function() {
	$('[name=catid]').change(function(){$('#selector').submit();});
});
</script>
</form>

<?php
foreach($entries as $dis) { ?>
	<div class="vars"><h4><?php echo $dis->name;?></h4>
	<?php foreach($dis->cats as $cat) {
		$thead = array_merge(['num', 'name', 'club'], $cat->videos);
		$table->setHeading($thead);
		$tbody = [];
		foreach($cat->entries as $entry) {
			#d($entry);
			if($entry->perm('videos', 'view')) {
				$video->entry_num = $entry->num;
				$name = $entry->name;
				if($entry->perm('videos', 'edit')) {
					$name .= sprintf(' <a href="%s" class="btn btn-sm btn-outline-secondary bi-pencil-square" title="edit this entry"></a>', site_url($entry->url('videos')));
				}
				$tr = [
					$entry->num,
					$name,
					$users[$entry->user_id]->name ?? '??' ,
				];
				foreach($entry->videos as $exe=>$url) {
					$video->exe = $exe;
					$video->url = $url;
					$tr[] = $video->view();
				}
				$tbody[] = $tr;
			}
		}
		printf('<h6>%s</h6>', $cat->name);
		echo $table->generate($tbody);
	}
	?>
	</div>
<?php } ?>

<?php 

d($users);
$this->endSection(); 
