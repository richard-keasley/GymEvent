<?php $this->extend('memdb/default');
$table = new \CodeIgniter\View\Table();
$table->setTemplate(['table_open' => '<table class="table table-sm">']);

$this->section('content'); 

$attr = [
	'id' => "editform",
	'autocomplete' => "off",
	'style' => "max-width:40em;"
];
echo form_open(base_url(uri_string()), $attr); ?>
<fieldset class="bg-light py-2">
<div class="my-1 row">
	<label for="ctrl-name1" class="text-end col-form-label col-sm-3">name</label>
	<div class="col-sm-4">
		<input type="text" name="name1" value="<?php echo $postvar['name1'];?>" class="form-control" placeholder="name1" id="ctrl-name1">
	</div>
	<div class="col-sm-4">
		<input type="text" name="name2" value="<?php echo $postvar['name2'];?>" class="form-control" placeholder="name2" id="ctrl-name2">
	</div>
</div>
<div class="my-1 row">
	<label for="ctrl-email" class="text-end col-form-label col-sm-3">email</label>
	<div class="col-sm-8">
		<input type="email" name="email" value="<?php echo $postvar['email'];?>" class="form-control" placeholder="email" id="ctrl-email">
	</div>
</div>
<div class="my-1 row">
	<label for="ctrl-group" class="text-end col-form-label col-sm-3">group</label>
	<div class="col-sm-8">
		<?php echo form_dropdown('group', $groups, $postvar['group']);?>
	</div>
</div>
</fieldset>

<div class="my-1">
	<button class="btn btn-primary" type="submit">Create</button>
</div>
<?php echo form_close(); 

$attr = ['id' => "listform"];
echo form_open(base_url(uri_string()), $attr);

$tbody = [];
foreach($invites as $key=>$invite) {
	$btns = sprintf('<div style="width:6em;"><a href="%s" class="btn btn-sm bi-envelope-fill"></a><a href="%s" class="btn btn-sm bi-person-circle"></a><button type="submit" name="del" value="%s" class="btn btn-sm bi-trash"></button></div>', base_url("memdb/email/{$key}"), base_url("memdb/enrol/{$key}"), $key);
	$tbody[] = ['key'=>$key] + $invite + ['btns' => $btns];
}
$thead = ['key', 'name1', 'name2', 'email', 'group', 'created', 'replied', ''];
$table->setHeading($thead);
echo $table->generate($tbody);
echo form_close(); 
?>

<?php $this->endSection();
