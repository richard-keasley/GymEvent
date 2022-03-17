<?php $this->extend('default');
	
$this->section('content'); ?>
<table class="table table-striped"><tbody>
<?php 
$get_var = $tt_lib::get_var('teams');
if($get_var) { 
foreach($get_var->value as $row) {
	printf('<tr><th>%s</th><td>%s</td</tr>', $row[0], $row[1]);
} 
?>
</tbody></table>
<?php } else { ?>
<p class="alert-warning">'Teams' appears to be empty</p>
<?php } ?>
<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top"><?php
	echo \App\Libraries\View::back_link($back_link); 
	echo getlink('control/teamtime/teams?bl=teamtime/info/teams', 'edit');
?></div>
<?php $this->endSection(); 
