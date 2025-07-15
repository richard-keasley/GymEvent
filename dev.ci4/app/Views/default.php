<!DOCTYPE html>
<html lang="en">
<head><?php 
echo $this->include('includes/html-head');
?>
</head>
<body class="container">

<?php echo $this->include('includes/html-body-header'); ?>

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

echo $this->include('includes/html-body-footer');
echo $this->include('includes/html-foot');
?>
</body>
</html>