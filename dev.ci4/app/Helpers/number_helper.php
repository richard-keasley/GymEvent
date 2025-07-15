<?php

function number_to_currency(float $num): string {
	return format_number($num, 1, null, [
		'type'     => NumberFormatter::CURRENCY,
		'currency' => 'GBP',
		'fraction' => 2,
	]);
}
