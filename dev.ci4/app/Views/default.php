<?php 
echo $this->include('includes/_head');
?>
<main class="clearfix"><?php 

echo $this->renderSection('top');

if(empty($this->sections['sidebar'])) {
	echo $this->renderSection('content');
} 
else { ?>
<div class="row">
	<div class="col-sm-auto">
	<?php echo $this->renderSection('sidebar'); ?>
	</div>

	<div class="col-sm" style="min-width: 15em;">
	<?php echo $this->renderSection('content'); ?>
	</div>
</div>
<?php }

echo $this->renderSection('bottom');

?></main>

<?php 

echo $this->include('includes/_footer');
echo $this->include('js/foot');
echo $this->include('includes/_foot');
