<?php $this->extend('teamtime/displays/layout');

$this->section('body'); ?>
<div id="msg"><p><?php 
echo $error ?? "undefined error";
?></p></div>
<?php $this->endSection();
