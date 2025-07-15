<?php 
if(!\App\Libraries\Auth::check_role('controller')) return;

$risk = model('Htmls')->find_path('~about-data-risk');
if(!$risk) return;
?>
<div class="p-1 border bg-light"><?php 
	echo "<h3>{$risk->heading}</h3>";
	echo $risk;
	# d($risk);
?></div>
<?php

$buffer = getlink("setup/help/edit/{$risk->id}", 'edit');
if($buffer) printf('<div class="toolbar">%s</div>', $buffer);
