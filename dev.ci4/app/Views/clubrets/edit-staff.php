<div id="staff">
<p>Staff details should be entered as: <?php echo \App\Libraries\Namestring::hint;?>. Each piece of information is separated by a comma. Each staff member should be entered in a separate box.</p>
<?php echo $event->staff;

$staff = $clubret->staff;
# d($staff);
if(!$staff) { // provide one blank entry 
	$staff = [[
		'cat' => '', 'name' => ''
	]];
}

$options = [];
foreach($event->staffcats as $val) $options[$val] = humanize($val);

$inputs = [
	'cat' => [
		'options' => $options, 
		'class' => 'form-control'
	],
	'name' => $names_edit
];
foreach(array_keys($inputs) as $key) {
	$inputs[$key]['data-field'] = $key;
}

$tbody = []; 
foreach($staff as $rowkey=>$row) {
	$inputs['cat']['selected'] = $row['cat'];
	if($row['name']) {
		$namestring = new \App\Libraries\Namestring($row['name']);
		$inputs['name']['value'] = (string) $namestring;
	}
	else $inputs['name']['value'] = '' ;

	$tbody[] = [
		'#' => [
			'class' => "pid",
			'data' => $rowkey + 1
		],
		'cat' => form_dropdown($inputs['cat']),
		'name' => form_input($inputs['name']),
		'del' => '<button onclick="editform.delstaff(this)" type="button" class="btn bi-trash btn-danger btn-sm"></button>'
	];
}

$table->setHeading(['', 'category', 'name', '']);

printf('<div class="clubent">%s</div>', $table->generate($tbody));
echo form_hidden('staff', json_encode($staff));
?>
<button onclick="editform.addstaff()" type="button" class="btn btn-success bi-person-plus-fill"></button>
<?php echo $clubret->errors('staff'); ?>
</div>

<?php if($event->stafffee) { ?>
<p class="mt-2 form-check">
<?php 
$input = [
	'class' => "form-check-input",
	'name' => "stafffee",
	'type' => "checkbox",
	'value' => "1",
	'id' => "chkstafffee"
];
if($clubret->stafffee) $input['checked'] = "checked";
echo form_input($input);
?>
<label class="form-check-label" for="chkstafffee">This club will meet all staff requirements for this event (<?php echo number_to_currency($event->stafffee); ?> will be added to your entry fee as "staff" if not).</label>
</p>
<?php } 