<?php $this->extend('default');
$base_url = base_url("/admin/music/clubs/{$event->id}");

$this->section('content'); ?>
<form class="toolbar" id="selform"><?php 
echo \App\Libraries\View::back_link("admin/music/view/{$event->id}"); 
$attr = [
	'name' => "status",
	'selected' => $status, 
	'options' => ['-'] + \App\Libraries\Track::state_labels,
	'class' => "form-control"
];
echo form_dropdown($attr);
?>
<script>
$('#selform [name=status]').change(function() {
	$('#selform').submit();
});
</script>
</form>
<?php 
# d($event);
# d($entries);
# d($users);
# d($state_labels);
# d($status);

$track = new \App\Libraries\Track();
$track->event_id = $event->id;

$tbody = []; $orderby = [];
$new_row = ['club' => ''];
foreach($state_labels as $state_label) $new_row[$state_label] = 0;

$email = [
	'to' => [],
	'subject' => $event->title .' - music upload'
];

foreach($entries as $dis) {
	foreach($dis->cats as $cat) {
		foreach($cat->entries as $entry) {
			$user_id = $entry->user_id;
			$track->entry_num = $entry->num;
			foreach($entry->music as $exe=>$check_state) {
				$track->exe = $exe;
				$track->check_state = $check_state;
				$state_label = $track->status();
				
				if(in_array($state_label, $state_labels)) {
					if(!isset($tbody[$user_id])) {
						$tbody[$user_id] = $new_row;
						$user = $users[$user_id] ?? null ;
						if($user) {
							$club = anchor("/admin/music/view/{$event->id}?user={$user_id}", $user->name) . ' ' . $user->link() ;
							if($user->email) {
								$club .= ' ' . mailto("{$user->email}?subject={$email['subject']}", '<i class="bi-envelope"></i>', ['title' => $user->email]);
								$email['to'][] = $user->email;
							}
							$orderby[$user_id] = $user->name;
							$tbody[$user_id]['club'] = $club;
						}
						else {
							$tbody[$user_id]['club'] = '[unkown]';
							$orderby[$user_id] = '';
						}
					}
					$tbody[$user_id][$state_label]++;
				}
			}
		}
	}
}

if($tbody) {
	array_multisort($orderby, $tbody);
	$track_count = 0;
	$tfoot = ['club' => count($tbody) . ' clubs'];
	$thead = ['club' => 'club'];
	
	foreach($state_labels as $state_label) {
		$href = "/admin/music/view/{$event->id}?status={$state_label}";
		$attrs = [
			'style' => "width:4em; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;",
			'title' => $state_label,
			'class' => "d-block"
		];
		$thead[$state_label] = anchor($href, $state_label, $attrs);
		$column = array_column($tbody, $state_label);
		$sum = array_sum($column);
		$track_count += $sum;
		$tfoot[$state_label] = $sum ? sprintf('%u / %u', $sum, count(array_filter($column))) : '' ;
		foreach($tbody as $rowkey=>$row) {
			$val = $row[$state_label];
			$tbody[$rowkey][$state_label] = $val ? \App\Views\Htm\Table::number($val) : '';
		}
	}
	$tfoot['club'] = sprintf('%u tracks / %u clubs', $track_count, count($tbody));
	
	$table = \App\Views\Htm\Table::load('bordered');
	$table->setFooting($tfoot);
	$table->setHeading($thead);
	echo $table->generate($tbody);

	if($status) { ?>
	<section>
		<h5>Email</h5>
		<p><strong>Subject:</strong> <?php echo $email['subject'];?></p>
		<p><?php 
			$href = sprintf('%s?subject=%s', implode(',', $email['to']), $email['subject']);
			echo mailto($href, '<i class="bi-envelope"></i> Send email to these clubs');
		?></p>
		<section>
		<p>Dear club,</p>
		<p>This is a reminder you must complete your music upload for the 
		<em><?php echo $event->title;?></em>
		before <strong>{closing date}</strong> when the service will close.</p>
		<p>Please upload your remaining music as soon as you can.</p>
		<p>https://gymevent.uk/events/view/<?php echo $event->id;?></p>
		<p>Please contact Richard if you are struggling to do this.</p>
		<p>Richard</p>
		</section>
	</section>
	<?php } 
}

$this->endSection(); 
