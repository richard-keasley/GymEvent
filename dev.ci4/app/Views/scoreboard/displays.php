<?php $this->extend('default');

$this->section('content'); ?>
<p><img src="/app/scoreboard/screen.png" class="float-end" style="max-width:35%" alt="screen">The application displays scores to audiences in "real time". Score corrections are also displayed.</p>
<p>You will need a large TV to display scores. We have a television available for use, but event organisers may like to provide more.</p> 
<p>The score screen display is a web page. When connecting a score screens, use a URL like<br>
<code>https://sb.gymevent.uk/display#/identify/{screen_name}</code></p>
<p>Use a unique name for {screen_name}. This enables the control desk to message individual screens and see which devices are online.</p>
<?php 
$this->endSection();
 
if($links) {
	$this->section('sidebar');
	echo $links->htm();
	$this->endSection(); 
}
