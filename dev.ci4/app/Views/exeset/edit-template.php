<?php 
printf('<template id="template-%s">', $exeset->rulesetname);

$exeset_fields = [
	'rulesetname' => "rulesetname", 
	'name' => "name", 
	'event' => "event",
	'club' => "club",
]; 
$tab_items = [];
$selectors = [];
foreach($exeset->exercises as $exekey=>$exercise) {
	ob_start();
	$exe_rules = $exeset->ruleset->$exekey;
	$selector = $exe_rules['selector'] ?? false;
	if($selector) $selectors[] = $exekey;
	# d($exe_rules);

	switch($exe_rules['method']) {
		case 'tariff':		
		$inputs = [
			[
				'type' => "number",
				'step' => "0.1",
				'min' => $exe_rules['d_min'],
				'max' => $exe_rules['d_max'],
				'class' => "form-control tariff-0",
				'placeholder' => 'tariff'
			],
			[
				'type' => 'select',
				'options' => $exeset->ruleset->exe_options($exe_rules, 'groups'),
				'class' => "form-control tariff-1",
				'placeholder' => 'grp'
			],
			[
				'type' => 'text',
				'class' => "form-control tariff-2",
				'placeholder' => 'description'
			]
		];
		break;
		
		case 'routine':
		default:
		$inputs = [
			[
				'type' => 'select',
				'options' => $exeset->ruleset->exe_options($exe_rules, 'difficulties'),
				'class' => "form-control routine-0",
				'placeholder' => 'val'
			],
			[
				'type' => 'select',
				'options' => $exeset->ruleset->exe_options($exe_rules, 'groups'),
				'class' => "form-control routine-1",
				'placeholder' => 'grp'
			],
			[
				'type' => 'text',
				'class' => "form-control routine-2",
				'placeholder' => 'description'
			]
		];
	}
		
	$has_dismount = $exe_rules['dismount'] ?? false;
	$last_elkey = array_key_last($exercise['elements']); 
	foreach($exercise['elements'] as $elkey=>$element) {
		$class = '';
		if($elkey) $class .= ' not-first';
		if($elkey<$last_elkey) $class .= ' not-last';
		$elnum = $has_dismount && $elkey==$last_elkey ? 
			'D' : 
			$elkey + 1;
		?>
		<div class="input-group my-0">
		<span class="input-group-text elnum<?php echo $class;?>">
			<?php echo $elnum; ?>
		</span>
		<?php
		foreach($inputs as $col=>$input) {
			$input['name'] = "{$exekey}_el_{$elkey}_{$col}";
			$input['value'] = $element[$col];
			$input['class'] .= $class;
			$exeset_fields[$exekey]['elements'][$elkey][$col] = $input['name'];

			switch($input['type']) {
				case 'select': 
				unset($input['type']);
				echo form_dropdown($input);
				break;
				
				default:
				echo form_input($input);
			}
		}
		
		if($selector) {
			$attrs = [
				'class' => "btn bg-primary-subtle d-print-none {$class}",
				'type' => "button",
				'onclick' => "esedit.selector.show('{$exeset->ruleset->name}','{$exekey}',{$elkey})"
			];
			printf('<button %s><span class=" bi-box-arrow-in-down"></span></button>', stringify_attributes($attrs));;
		} 

		?>
		</div>
	<?php } ?>

	<div class="row my-1">
	
	<?php if($exe_rules['connection']) { ?>
	<div class="col-auto">
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
	</div>
	<?php } ?>
	
	<?php if($exe_rules['neutrals']) { ?>	
	<div class="col-auto"><?php
	foreach($exe_rules['neutrals'] as $nkey=>$neutral) { ?>
		<div class="form-check">
		<?php
		$id = "{$exekey}_nd_{$nkey}";
		$checked  = $exercise['neutrals'][$nkey] ?? false;
		$input = [
			'type' => 'checkbox',
			'name' => $id,
			'id' => $id,
			'value' => 1,
			'class' => "form-check-input"
		];
		if($checked) $input['checked'] = 'checked';
		$exeset_fields[$exekey]['neutrals'][$nkey] = $input['name'];
		
		$attr = [
			'class' => "form-check-label"
		];
	
		echo form_input($input);
		echo form_label(sprintf('%s (%1.1f)', $neutral['description'], $neutral['deduction']), $id, $attr);
		?>
		</div>
		<?php
	}
	?></div>
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
foreach($selectors as $exekey) {
	include __DIR__ . '/edit-selector.php';
}
?>
</template>
<script>
exesets_tmpl['<?php echo $exeset->rulesetname;?>'] = {
	name: '<?php echo $exeset->rulesetname;?>',
	fields: <?php echo json_encode($exeset_fields) ?>,
	exekeys: <?php echo json_encode(array_keys($exeset->exercises));?>
};
</script>