<?php
use \App\Libraries\Teamtime as tt_lib;
$teams = tt_lib::get_value('teams');
?>
<div class="teams">
<?php
if($teams) {
	$table = \App\Views\Htm\Table::load('striped');
	$table->autoHeading = false;
	echo $table->generate($teams);
}
else { ?>
	<p class="alert alert-warning">'Teams' appears to be empty</p>
<?php } ?>
</div>
