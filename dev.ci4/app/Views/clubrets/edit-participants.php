<div id="participants">
<section>
<p>Gymnasts' details are entered as: <?php echo \App\Libraries\Namestring::hint;?>. Each piece of information is separated by a comma.</p>
<p>Each gymnast occupies a separate line.</p>
<p>Each entry is in a separate box.</p>
<p>If an entry comprises more than one gymnast (e.g. Acro and Team-gym), enter each gymnast on a separate line <em>within the same box</em>.</p>
<?php echo $event->participants; ?>
</section>
<?php
$tbody = []; $tr = []; 
$participants = $clubret->participants;
if(!$participants) { // provide one blank participant 
	$participants = [[
		'dis'=>'', 'cat'=>[], 'team'=>'', 'names'=>[] 
	]];
}

$inputs = [
	'dis' => [
		'options' => $dis_opts,
		'class' => 'form-control',
		'style' => 'min-width:4em;'
	],
	'cat' => [
		'class' => 'form-control',
		'style' => 'min-width:4em;'
	],
	'team' => [
		'class' => 'form-control',
		'placeholder' => 'Team name',
		'style' => 'min-width:8em;'
	],
	'names' => $names_edit,
	'opt' => [
		'class' => 'form-control',
		'style' => 'min-width:5em;'
	]
];

foreach(array_keys($inputs) as $key) {
	$inputs[$key]['data-field'] = $key;
}

foreach($participants as $rowkey=>$row) {
	$inputs['dis']['selected'] = $row['dis'];
	$inputs['team']['value'] = $row['team'];
	$inputs['names']['value'] = implode("\n", $row['names']);

	$tr['#'] = [
		'class' => "pid",
		'data' => $rowkey + 1
	];

	$tr['discat'] = '<div class="input-group">';
	$tr['discat'] .= form_dropdown($inputs['dis']);
	foreach($discats as $discat) {
		$inputs['cat']['data-dis'] = $discat['name'];
		foreach($discat['cats'] as $cat_key=>$options) {
			$inputs['cat']['options'] = $options;
			$inputs['cat']['selected'] = $row['cat'][$cat_key] ?? '-' ;
			$tr['discat'] .= form_dropdown($inputs['cat']);
		}
	}
	$tr['discat'] .= '</div>';
	
	$tr['team'] = form_input($inputs['team']);
	$tr['names'] = form_textarea($inputs['names']); 
	$tr['opt'] = '';

	$input = $inputs['opt'];
	foreach($discats as $discat) {
		$input['data-dis'] = $discat['name'];
		$input['options'] = $discat['options'];
		$input['selected'] = $row['opt'] ?? '';
		if($input['options']) $tr['opt'] .= form_dropdown($input);
	}

	$tr['del'] = '<button onclick="editform.delpart(this)" type="button" class="btn bi-trash btn-danger btn-sm"></button>';
	$tbody[] = $tr;	
}

$table->setHeading(['','category','',"Gymnasts' details",'']);
printf('<div class="clubent">%s</div>', $table->generate($tbody));
echo form_hidden('participants', json_encode($participants));

?>
<button onclick="editform.addpart()" type="button" class="btn btn-success bi-person-plus-fill"></button>
<?php 

echo $clubret->errors('participants', 6);

echo $this->include('events/_terms');

?>
</div>
