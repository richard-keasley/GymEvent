<div class="teams">
<table class="table table-striped"><tbody>
<?php 
$tt_lib = new \App\Libraries\Teamtime;
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
</div>