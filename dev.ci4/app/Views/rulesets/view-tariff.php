<div class="d-md-flex flex-wrap">

<section class="border m-1 p-1">
<div class="row"><?php
$list = [
	'min_tariff' => sprintf('%.1f', $exe_rules['d_min']),
	'max_tariff' => sprintf('%.1f', $exe_rules['d_max']),
	'exercise_count' => intval($exe_rules['exe_count']),
];
$format = '<div class="col col-auto" style="min-width:12em;"><strong>%s:</strong> %s</div>';

foreach($list as $key=>$item) {
	printf($format, humanize($key), $item);
}
?></div>
</section>

</div>