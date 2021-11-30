<?php $this->extend('memdb/default');
$member_name = sprintf('%s %s', $postvar['name1'], $postvar['name2']);

#print_r($invite);
#echo "\$keys = ['" . implode("', '", array_keys($_POST)) . "'];"

$this->section('content'); 
echo form_open(base_url(uri_string())); ?>
<h3>Welcome to Hawth Gymnastics</h3>
<p>Please complete the information required for <?php echo $member_name;?> to start their new class with us. Please read the questions carefully and fill in the form below.</p>

<p>We should remind you this place is for <?php echo $member_name;?> and can not be transferred to someone else. If you have not been offered a space, filling in this form will not get you one!</p>

<p>This offer is for a place in our <?php echo $group;?>. Their first session will be on <?php echo date('d M H:i', time());?>.</p>

<p>We use the personal information you provide to identify an appropriate class for you/your child and to ensure you/they are well supported and safe whilst participating in gymnastics. All personal information will be held securely and will only be shared with coaches or others who need this information to provide gymnastics activity and meet your/your child’s needs. If you would like more information on how we use information about you/your child please <a href="mailto:office@hawthgymnastics.co.uk">contact us</a>.</p>

<fieldset class="border p-3 my-2"><legend>About the new member</legend>
<p>Please tell us about the one who will be joining the club.</p>

<div class="my-3">
<label class="form-label">Name</label>
<div class="row">
<?php 
foreach(['name1','name2'] as $fldname) {
	$input = [
		'class'=>"form-control",
		'name' => $fldname,
		'value' => $postvar[$fldname],
		'placeholder' => $fldname
	];
	printf('<div class="col">%s</div>', form_input($input));
} ?>
</div>
</div>

<div class="row my-3">
<div class="col">
<label class="form-label">Gender</label>
<?php 
$fldname = 'gender';
$dropdown = [
	'class' => "form-control",
	'name' => $fldname,
	'selected' => $postvar[$fldname],
	'options' => ['-', 'M'=>'male', 'F'=>'female']
];
echo form_dropdown($dropdown);?>
</div>

<div class="col">
<label class="form-label text-nowrap">Date Of Birth</label>
<?php $fldname = 'DoB';
$input = [
	'class'=>"form-control",
	'name' => $fldname,
	'value' => $postvar[$fldname],
	'placeholder' => $fldname,
	'type' => 'date'
];
echo form_input($input);?>
</div>

<div class="col">
<label class="form-label text-nowrap">Ethnic group</label>
<?php 
$fldname = 'ethnic';
$dropdown = [
	'class'=>"form-control",
	'name' => $fldname,
	'selected' => $postvar[$fldname],
	'options' => $ethnics
];
echo form_dropdown($dropdown);?>
</div>

</div>

<p>
<label class="col-form-label">Do you consider the child to have a disability? If so, please describe the nature of this disability here</label>
<?php 
$fldname = 'SpecialNeeds';
$textarea = [
	'class'=>"form-control",
	'name' => $fldname,
	'value' => $postvar[$fldname],
	'rows' => 3
];
echo form_textarea($textarea);?>
</p>

<p>
<label class="col-form-label">Please state any medical conditions that our coaches may need to be aware of</label>
<?php 
$fldname = 'MedicalNotes';
$textarea = [
	'class'=>"form-control",
	'name' => $fldname,
	'value' => $postvar[$fldname],
	'rows' => 3
];
echo form_textarea($textarea);?>
</p>

<p>
<label class="col-form-label">If there is any other information that you feel would assist us in the coaching of your child, please let us know.</label>
<?php 
$fldname = 'MemberNotes';
$textarea = [
	'class'=>"form-control",
	'name' => $fldname,
	'value' => $postvar[$fldname],
	'rows' => 3
];
echo form_textarea($textarea);?>
</p>

<p>
<label class="col-form-label">Does your child have any British Gymnastics Proficiency Award Badges? If so, which ones and where were they obtained?</label>
<?php 
$fldname = 'BadgeNotes';
$textarea = [
	'class'=>"form-control",
	'name' => $fldname,
	'value' => $postvar[$fldname],
	'rows' => 3
];
echo form_textarea($textarea);?>
</p>

</fieldset>

<fieldset class="border p-3 my-2"><legend>About You (the one paying the fees)</legend>
<p>Our primary method of communication is email. We have had issues with our emails not getting through to Hotmail addresses, so we prefer you to use an alternative. We will use the main phone number you specify in case of an emergency.</p>

