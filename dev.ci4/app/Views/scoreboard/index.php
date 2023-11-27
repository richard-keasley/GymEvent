<?php $this->extend('default');

$this->section('content'); ?>
<div class="item-image"><img src="/app/scoreboard/screen.png" alt="screen"></div>

<?php
$appvars = new \App\Models\Appvars();
echo $appvars->get_value('scoreboard.home');
?>

<div class="toolbar"><?php 
echo \App\Libraries\View::back_link("/");
echo getlink('setup/scoreboard', 'admin');
echo getlink('scoreboard/follow', 'follow');
?></div>

<?php $this->endSection();
	