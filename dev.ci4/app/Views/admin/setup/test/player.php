<?php $this->extend('default');

$this->section('content'); 

$tracks = [
	'089_FX.mp3',
	'145_FX.mp3'
];

$links = [];
$player_path = 'file:///C:/Users/richa/Downloads/player/play.htm';

d($player_path);


foreach($tracks as $basename) {
	$query = ['t'=>$basename];
	$attrs = [
		'href' => sprintf("{$player_path}?%s", http_build_query($query))
	];
	printf('<p><a %s>%s</a></p>', stringify_attributes($attrs), $basename);
}




?>
<?php $this->endSection();