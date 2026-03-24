<?php
if(!$event->terms && !$event->password) return;

$terms_layout = $terms_layout ?? 'view';

if($terms_layout=='edit') {
	$input = [
		'class' => "form-check-input",
		'name' => "terms",
		'type' => "checkbox",
		'value' => "1",
	];	
	if($clubret->terms) $input['checked'] = "checked";
}

if(!$event->terms) $terms_layout .= "-empty";

# d($this->data, $terms_layout);

switch($terms_layout) {
	
case 'edit-empty':
$input['class'] = "d-none";
echo form_input($input);
break;

case 'view-empty':
break;
	
case 'edit': ?>
<div class="my-3">
<p class="form-check"><label class="form-check-label"><strong><?php 
echo form_input($input); ?>
This club agrees its staff and participants will adhere to the terms &amp; conditions below:</strong></label></p>
<?php echo $event->terms; ?>
</div>
<?php 
break;

case 'view':
default: ?>
<div class="my-3">
<p><strong>Clubs entering this event must agree to ensure all staff and participants adhere to the terms &amp; conditions for this event (below).</strong></p>
<?php echo $event->terms; ?>
</div>
<?php

}
