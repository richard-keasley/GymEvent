<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="/app/teamtime/display.css" rel="stylesheet" type="text/css">
<script src="/app/jquery-3.6.1/jquery.min.js" type="text/javascript"></script>
<?php 
if(!empty($title)) printf('<title>%s</title>', $title);
echo $this->include('teamtime/js');
?>
</head>
<body><?php 
$this->renderSection('body'); 
echo $this->include('includes/js');
?></body>
</html>