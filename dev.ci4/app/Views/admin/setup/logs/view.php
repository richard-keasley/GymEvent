<?php $this->extend('default');

$this->section('content'); ?>
<pre><?php readfile($logfile);?></pre>
<?php $this->endSection(); 

$this->section('top');
$attrs = ['class' => 'toolbar sticky-top'];
$hidden = ['cmd' => 'delete', 'logkey' => $logkey];
echo form_open('setup/logs', $attrs, $hidden);
echo implode(' ', $buttons);
echo form_close();
$this->endSection(); 