<section><?php 
$files = \App\Models\Events::files();
$files_size = 0;
foreach($files as $file) $files_size += $file->getSize();

$allowed = config('App')->events_space;
$pc = 100 * $files_size / $allowed;
$colour = ($pc>80) ? 'danger' : 'light' ;
$format = '<p class="bg-%s-subtle p-1">Event files occupy %s of disk space (%u%% of %s allowance).</p>';
printf($format, 
	$colour,
	formatBytes($files_size),
	$pc,
	formatBytes($allowed)
);
?></section>

<section>
<p>Club returns and music use the following states:</p>
<ul class="list list-unstyled">
<li><?php echo \App\Entities\Event::icons['future'];?> 0: Waiting: Not yet open</li>
<li><?php echo \App\Entities\Event::icons['current'];?> 1: Current (either for edits or complete)</li>
<li><?php echo \App\Entities\Event::icons['past'];?> 3: Finished</li>
<li><?php echo \App\Entities\Event::icons['hidden'];?> Ready for deletion (hidden)</li>
<li><?php echo \App\Entities\Event::icons['private'];?> Private event (hidden from public)</li>
</ul>
</section>

<ul class="list list-unstyled"><?php
$today = new \datetime('today');
$dates = [];
foreach($events as $event) {
	if($event->deleted_at) continue;
	$clubrets = $event->clubrets < 3;
	$music = $event->music < 3;
	$link = site_url("admin/events/view/{$event->id}");
	$link = sprintf('<a href="%s">%s</a>', $link, $event->title);

	$date = new \datetime($event->date);
	if($date<$today) continue;
	
	$dates[] = [
		'date' => $event->date,
		'event' => '', 
		'link' => $link
	];
	
	foreach($event->dates as $key=>$date) {
		if(!$date) continue;
		if(strpos($key, 'clubrets')===0 && !$clubrets) continue;
		if(strpos($key, 'music')===0 && !$music) continue;
		
		$dates[] = [
			'date' => $date,
			'event' => $key, 
			'link' => $link
		];
	}

}
sort($dates);
# d($dates);

// don't show dates too far in the future
$last = new \datetime;
$last->add(new \DateInterval("P3M")); // 3 months

$format = '<li class="py-1"><span class="text-%s bi bi-%s"></span> %s %s %s</li>';
foreach($dates as $row) {
	$date = new \datetime($row['date']);
	if($date > $last) continue;

	$state = match($date<=>$today) { 
		-1 => 'past',
		 0 => 'today',
		 1 => 'future'
	};
	
	$event = str_replace('clubrets', 'online entry', $row['event']);
	$event = humanize($event);
	if($event) $event = "({$event})";
	
	$icon = $event ? 'bell' : 'calendar-event' ;
	if($state=='today') $icon .= '-fill';
	
	$colour = ($state=='future') ? 'dark' : 'danger' ;
	
	printf($format, $colour, $icon, $date->format('j M Y'), $row['link'], $event);
}

?></ul>
