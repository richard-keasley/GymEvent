<?php $this->extend('default');

$this->section('content');
$ipinfo = new \App\Libraries\Ipinfo;
?>
<div class="row">

<div class="col-auto"><?php
$tbody = [];
$keys = ['city', 'countryCode'];
$link = [
	'title' => "details",
];
foreach($ipinfo->list() as $row) {
	$ip = $row['ip'];
	$info = $ipinfo->get($ip);
	$href = base_url("setup/ipinfo/view/{$ip}");
	$tbody[] = [
		'IP' => anchor($href, $ip, $link),
		'time' => $row['date'],
		'location' => implode(', ', $ipinfo->attributes($keys)),
	];
}

if($tbody) {
	$table = \App\Views\Htm\Table::load();
	$table->setHeading(array_keys($tbody[0]));
	echo $table->generate($tbody);
}
?></div>

<div class="col-auto"><?php
if($details) {
	$ipinfo->get($details);
	echo (string) $ipinfo;
}
?></div>

</div>
<?php
$this->endSection(); 
