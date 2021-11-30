<?php $this->extend('default');

$this->section('content'); 

$attr = [
	'class' => "vars"
];
echo form_open(base_url(uri_string()), $attr); ?>
<input type="hidden" name="save" value="1">

<p class="input-group">
  <label class="input-group-text">title</label>
	<?php echo form_input("title", $event->title, 'class="form-control"');?>
</p>

<p class="input-group">
  <label class="input-group-text">date</label>
	<?php echo form_input("date", $event->date, 'class="form-control"', 'date');?>
</p>

<p><label>Description [HTML]</label> <?php echo form_textarea("description", $event->description, 'class="form-control"');?></p>

<div class="toolbar">
	<?php echo \App\Libraries\View::back_link("admin/events"); ?>
	<button class="btn btn-primary" name="save" type="submit" value="1">create</button>
</div>

</form>
<?php $this->endSection(); 
