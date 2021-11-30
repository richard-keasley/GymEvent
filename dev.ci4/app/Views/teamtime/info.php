<?php $this->extend('default');

$this->section('content'); 
echo \App\Libraries\Teamtime::view_html($var_name);
$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top"><?php
echo \App\Libraries\View::back_link('');
?></div>
<?php $this->endSection(); 