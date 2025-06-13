<?php $this->extend('default');

$this->section('content'); 

$track = new \App\Libraries\Track();
$track->event_id = $event->id;

# d($event);
# d($entries);
# d($filter, $cat_opts);

if($event->dates['music_closes']) {
	$date = new \datetime($event->dates['music_closes']);
	printf('<p>The music upload service will close at 8:00pm on %s.</p>', $date->format('j F'));
} ?>

<form method="GET" id="selector" class="mb-2 toolbar">
<?php 
echo getlink("admin/music/view/{$event->id}", 'admin');
$input = [
	'name' => "cat",
	'options' => $cat_opts,
	'selected' => $filter['cat'],
	'class' => "form-control"
];
echo form_dropdown($input);
?>
<script>
$(function() {
	$('#selector [name=cat]').change(function() {
		$('#selector').submit();
	});
});
</script>
</form>

<?php 
echo $this->include('Htm/Playtrack');

if($event->music<2) { ?>
	<p>Click <span class="text-primary"><span title="edit this entry" class="bi bi-pencil"></span> edit</span> to upload new tracks for each entry.</p>
<?php } 
?>
<div id="player"><?php
$table = \App\Views\Htm\Table::load('responsive');
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
				$tr[] = $track->playbtn();
				if(!$key) $thead[] = $track->exe;
			}
			if($entry->perm('music', 'edit')) {
				$label = sprintf('<span title="edit track for %s/%s" class="bi bi-pencil"></span>', $entry->num, $track->exe);
				$tr[] = getlink($entry->url('music'), $label);
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
</div>
<?php $this->endSection(); 
