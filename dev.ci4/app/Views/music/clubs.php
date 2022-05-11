<?php $this->extend('default');

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

foreach($entries as $dis) {
	foreach($dis->cats as $cat) {
		foreach($cat->entries as $entry) {
			$user_id = $entry->user_id;
			if(!isset($tbody[$user_id])) {
				$user = $users[$user_id] ?? null;
				if($user) {
					$club = anchor("/admin/music/view/{$event->id}?user={$user_id}", $user->name) . ' ' . $user->link() ;
					if($user->email) {
						$club .= sprintf(' <a href="mailto:%1$s" title="%1$s"><span class="bi-envelope"><span></a>', $user->email);
					}
					$orderby[$user_id] = $user->name;
				}
				else {
					$club = '[unkown]';
					$orderby[$user_id] = '';
				}
				$tbody[$user_id] = ['club' => $club];
				foreach($state_labels as $key) { 
					$tbody[$user_id][$key] = 0;
				}
			}
			$track->entry_num = $entry->num;
			foreach($entry->music as $exe=>$check_state) {
				$track->exe = $exe;
				$track->check_state = $check_state;
				$column = $track->status();
				$count = $tbody[$user_id][$column] ?? 0;
				$tbody[$user_id][$column] = $count + 1;
			}
		}
	}
}
array_multisort($orderby, $tbody);

if($tbody) {
	$track_count = 0;
	$tfoot = ['club' => count($tbody) . ' clubs'];
	$thead = ['club' => '<div style="width:10em;">Club</div>'];
	foreach($state_labels as $key) { 
		$thead[$key] = sprintf('<div style="width:4em;overflow:hidden;">%s</div>', $key);
		$column = array_column($tbody, $key);
		$sum = array_sum($column);
		$track_count += $sum;
		$tfoot[$key] = $sum ? sprintf('%u / %u', $sum, count(array_filter($column))) : '' ;
		foreach($tbody as $rowkey=>$row) {
			if(!$row[$key]) $tbody[$rowkey][$key] = '';
		}
	}
	$tfoot['club'] = sprintf('%u tracks / %u clubs', $track_count, count($tbody));
	$table = new \CodeIgniter\View\Table(\App\Libraries\Table::templates['bordered']);
	$table->setFooting($tfoot);
	$table->setHeading($thead);
	
	echo $table->generate($tbody);
}

$this->endSection(); 