<div class="row my-3" >
<?php
$fldname = 'contact_name';
$input = [
	'class' => "form-control",
	'style' => "min-width:8em;",
	'name' => $fldname,
	'value' => $postvar[$fldname],
	'placeholder' => 'contact name'
];
printf('<div class="col">%s</div>', form_input($input));
$fldname = 'contact_rel';
$input['name'] = $fldname;
$input['value'] = $postvar[$fldname];
$input['placeholder'] = 'relationship';
printf('<div class="col">%s</div>', form_input($input));
$fldname = 'contact_con';
$input['name'] = $fldname;
$input['value'] = $postvar[$fldname];
$input['placeholder'] = 'main email address';
$input['type'] = 'email';
printf('<div class="col">%s</div>', form_input($input));
?>
</div>

<h6>Do you already have children in the Club?</h6>
<?php 
$tabnames = ['create'=>'No', 'exists'=>'Yes'];
$tabs = new \App\Libraries\Ui\Tabs($tabnames);
$tabs->format['tabs_start'] = '<nav class="nav nav-pills" role="tablist">';
echo $tabs->tabs(); ?>
<section class="bg-light p-2 my-2">

<?php echo $tabs->panel_start('create'); ?>
<div class="my-3">
<label class="form-label">Parent/Guardian name</label>
<div class="row">
<?php 
$fldname = 'Accountname1';
$input = [
	'class' => "form-control",
	'style' => "min-width:7em;",
	'name' => $fldname,
	'value' => $postvar[$fldname],
	'placeholder' => '1st name'
];
printf('<div class="col">%s</div>', form_input($input));
$fldname = 'Accountname2';
$input = [
	'class' => "form-control",
	'style' => "min-width:7em;",
	'name' => $fldname,
	'value' => $postvar[$fldname],
	'placeholder' => '2nd name'
];
printf('<div class="col">%s</div>', form_input($input));
?>
</div>
</div>

<p>
<label class="col-form-label">Postal Address</label>
<?php 
$fldname = 'Address';
$textarea = [
	'class'=>"form-control",
	'name' => $fldname,
	'value' => $postvar[$fldname],
	'rows' => 4
];
echo form_textarea($textarea);
?>
</p>

<section>
<h5>Contact details</h5>
<?php 
$inputs = [
	[
		'type' => 'text',
		'placeholder' => 'main phone',
		'class' => 'form-control',
		'style' => "min-width:8em;"
	],
	[
		'type' => 'email',
		'placeholder' => '2nd email address',
		'class' => 'form-control',
		'style' => "min-width:8em;"
	],
	[
		'type' => 'text',
		'placeholder' => "2nd phone",
		'class' => 'form-control',
		'style' => "min-width:8em;"
	],
	[
		'type' => 'text',
		'placeholder' => "3rd phone",
		'class' => 'form-control',
		'style' => "min-width:8em;"
	]
];
$name = [
	'type' => 'text',
	'placeholder' => 'contact name',
	'class' => 'form-control',
	'style' => "min-width:8em;"
];
$rel = [
	'type' => 'text',
	'title' => 'relationship to member',
	'placeholder' => 'relationship',
	'class' => 'form-control',
	'style' => "min-width:8em;"
];
foreach($inputs as $key=>$input) { 
	$name['name'] = "contact_{$key}_name";
	$name['value'] = $postvar["contact_{$key}_name"];
	$rel['name'] = "contact_{$key}_rel";
	$rel['value'] = $postvar["contact_{$key}_rel"];
	$input['name'] = "contact_{$key}_con";
	$input['value'] = $postvar["contact_{$key}_con"];
?>
<div class="row my-1">
	<div class="col"><?php echo form_input($name);?></div>
	<div class="col"><?php echo form_input($rel);?></div>
	<div class="col"><?php echo form_input($input);?></div>
</div>
<?php } ?>
</section>

<?php echo $tabs->panel_start('exists'); ?>
<p>We will use our existing contact details for this new member</p>
<div class="my-3 row">
<label class="col-form-label">Please enter the name of the existing Club member.</label>
<?php 
$fldname = 'AccountSearch1';
$input = [
	'class'=>"form-control",
	'name' => $fldname,
	'value' => $postvar[$fldname],
	'placeholder' => 'Name 1'
];
printf('<div class="col">%s</div>', form_input($input));
$fldname = 'AccountSearch2';
$input = [
	'class'=>"form-control",
	'name' => $fldname,
	'value' => $postvar[$fldname],
	'placeholder' => 'Name 2'
];
printf('<div class="col">%s</div>', form_input($input));
?>
</div>

<?php echo $tabs->content_end(); ?>
</section>

<p>
<label class="form-label">How will/were your class fees paid? More information can be found on our <a href="https://www.hawthgymnastics.co.uk/how-to-pay" target="_blank">How to Pay</a> page.</label>
<?php 
$fldname = 'pay_method';
$input = [
	'class'=>"form-control",
	'name' => $fldname,
	'selected' => $postvar[$fldname],
	'options' => [
		0 => 'please select',
		'cash' => 'Cash (at the Hawth office @ K2)',
		'bacs' => 'Online bank transfer'
	]
];
echo form_dropdown($input);?>
</fieldset>

