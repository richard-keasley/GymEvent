<div class="modal" id="dlgevterms" tabindex="-1" data-bs-backdrop="static">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title"><?php echo $event->title;?></h5>
</div>
	  
<div class="modal-body">
<?php
$orig_layout = $terms_layout ?? null;
$terms_layout = 'view';
include __DIR__ . '/_terms.php';
$terms_layout = $orig_layout;
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

modal: null,
checkbox: $('[name=terms]')[0],
escape: '<?php echo site_url("events/view/{$event->id}");?>',

show: function() {
	var el = document.getElementById('dlgevterms');
	evterms.modal = new bootstrap.Modal(el);
	evterms.modal.show();
	
	el.addEventListener('hide.bs.modal', (event) => {
		if(!evterms.checkbox.checked) {
			// ESC is too fast, this won't happen
			window.location.href = evterms.escape;
		}
	});
},
	
accept: function() {
	evterms.checkbox.checked = true;
	evterms.modal.hide();
},

decline: function() {
	evterms.checkbox.checked = false;
	evterms.modal.hide();
},

};

$(function() {
evterms.show();
});

</script>
