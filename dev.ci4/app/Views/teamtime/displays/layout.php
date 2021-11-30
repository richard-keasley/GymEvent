<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="/public/teamtime/display.css" rel="stylesheet" type="text/css" >
<script src="/public/ui/jquery/jquery-3.6.0.min.js" type="text/javascript"></script>
<?php 
if(!empty($title)) printf('<title>%s</title>', $title);
echo view('teamtime/js');
?>
</head>
<body><?php 
$this->renderSection('body'); 
echo view('includes/js');
?></body>
</html>