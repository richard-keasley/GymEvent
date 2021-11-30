<?php echo view('includes/_head');?>

<main class="clearfix"><?php 
if(empty($this->sections['sidebar'])) {
	$this->renderSection('content');
} 
else { ?>
<div class="row">
	<div class="col-auto">
	<?php $this->renderSection('sidebar'); ?>
	</div>
	<div class="col" style="min-width: 15em;">
	<?php $this->renderSection('content'); ?>
	</div>
</div>
<?php } 
?></main>

<?php if(!empty($this->sections['bottom'])) { ?>
<section>
	<?php echo $this->renderSection('bottom'); ?>
</section>
<?php } ?>

<?php 
echo view('includes/_footer');
echo view('includes/js');
echo view('includes/_foot');
 
