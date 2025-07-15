<!DOCTYPE html>
<html lang="en">
<head>
<?php 
$stylesheets = ['teamtime/display.css'];
$this->setData(['stylesheets' => $stylesheets]);
echo $this->include('includes/html-head');

echo $this->include('teamtime/js');
?>
</head>
<body><?php 

echo $this->renderSection('body'); 

echo $this->include('includes/html-foot');
?>
</body>
</html>