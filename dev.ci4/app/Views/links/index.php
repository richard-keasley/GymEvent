<?php $this->extend('default');

$this->section('content'); ?>

<ul class="mb-2 list-group"><?php

$format = '<li class="list-group-item">%s<code class="d-block py-2">%s</code></li>';

$attrs = [
	'class' => "fw-bold d-block"
];
foreach($links as $key=>$link) {
	$href = "x/{$key}";
	$anchor = anchor($href, base_url($href), $attrs);
	printf($format, $anchor, $link);
}
?></ul>

<h4>Scoreboard displays</h4>
<p>When connecting a score screen, replace the last part of the URL with a unique identifier. This enables the control desk to message individual screens and see which devices are online.</p>

<p><img src="/app/profile/image.png"></p>

<?php 
$this->endSection();
