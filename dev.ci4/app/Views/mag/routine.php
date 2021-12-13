<?php $this->extend('default');
$ruleset = $routineset->ruleset;
 
$this->section('content');?>
<h3><?php echo $routineset->name;?></h3>
<div class="mb-3"><?php
	$input = [
		'class' => "form-control", 
		'id' => "ruleset",
		'name' => "ruleset",
		'options' => \App\Libraries\Mag\Rules::index()
	];
	echo form_dropdown($input);?>

	
</div>



<p style="white-space:pre;"><?php echo $routineset->event;?></p>
<?php 
$items = [];
foreach(\App\Libraries\Mag\Rules::apparatus as $appkey=>$heading) {
	ob_start();
	$routine = empty($routineset->routines[$appkey]) ? [] : $routineset->routines[$appkey];
	
	$elements = empty($routine['elements']) ? [] : $routine['elements'];
	$inputs = [
		[
			'name' => '',
			'style' => "max-width:3em",
			'class' => "form-control",
			'placeholder' => 'val'
		],
		[
			'name' => '',
			'style' => "max-width:3em",
			'class' => "form-control",
			'placeholder' => 'grp'
		],
		[
			'name' => '',
			'class' => "form-control",
			'placeholder' => 'description'
		]
	];
	for($elnum=0; $elnum<$ruleset->count; $elnum++) { ?>
		<div class="input-group my-1">
		<span class="input-group-text" style="width:3em"><?php echo $elnum+1;?></span>
		<?php
		$element = empty($elements[$elnum]) ? [] : $elements[$elnum] ;
		$element = array_pad($element, count($inputs), '');
		foreach($inputs as $col=>$input) {
			$input['name'] = "{$appkey}_el_{$elnum}_{$col}";
			$input['value'] = $element[$col];
			echo form_input($input);
		}
		?>
		</div>
	<?php }
	
	$neutral = empty($routine['neutral']) ? [] : $routine['neutral'];
	
	foreach($ruleset->neutral_deductions[$appkey] as $key=>$deduction) { ?>
		<div class="form-check">
		<?php
		$id = "{$appkey}_n_{$key}";
		$input = [
			'type' => 'checkbox',
			'name' => $id,
			'id' => $id,
			'value' => 1,
			'class' => "form-check-input"
		];
		$label = [
			'class' => "form-check-label"
		];
		if(!empty($neutral[$key])) $input['checked'] = 'checked';
		echo form_input($input);
		echo form_label(sprintf('%s (%1.1f)', $deduction['description'], $deduction['deduction']), $id, $label);
		?>
		</div>
	<?php }
	
	
	$items[] = [
		'heading' => $heading,
		'content' => ob_get_clean()
	];
}
$tabs = new \App\Libraries\Ui\Tabs($items);
echo $tabs->htm();
 ?>



<?php d($ruleset);?>
<?php d($routineset);?>

<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link("mag");?>
</div>
<?php $this->endSection(); 
