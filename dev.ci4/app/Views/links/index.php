<?php $this->extend('default');

$this->section('content'); ?>
<p>Defined shortcut links.</p>

<ul class="mb-2 list-group"><?php
foreach($links as $key=>$link) {
	$href = base_url("x/{$key}");
	printf('<li class="list-group-item"><strong>%s</strong><div class="py-2"><code>%s</code></div></li>', 
		anchor($href),
		$link
	);
}

?></ul>

<h4>Scoreboard displays</h4>
<p>When connecting a score screen, replace the last part of the URL with a unique identifier. This enables the control desk to message individual screens and see which devices are online.</p>

<p><img src="/app/profile/image.png"></p>

<?php 
$this->endSection();
