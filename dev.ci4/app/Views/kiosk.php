<!DOCTYPE html>
<html lang="en">
<head><?php 
$viewpath = config('Paths')->viewDirectory;
$style = $style ?? '';
$style .= file_get_contents("{$viewpath}/kiosk.css");
$this->setData(['style' => $style]);
echo $this->include('includes/html-head');
?>
</head>
<body id="kiosk">
<div id="container">
<div id="flex"><?php 
echo $this->renderSection('content');
?></div>

<footer><?php 
echo base_url($link);
?></footer>

<img id="logo" src="/app/profile/logo.png">

</div>
<?php
echo $this->include('includes/html-foot');
?>
</body>
</html>
