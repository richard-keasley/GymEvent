<?php $this->extend('default');

$this->section('content');?>
<div class="item-image"><img src="<?php echo base_url('/app/profile/logo.png');?>"></div>
<p>All the help files are listed here. Please ask Richard if there is something more you need to know.</p>

<button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#newentry">Create entry</button>

<div class="modal fade" id="newentry">
<div class="modal-dialog modal-md">
<?php 
$attrs = ['class' => "modal-content"];
$hidden = [];
echo form_open('', $attrs, $hidden); 
?>
<div class="modal-header">
	<h5 class="modal-title">New help entry</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<div class="row my-1">
	<div class="col-3 text-end"><label class="form-label">
		Path
	</label></div>
	<div class="col-9"><?php 
	$input = [
		'class' => 'form-control',
		'name' => "path"
	];
	echo form_input($input); 
	?></div>
</div>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
	<button type="submit" class="btn btn-primary">Create</button>
</div>

<?php echo form_close();?>
</div>
</div>

<?php $this->endSection();

$this->section('sidebar');

$nav = [];
$htmls = model('Htmls')->orderBy('path', 'ASC')->findall();
foreach($htmls as $html) {
	$nav[] = ["/setup/help/view/{$html->id}", $html->path];
}
echo (new \App\Views\Htm\Navbar($nav))->htm();

$this->endSection();
