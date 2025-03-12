<?php
// look for help file
$htmls = new \App\Models\Htmls;
$html = $htmls->find_path();
if(!$html) return;

?>
<button type="button" id="btnhelp" class="btn badge bg-info float-end d-print-none" data-bs-toggle="modal" data-bs-target="#dlghelp" data-id="<?php echo $html->id;?>"><span class="bi bi-question-circle"></span></button>

<div class="modal fade" id="dlghelp" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable modal-lg">
<div class="modal-content">
<div class="modal-header">
	<h4 class="modal-title"><?php echo $html->heading ?? 'Help' ;	?></h4>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
</div>
<div class="modal-footer">
	<?php
	echo (getlink("setup/help/edit/{$html->id}", 'edit'));	
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
	var id = button.getAttribute('data-id');
	var modalTitle = dlghelp.querySelector('.modal-title');
	var modalBody = dlghelp.querySelector('.modal-body');
	var url = '<?php echo site_url("api/help/view");?>/' + id;
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
