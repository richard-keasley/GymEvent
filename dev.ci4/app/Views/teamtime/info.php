<?php $this->extend('default');

$this->section('content'); ?>
<p>This page will not automatically update. Update the page to ensure you have up to date information.</p>
<?php
echo \App\Libraries\Teamtime::view_html($var_name);
$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top"><?php
echo \App\Libraries\View::back_link($back_link);
?></div>
<?php $this->endSection(); 