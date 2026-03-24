<?php 
// no terms or password needed 
if(!$event->terms && !$event->password) return '';
// already agreed
if($clubret->terms) return ''; 

// show nag-screen every page load
?>
<div class="modal" id="dlgevterms" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title"><?php echo $event->title;?></h5>
</div>
	  
<div class="modal-body">
<?php

if($event->password) { ?>
<p>Please enter the event password: <?php
$input = [
	'name' => "evpass",
	'type' => "text",
	'autocomplete' => "new-password",
	'class' => "form-control",
];
echo form_input($input);
?></p>
<?php }

$terms_layout = 'view';
include __DIR__ . '/_terms.php';
$terms_layout = $this->data['terms_layout'] ?? null ;
?></div>

<div class="modal-footer">
<button title="Decline the terms of this event" type="button" class="btn btn-secondary" onclick="evterms.decline()">Cancel</button>
<button title="Accept the terms of this event" type="button" class="btn btn-primary" onclick="evterms.accept()">Agree</button>
</div>
  
</div>
</div>
</div>

<script>

const evterms = {

modal_id: 'dlgevterms',
modal: null,
checkbox: $('[name=terms]')[0],
escape: '<?php echo site_url("events/view/{$event->id}");?>',
password: <?php echo $event->password ? 'true' : 'false' ;?>,

show: function() {
	var el = document.getElementById(this.modal_id);
	this.modal = new bootstrap.Modal(el);
	this.modal.show();
	
	el.addEventListener('hide.bs.modal', (event) => {
		if(!evterms.checkbox.checked) {
			window.location.href = evterms.escape;
		}
	});
},
	
accept: function() {
	if(this.password) {
		var postvars = {
			password: $(`#${this.modal_id} [name=evpass]`).val(),
		};
		var api = '<?php echo base_url("api/events/password/{$event->id}");?>';
		
		$.post(api, securepost(postvars))
		.done(function(response) {
			if(response===true) evterms.confirm();
			else {
				alert('Your password is not correct');
			}
		});
	}
	else this.confirm();
},

confirm: function() {
	this.checkbox.checked = true;
	this.modal.hide();
},

decline: function() {
	window.location.href = evterms.escape;
	// evterms.checkbox.checked = false;
	// evterms.modal.hide();
},

};

$(function() {
evterms.show();
});

</script>
