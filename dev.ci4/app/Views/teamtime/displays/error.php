<?php $this->extend('teamtime/displays/layout');
if(empty($error)) $error = "unspecified error";

$this->section('body'); ?>

<div class="msg"><p><?php echo $message;?></p></div>

<?php $this->endSection();