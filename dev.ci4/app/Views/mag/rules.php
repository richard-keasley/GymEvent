<?php $this->extend('default');
 
$this->section('content');?>
<p>Please tell Richard Keasley if you spot any errors.</p>

<pre>
<?php
$rules = new \App\Libraries\Mag\Rules;
d($rules);
d(\App\Libraries\Mag\Rules::index());


?>
</pre>


<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link("mag");?>
</div>
<?php $this->endSection(); 
