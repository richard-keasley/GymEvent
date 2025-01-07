<?php 
# return;

$exe_rules = $exeset->ruleset->$exekey;
$skills = $exeset->ruleset->skills($exekey);
# d($skills);
# d($skills['skills']);

$keys = match($skills['method']) {
	'tariff' => ['description', 'tariff'],
	'routine' => ['description', 'difficulty'],
	default => []
};
$buffer = [];
foreach($skills['skills'] as $skill) {
	$grp_id = $skill['group'];
	$keys = match($skills['method']) { 
		'tariff' => ['tariff', 'group', 'description'],
		'routine' => ['difficulty', 'group', 'description'],
		default => null
	};
	if($keys) {
		$data = [];
		foreach($keys as $key) $data[] = $skill[$key];
		$label = match($skills['method']) {
			'tariff' => sprintf('%s (%s)', $data[2], $data[0]),
			'routine' => sprintf('%s <strong>(%s)</strong>', $data[2], $data[0])
		};

		$div = [
			'class' => "col-sm-6 col-lg-4 col-xl-3",	
		];
		$button = [
			'class' => "bg-light m-1 border-1 border-secondary text-start w-100",
			'onclick' => "esedit.selector.apply(this)",
			'type' => "button",
			'data-skill' => implode('|', $data)
		];
		
		$buffer[$grp_id][] = sprintf('<div %s><button %s>%s</button></div>', 
			\stringify_attributes($div),
			\stringify_attributes($button), 
			new \App\Views\Htm\Pretty($label),
		);
	}
}

$id = "sel-{$exeset->ruleset->name}-{$exekey}";
?>
<div class="modal fade" id="dlg<?php echo $id;?>" tabindex="-1">
<div class="modal-dialog modal-xl modal-fullscreen-lg-down">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Skill selector: <?php echo $exekey;?></h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
  
<div class="modal-body">
<div class="input-group my-1" title="filter results">
<span class="input-group-text bi-filter"></span>
<input type="text" class="form-control" id="filter1" placeholder="filter">
<script>
$(document).ready(function() {
$("#filter1").on("keyup", function() {
	var value = $(this).val().toLowerCase();
	$(".accordion-body button").filter(function() {
		$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
	});
});
});
</script> 
</div>
<?php
$items = [];
foreach($buffer as $grp=>$grp_skills) {
	$heading = $exe_rules['group_labels'][$grp] ?? '' ;
	if($heading) $heading = ": {$heading}";

	$content = implode('', $grp_skills);

	$items[] = [
		'heading' => "Group {$grp}{$heading}",
		'content' => sprintf('<div class="row">%s</div>', $content),
	];
}
echo new \App\Views\Htm\Accordion($items, "acc{$id}");
?></div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
</div>
</div>
</div>
</div>
