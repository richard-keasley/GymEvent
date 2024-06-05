<?php $this->extend('default');

$this->section('content'); ?>
<h5>Filter Scoreboard data</h5>
<p>Filter the data from scoreboard database. Each line is comma separated digits.</p>
<?php 
echo form_open();

foreach($filters as $varname=>$value) { ?>
<div class="my-1 row">
<label class="col-form-label col-sm-4"><?php echo $varname;?></label>		
<div class="col-sm-8">
	<input type="text" 
		name="<?php echo $varname;?>" 
		value="<?php echo implode(', ', $value);?>" 
		class="form-control">
</div>
</div>
<?php } ?>

<div class="toolbar">
<?php 
echo \App\Libraries\View::back_link("setup/scoreboard/data");
?>
<button class="btn btn-primary" title="apply these filters" type="submit" name="filter" value="1"><i class="bi bi-filter"></i></button>
</div>

<?php
echo form_close();

$this->endSection(); 
