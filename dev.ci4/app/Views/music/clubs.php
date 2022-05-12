<?php $this->extend('default');
$base_url = base_url("/admin/music/clubs/{$event->id}");

$this->section('content'); ?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link("admin/music/view/{$event->id}"); ?>
</div>
<?php 

# d($event);
# d($entries);
# d($users);

$track = new \App\Libraries\Track();
$track->event_id = $event->id;

$tbody = []; $orderby = [];
$state_labels = ['missing', 'unchecked', 'ok', 'archived', 'withdrawn'];
if($filter && in_array($filter, $state_labels)) $state_labels = [$filter];
$new_row = ['club' => ''];
foreach($state_labels as $state_label) $new_row[$state_label] = 0;

if($filter) {
	$btn = anchor($base_url, '<i span class="text-primary bi-x-circle" title="clear filter"></i>');
	printf('<p><strong>Filter:</strong> %s %s</p>', $filter, $btn);
}
# d($filter);

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
								$club .= sprintf(' <a href="mailto:%1$s" title="%1$s"><span class="bi-envelope"><span></a>', $user->email);
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
array_multisort($orderby, $tbody);

if($tbody) {
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
		$thead[$state_label] = anchor("{$base_url}?state={$state_label}", $state_label, $attr);
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
