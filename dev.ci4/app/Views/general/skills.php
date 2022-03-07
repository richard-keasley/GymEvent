<?php $this->extend('default');
 
$this->section('content'); 
foreach($skills->get_grouped() as $group_id=>$skill_arr) { ?>
	<h5 class="fw-bold">Group <?php echo $group_id;?></h5>
	<div class="row justify-content-start">
	<?php foreach($skill_arr as $difficulty=>$skills) { ?>
		<div class="col-auto">
		<ul class="list-unstyled">
		<li><h6 class="fw-bold bg-light"><?php echo $difficulty;?></h6></li>
		<?php foreach($skills as $skill) { ?>
			<li class="text-nowrap"><?php 
			echo $skill['description'];
			foreach(\App\Libraries\General\Skills::attributes as $attr) {
				if($skill[$attr]) printf(' <span class="badge bg-info">%s</span>', $attr);
			}
			?></li> 
		<?php } ?>
		</ul>
		</div>
	<?php } ?>
	</div>
	<?php
} 
$this->endSection(); 

$this->section('top');?>
<div class="toolbar sticky-top"> 
	<?php echo \App\Libraries\View::back_link($back_link); ?>
</div>
<?php $this->endSection(); 
