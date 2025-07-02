<button type="button" id="btnhelp" title="help on this page" class="btn btn-info btn-sm float-end d-print-none m-2 rounded-circle" data-bs-toggle="modal" data-bs-target="#dlghelp"><span class="bi bi-question-circle"></span></button>

<div class="modal fade" id="dlghelp" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable modal-lg">
<div class="modal-content">
<div class="modal-header">
	<h4 class="modal-title"></h4>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
</div>
<div class="modal-footer">
	<?php
	echo getlink("setup/help/edit/{$html->id}", 'edit', ['target'=>"help"]);	
	?>
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

<script>
const dlghelp = document.getElementById('dlghelp');
dlghelp.addEventListener('show.bs.modal', function (event) {
	var button = event.relatedTarget;
	var modalTitle = dlghelp.querySelector('.modal-title');
	var modalBody = dlghelp.querySelector('.modal-body');
	var url = '<?php echo site_url("api/help/view/{$html->id}");?>';
	$.get(url, function(response) {
		modalBody.innerHTML = response.body;
		modalTitle.innerHTML = response.heading ? response.heading : 'Help' ;
	})
	.fail(function(jqXHR) {
		modalBody.innerHTML = '<p class="alert alert-danger">' + get_error(jqXHR) + '</p>';
		modalTitle.innerHTML = 'error';
	});
});
</script>
