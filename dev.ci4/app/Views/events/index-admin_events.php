<p>Select the event you are interested in.</p>
<p>Club returns, music and videos use the following states:</p>
<ul class="list list-unstyled">
<li><?php echo \App\Entities\Event::icons['future'];?> 0: Waiting: Not yet open</li>
<li><?php echo \App\Entities\Event::icons['current'];?> 1: Edit: Open for uploads / edits</li>
<li><?php echo \App\Entities\Event::icons['current'];?> 2: View: Read-only</li>
<li><?php echo \App\Entities\Event::icons['past'];?> 3: Finished</li>
</ul>
<p>Additionally, events may be marked as follows:</p>
<ul class="list list-unstyled">
<li><?php echo \App\Entities\Event::icons['hidden'];?> Ready for deletion (hidden)</li>
<li><?php echo \App\Entities\Event::icons['private'];?> Private event (hidden from public)</li>
</ul>

<ul class="list list-unstyled"><?php
$dates = [];
foreach($events as $event) {
	if($event->deleted_at) continue;
	$clubrets = $event->clubrets < 3;
	$music = $event->music < 3;
	$link = site_url("admin/events/view/{$event->id}");
	$link = sprintf('<a href="%s">%s</a>', $link, $event->title);		

	foreach($event->dates as $key=>$date) {
		if(!$date) continue;
		if(strpos($key, 'clubrets')===0 && !$clubrets) continue;
		if(strpos($key, 'music')===0 && !$music) continue;
		
		$key = str_replace('clubrets', 'online entry', $key);
		$key = humanize($key);
		$dates[] = [$date, "({$key})", $link];
	}

}
asort($dates);
# d($dates);

$format = '<li class="py-2"><span class="text-%s bi bi-%s"></span> %s %s %s</li>';
$now = new \datetime;
foreach($dates as $row) {
	$date = new \datetime($row[0]);
	$past = $date < $now;
	$icon = $past ? 'bell' : 'bell-fill' ;
	$colour = $past ? 'dark' : 'danger' ;
	printf($format, $colour, $icon, $date->format('j M Y'), $row[2], $row[1]);
}

?></ul>
