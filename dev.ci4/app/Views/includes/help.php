<button type="button" id="btnhelp" class="btn badge bg-info float-end" data-bs-toggle="modal" data-bs-target="#dlghelp" data-stub="<?php echo $help;?>"><span class="bi bi-question-circle"></span></button>

<div class="modal fade" id="dlghelp" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable modal-lg">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Help</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

<script>
const dlghelp = document.getElementById('dlghelp');
dlghelp.addEventListener('show.bs.modal', function (event) {
	var button = event.relatedTarget;
	var stub = button.getAttribute('data-stub');
	var modalTitle = dlghelp.querySelector('.modal-title');
	var modalBody = dlghelp.querySelector('.modal-body');
	var url = '<?php echo site_url("api/help/view");?>/' + stub;
	$.get(url, function(response) {
		try {
			modalBody.innerHTML = response;
		}
		catch(err) {
			modalBody.innerHTML = '<p class="alert alert-danger">' + err.message + '</p>';
		}
	})
	.fail(function(jqXHR) {
		modalBody.innerHTML = '<p class="alert alert-danger">' + get_error(jqXHR) + '</p>';
	});
});
</script>
