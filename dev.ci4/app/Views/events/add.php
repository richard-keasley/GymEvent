<?php $this->extend('default');

$this->section('content'); 

$attrs = [
	'class' => "vars"
];
$hidden = [
	'save' => '1'
];
echo form_open('', $attrs, $hidden); ?>

<p class="input-group">
  <label class="input-group-text">title</label>
	<?php echo form_input("title", $event->title ?? '', 'class="form-control"');?>
</p>

<p class="input-group">
  <label class="input-group-text">date</label>
	<?php echo form_input("date", $event->date ?? '', 'class="form-control"', 'date');?>
</p>

<div class="my-3"><?php 
$attrs = [
	'name' => 'description',
	'value' => $event->description ?? ''
];
echo new \App\Views\Htm\Editor($attrs);
?></div>

<div class="toolbar">
	<?php echo \App\Libraries\View::back_link("admin/events"); ?>
	<button class="btn btn-primary" name="save" type="submit" value="1">create</button>
</div>

<?php 
echo form_close();
# d($event);
$this->endSection(); 
