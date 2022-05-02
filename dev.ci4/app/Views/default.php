<?php 
echo $this->include('includes/_head');

if($help) echo $this->include('includes/help');

$nav = new \App\Views\Htm\Breadcrumbs;
echo $nav->htm($breadcrumbs);
?>

<main class="clearfix">
<?php 

$this->renderSection('top');

if(empty($this->sections['sidebar'])) {
	$this->renderSection('content');
} 
else { ?>
<div class="row">
	<div class="col-sm-auto">
	<?php $this->renderSection('sidebar'); ?>
	</div>

	<div class="col-sm" style="min-width: 15em;">
	<?php $this->renderSection('content'); ?>
	</div>
</div>
<?php } 

$this->renderSection('bottom');
?>
</main>

<?php 
echo $this->include('includes/_footer');
echo $this->include('includes/js');
echo $this->include('includes/_foot');
