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
# d($attr);

# d($event);
# d($entries);
# d($users);

$track = new \App\Libraries\Track();
$track->event_id = $event->id;

$tbody = []; $orderby = [];
$new_row = ['club' => ''];
foreach($state_labels as $state_label) $new_row[$state_label] = 0;

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
								$club .= ' ' . mailto("{$user->email}?subject={$event->title} - music upload", '<i class="bi-envelope"><i>', ['title' => $user->email]);
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
	$attr = [
		'style' => "width:10em;",
		'class' => "d-block overflow-hidden"
	];
	$thead = ['club' => 'club'];
	// used for state header
	$attr = [
		'style' => "width:4em;",
		'class' => "d-block overflow-hidden"
	];
	foreach($state_labels as $state_label) {
		$thead[$state_label] =  anchor("/admin/music/view/{$event->id}?status={$state_label}", $state_label);
			$column = array_column($tbody, $state_label);
		$sum = array_sum($column);
		$track_count += $sum;
		$tfoot[$state_label] = $sum ? sprintf('%u / %u', $sum, count(array_filter($column))) : '' ;
		foreach($tbody as $rowkey=>$row) {
			if(!$row[$state_label]) $tbody[$rowkey][$state_label] = '';
		}
	}
	$tfoot['club'] = sprintf('%u tracks / %u clubs', $track_count, count($tbody));
	
	$table = new \CodeIgniter\View\Table(\App\Libraries\Table::templates['bordered']);
	$table->setFooting($tfoot);
	$table->setHeading($thead);
	echo $table->generate($tbody);
}

$this->endSection(); 
