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
<ul class="nav bg-light my-1">
<li class="nav-item"><a class="nav-link" href="<?php echo base_url('memdb/help/registers');?>">registers</a></li>
<li class="nav-item"><a class="nav-link" href="<?php echo base_url('memdb/help/accounts');?>">accounts</a></li>
<li class="nav-item"><a class="nav-link" href="<?php echo base_url('memdb/help/users');?>">users</a></li>
<li class="nav-item"><a class="nav-link" href="<?php echo base_url('memdb/help/structure');?>">structure</a></li>
</ul>

<?php 
echo view('includes/_footer');
echo view('includes/js');
echo view('includes/_foot');
 
