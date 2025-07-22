<?php
$fees = $clubret->fees;
# d($fees);
	
$vartable = new \App\Views\Htm\Vartable;
foreach($fees as $fee) {
	$vartable->items[$fee[0]] = \App\Views\Htm\Table::money($fee[1]);
}
$total = array_sum(array_column($fees, 1));
$vartable->footer = [\App\Views\Htm\Table::money($total), 'Total'];
echo $vartable->htm();

if($clubret->event->stafffee && !$clubret->stafffee) { ?>
<div class="alert alert-secondary p-1"><?php
	echo $clubret->event->staff;
	printf('<p>%s has been added to your entry fee to cover staff costs for this event.</p>', number_to_currency($clubret->event->stafffee));
?></div>
<?php }
