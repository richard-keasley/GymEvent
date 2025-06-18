<?php $this->extend('default');

$this->section('content'); 

$role_opts = [];
foreach(\App\Libraries\Auth::roles as $role) {
	if(\App\Libraries\Auth::check_role($role)) $role_opts[$role] = $role;
}

$inputs = [
'name' => [
	'type' => 'text',
	'class' => 'form-control',
	'value' => $user->name,
	'placeholder' => 'User name'
],
'abbr' => [
	'label' => 'Short name',
	'type' => 'text',
	'class' => 'form-control',
	'value' => $user->abbr,
	'placeholder' => 'Short name'
],
'email' => [
	'label' => 'Email address',
	'type' => 'email',
	'class' => 'form-control',
	'value' => $user->email,
	'placeholder' => 'Email address'
],
'password' => [
	'type' => 'password',
	'class' => 'form-control',
	'autocomplete' => 'new-password',
	'value' => ''
],
'password2' => [
	'label' => 'Password (repeat)',
	'type' => 'password',
	'class' => 'form-control',
	'value' => ''
],
'role' => [
	'type' => 'select',
	'class' => 'form-control',
	'selected' => $user->role,
	'options' => $role_opts
]
];

if($user_self) {
	$inputs['role']['disabled'] = 'disabled';
}

$attrs = [
	'id' => "editform",
	'autocomplete' => "off",
	'style' => "max-width:28em;"
];
echo form_open('', $attrs);
foreach($inputs as $key=>$input) {
	$input['id'] = "ctrl-$key";
	$input['name'] = $key;
	if(isset($input['label'])) {
		$label = $input['label'];
		unset($input['label']);
	}
	else $label = ucfirst($key);
	?>
	<div class="my-1 row"><?php
		$label_type = $input['type']=='checkbox' ? 'col-form-check-label' : 'col-form-label' ;
		$attr = [
			'class' => "$label_type col-sm-4"
		];
		echo form_label($label, $input['id'], $attr);
		?>
		<div class="col-sm-8">
		<?php
		switch($input['type']) {
			case 'checkbox':
				if($input['value']) $input['checked'] = 'checked';
				$input['value'] = 1;
				printf('<div class="form-check form-switch">%s</div>', form_input($input));
				break;
			case 'select':
				echo form_dropdown($input);
				break;
			default:
				echo form_input($input);
		}
		?>
		</div>
	</div>
	<?php
} 
echo form_close();
$this->endSection(); 

$this->section('bottom');?>
<div class="toolbar">
<?php echo implode(' ', $toolbar); ?> 
<button form="editform" class="btn btn-primary" name="save" value="1" type="submit">save</button>
</div>
<?php $this->endSection(); 