<fieldset class="border p-3 my-2"><legend>Hawth's Policies</legend>
<?php 
$checkbox = [
	'class' => "form-check-input",
	'value' => "1"
];
?>
<section>
<h5>Participation Agreements</h5>
<p class="form-check"><?php
$id = 'agree0'; 
$checkbox['id'] = $id;
$checkbox['name'] = $id;
echo form_checkbox($checkbox, $id, $postvar[$id]);
?>
<label class="form-check-label" for="<?php echo $id;?>">I confirm that to the best of my knowledge, the participant is physically fit and healthy and am aware of no other information which needs to be considered in advance to ensure safe participation in gymnastics.</label>
</p>

<p class="form-check"><?php
$id = 'agree1'; 
$checkbox['id'] = $id;
$checkbox['name'] = $id;
echo form_checkbox($checkbox, $id, $postvar[$id]);
?>
<label class="form-check-label" for="<?php echo $id;?>">I agree for the information I have provided to be used for carrying out risk assessments and reasonable adjustments and understand that the club may contact me if they require any further information.</label>
</p>
</section>

<section>
<h5>Marketing and communication</h5>
<p>We use email for most of our communications. We will contact you in this way to inform you of changes to your class arrangements and to remind you of any outstanding fees. We will not share your contact details.</p>

<p>For further details about how we will use information about you, please see our terms and conditions and privacy notice on our
<a href="https://www.hawthgymnastics.co.uk/downloads" target="_blank">website</a>.</p>

<p>I agree to the club contacting with other information about gymnastics activities that I might be interested in.</p>
<?php 
$labels = [
	'com_email' => 'by email',
	'com_sms' => 'by SMS',
	'com_mail' => 'by post'
];
foreach($labels as $id=>$label) { ?>
	<div class="form-check">
	<?php 
	$checkbox['id'] = $id;
	$checkbox['name'] = $id;
	echo form_checkbox($checkbox, $id, $postvar[$id] );
	printf('<label class="form-check-label" for="%s">%s</label>', $id, $label);
	?>
	</div>
<?php } ?>
</section>

<section>
<h5>Medical treatment/first aid</h5>
<p>Gymnastics activities have an inherent risk of injury and although the club endeavours to minimise risk, accidents may still happen.</p>
<p class="form-check"><?php
$id = 'chk_medical'; 
$checkbox['id'] = $id;
$checkbox['name'] = $id;
echo form_checkbox($checkbox, $id, $postvar[$id]);
?>
<label class="form-check-label" for="<?php echo $id;?>">I agree to emergency medical treatment or first aid which in the opinion of a qualified medical practitioner or first aider is necessary. I also understand that should such a situation arise; all reasonable steps will be taken to contact an emergency contact.</label>
</p>
</section>

<section>
<h5>Conduct</h5>
<p>All Hawth policies can be downloaded from our <a href="https://www.hawthgymnastics.co.uk/downloads" target="_blank">website</a>:</p>
<p class="form-check"><?php
$id = 'con_participant'; 
$checkbox['id'] = $id;
$checkbox['name'] = $id;
echo form_checkbox($checkbox, $id, $postvar[$id]);
?>
<label class="form-check-label" for="<?php echo $id;?>">I confirm I have read and understood the club’s <em>Code of Conduct for Participants</em> and agree to my responsibilities detailed within it.</label>
</p>

<p class="form-check"><?php
$id = 'con_parent'; 
$checkbox['id'] = $id;
$checkbox['name'] = $id;
echo form_checkbox($checkbox, $id, $postvar[$id]);
?>
<label class="form-check-label" for="<?php echo $id;?>">I confirm I have read and understood the club’s <em>Code of Conduct for Parents and Guardians</em> and agree to my responsibilities detailed within it.</label>
</p>
<p class="form-check"><?php
$id = 'con_welfare'; 
$checkbox['id'] = $id;
$checkbox['name'] = $id;
echo form_checkbox($checkbox, $id, $postvar[$id]);
?>
<label class="form-check-label" for="<?php echo $id;?>">I confirm I have read and understood the club’s <em>Welfare Policies</em> and agree to my responsibilities detailed within it.</label>
</p>
</section>

<h5>Filming and promotional activities</h5>
<p>On occasion, we may film you/your child during a gymnastics session for coaching purposes. Unless you agree otherwise, we will retain these images for only as long as are required to support you/your child’s learning. If you do not want us to film you/your child, please let us know and we will not do this.</p>

</fieldset>
<button class="btn btn-primary" type="submit">submit</button>
</form>

<?php $this->endSection();
