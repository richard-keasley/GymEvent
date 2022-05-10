<?php $this->extend('default');
 
$this->section('content');
$attr = [
	'id' => "editform"
];
echo form_open(base_url(uri_string()), $attr); 

foreach($fields as $fldname=>$fldtype) { ?>
	<div class="input-group my-1">
	<label class="input-group-text"><?php echo $fldname;?></label>
	<?php 
	$input = [
		'name' => $fldname,
		'class' => "form-control",
		'type' => $fldtype,
		'value' => $data[$fldname] ?? ''
	];
	echo form_input($input);
	?>
	</div>
<?php } 
?>
<div class="toolbar"> 
	<?php echo \App\Libraries\View::back_link($back_link); ?>
	<button class="btn btn-primary" type="submit" name="save" value="1">save</button>
</div>


<?php 
echo form_close();

# d($data);
# d($fields);

$this->endSection();
