<?php 
printf('<template id="template-%s">', $exeset->rulesetname);

$exeset_fields = [
	'rulesetname' => "rulesetname", 
	'name' => "name", 
	'event' => "event"
]; 
$tab_items = [];
foreach($exeset->exercises as $exekey=>$exercise) {
	ob_start();
	# d($exercise);
	$exe_rules = $exeset->ruleset->exes[$exekey] ?? [] ;
	
	switch($exe_rules['method']) {
		case 'tariff':
			$inputs = [
				[
					'type' => "number",
					'step' => "0.1",
					'min' => "0",
					'max' => $exe_rules['d_max'],
					'class' => "form-control tarrif-0",
					'placeholder' => 'tariff'
				],
				[
					'type' => 'select',
					'options' => $exeset->ruleset->select_options('tarrif.groups'),
					'class' => "form-control tarrif-1",
					'placeholder' => 'grp'
				],
				[
					'type' => 'text',
					'class' => "form-control tarrif-2",
					'placeholder' => 'description'
				]
			];
			$dismount_num =  999;
			break;
		case 'routine':
		default: 
			$inputs = [
				[
					'type' => 'select',
					'options' => $exeset->ruleset->select_options('routine.difficulties'),
					'class' => "form-control routine-0",
					'placeholder' => 'val'
				],
				[
					'type' => 'select',
					'options' => $exeset->ruleset->select_options('routine.groups'),
					'class' => "form-control routine-1",
					'placeholder' => 'grp'
				],
				[
					'type' => 'text',
					'class' => "form-control routine-2",
					'placeholder' => 'description'
				]
			];
			$dismount_num = array_key_last($exercise['elements']); 
	}

	$last_elnum = array_key_last($exercise['elements']); 
	foreach($exercise['elements'] as $elnum=>$element) {
		$class = '';
		if($elnum) $class .= ' not-first';
		if($elnum<$last_elnum) $class .= ' not-last';
		?>
		<div class="input-group my-0">
		<span class="input-group-text elnum<?php echo $class;?>">
			<?php echo $elnum==$dismount_num ? 'D' : $elnum + 1; ?>
		</span>
		<?php
		foreach($inputs as $col=>$input) {
			$input['name'] = "{$exekey}_el_{$elnum}_{$col}";
			$input['value'] = $element[$col];
			$input['class'] .= $class;
			$exeset_fields[$exekey]['elements'][$elnum][$col] = $input['name'];

			switch($input['type']) {
				case 'select': 
					unset($input['type']);
					echo form_dropdown($input);
					break;
				default:
					echo form_input($input);
			}
		}
		?>
		</div>
	<?php }
	
	if(!empty($exe_rules['connection'])) { ?>
		<div class="input-group my-1" style="max-width:14em">
		<span class="input-group-text">Connection</span>
		<?php 
		$id = "{$exekey}_con";
		$input = [
			'type' => "number",
			'step' => "0.1",
			'min' => "0",
			'class' => "form-control",
			'name' => $id,
			'id' => $id,
			'value' => $exercise['connection'] ?? 0
		];
		$exeset_fields[$exekey]['connection'] = $input['name'];
		echo form_input($input);
		?>
		</div>
	<?php } ?>
	
	<div class="my-2">
	<?php foreach($exercise['neutrals'] as $nkey=>$nval) { ?>
		<div class="form-check">
		<?php
		$id = "{$exekey}_nd_{$nkey}";
		$input = [
			'type' => 'checkbox',
			'name' => $id,
			'id' => $id,
			'value' => 1,
			'class' => "form-check-input"
		];
		if($nval) $input['checked'] = 'checked';
		$exeset_fields[$exekey]['neutrals'][$nkey] = $input['name'];
		$neutral = $exe_rules['neutrals'][$nkey]; 
		
		$attr = [
			'class' => "form-check-label"
		];
	
		echo form_input($input);
		echo form_label(sprintf('%s (%1.1f)', $neutral['description'], $neutral['deduction']), $id, $attr);
		?>
		</div>
	<?php } ?>
	</div>
	
	<div class="exeval"><!-- filled by API --></div>
	
	<?php 
	$tab_items[$exekey] = [
		'heading' => $exe_rules['name'],
		'content' => ob_get_clean()
	];
}

$tabs = new \App\Views\Htm\Tabs($tab_items, 'exes');
echo $tabs->htm();

# d($exeset);
# d($exeset_fields);

?>
</template>
<script>
exesets_tmpl['<?php echo $exeset->rulesetname;?>'] = {
	name: '<?php echo $exeset->rulesetname;?>',
	fields: <?php echo json_encode($exeset_fields) ?>,
	exekeys: <?php echo json_encode(array_keys($exeset->exercises));?>
};
</script>