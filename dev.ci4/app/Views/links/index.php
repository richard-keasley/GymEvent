<?php $this->extend('default');

$this->section('content'); ?>
<p>Defined shortcut links.</p>

<ul class="list-group"><?php
foreach($links as $key=>$link) {
	printf('<li class="list-group-item"><strong>%s</strong><div class="py-2"><code>%s</code></div></li>', 
		anchor($link, $key), 
		$link
	);
}

?></ul>

<p><img src="/app/profile/image.png"></p>

<?php 
$this->endSection();
