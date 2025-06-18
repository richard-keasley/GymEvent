<?php $this->extend('default');

$this->section('content'); ?>

<ul class="mb-2 list-group"><?php

$format = '<li class="list-group-item">%s<code class="d-block py-2">%s</code></li>';

$attrs = [
	'class' => "fw-bold d-block"
];
foreach($links as $key=>$link) {
	if(!$link) continue; // link closed
	if($key[0]=='_') continue; // not a URL
	$href = "x/{$key}";
	$anchor = anchor($href, base_url($href), $attrs);
	printf($format, $anchor, $link);
}
?></ul>

<p><img src="/app/profile/image.png"></p>

<?php 
$this->endSection();

$this->section('bottom'); 
$btns = [];
$btn = getlink('setup/links', 'edit');
if($btn) $btns[] = $btn;
if($btns) {
	$format = '<div class="toolbar nav">%s</div>';
	printf($format, implode(' ', $btns));
}
$this->endSection(); 
