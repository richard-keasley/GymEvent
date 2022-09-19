<?php
$format = 'htm';

if($format=='htm') echo '<pre>';
foreach($export as $row) echo $row . "\n";
if($format=='htm') echo '</pre>';
