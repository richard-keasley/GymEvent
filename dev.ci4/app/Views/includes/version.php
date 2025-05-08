<section class="card bg-light">
<div class="card-header">Versions</div>
<div class="card-body">
<?php

$include = FCPATH . 'app/gymevent.css';
$contents = file_get_contents($include);
$pattern = '#bootstrap icons v(.*) #mi';
preg_match($pattern, $contents, $matches);
$bs_icons = $matches[1] ?? '?';

$vars = [
	'PHP' => phpversion(),
	'CodeIgniter' => \CodeIgniter\CodeIgniter::CI_VERSION,
	
	'Bootstrap' => '<span id="bsv"></span>',
	'Bootstrap icons' => $bs_icons,
	'jQuery' => '<span id="jqv"></span>',
	'TinyMCE' => \App\Views\Htm\Editor::version,
];

// ThirdParty classes
foreach(\App\ThirdParty\classes::classes() as $title=>$version) {
	$vars[$title] = $version;
}

echo new \App\Views\Htm\Vartable($vars);
?>
<script>
$(function() {
$('#jqv').text(jQuery().jquery);
$('#bsv').text(bootstrap.Tooltip.VERSION);
});
</script>
</div>
</section>