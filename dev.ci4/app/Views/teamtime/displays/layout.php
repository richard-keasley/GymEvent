<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="/app/teamtime/display.css" rel="stylesheet" type="text/css">
<?php 
echo \App\ThirdParty\jquery::script(); 
if(!empty($title)) printf('<title>%s</title>', $title);
echo $this->include('teamtime/js');
if(!empty($style)) echo "<style>{$style}</style>";
?>
</head>
<body><?php 
$this->renderSection('body'); 
echo $this->include('includes/js');
?></body>
</html>