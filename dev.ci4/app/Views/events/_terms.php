<?php
$terms = $event->terms ?? null; 
if(!$terms) return;

$terms_layout = $terms_layout ?? 'view';

# d($this->data);
?>
<div class="my-3"><?php 

switch($terms_layout) {
	
case 'edit': ?>
<p class="form-check"><label class="form-check-label"><strong><?php 
$input = [
	'class' => "form-check-input",
	'name' => "terms",
	'type' => "checkbox",
	'value' => "1",
];
if($clubret->terms) $input['checked'] = "checked";
echo form_input($input); ?>
This club agrees its staff and participants will adhere to the terms &amp; conditions below:</strong></label></p>
<?php 

break;
case 'view':
default: ?> 
<p><strong>Clubs entering this event must agree to ensure all staff and participants adhere to the terms &amp; conditions for this event (below).</strong></p>
<?php

}

echo $event->terms;
# printf('<div class="bg-secondary-subtle p-1">%s</div>', $event->terms); 
?>
</div>
