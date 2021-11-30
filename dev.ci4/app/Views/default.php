<?php 
echo view('includes/_head');

// help button
if($help) { ?>
<button type="button" class="btn badge bg-info float-end" data-bs-toggle="modal" data-bs-target="#modalHelp" data-stub="<?php echo $help;?>"><span class="bi bi-question-circle"></span></button>

<div class="modal fade" id="modalHelp" tabindex="-1" aria-hidden="true">
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
const modalHelp = document.getElementById('modalHelp')
modalHelp.addEventListener('show.bs.modal', function (event) {
	var button = event.relatedTarget;
	var stub = button.getAttribute('data-stub');
	var modalTitle = modalHelp.querySelector('.modal-title');
	var modalBody = modalHelp.querySelector('.modal-body');
	var url = '<?php echo base_url("api/help/view");?>/' + stub;
	$.get(url, function(response) {
		try {
			modalBody.innerHTML = response;
		}
		catch(err) {
			console.error(err.message);
		}
	})
	.fail(function(jqXHR) {
		console.error(get_error(jqXHR));
	});
});
</script>
<?php }

echo \App\Libraries\View::breadcrumbs($breadcrumbs);
?>

<main class="clearfix"><?php 

$this->renderSection('top');

if(empty($this->sections['sidebar'])) {
	$this->renderSection('content');
} 
else { ?>
<div class="row">
	<div class="col-auto">
	<?php $this->renderSection('sidebar'); ?>
	</div>
	<div class="col" style="min-width: 15em;">
	<?php $this->renderSection('content'); ?>
	</div>
</div>
<?php } 

$this->renderSection('bottom');
?></main>

<?php 
echo view('includes/_footer');
echo view('includes/js');
echo view('includes/_foot');
 
