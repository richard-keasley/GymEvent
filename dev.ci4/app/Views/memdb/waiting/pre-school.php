<?php $this->extend('memdb/default');

$this->section('content');?>
<form method="POST">      
<p>Please complete this form to add your child to the <em>Pre-school</em> waiting list.</p>
<p><?php echo $intro;?></p>

<fieldset><legend>About the child</legend>
<p>
<label>Child's name</label>
<input name="name1" type="text" spellcheck="false" maxlength="30" placeholder="First name">
<input name="name2" type="text" spellcheck="false" maxlength="30" placeholder="Second name">
</p>

<p>
<label>Gender</label>
<select name="gender">
	<option value="-">-</option>
	<option value="Male">Male</option>
	<option value="Female">Female</option>
</select>
</p>

<p>
<label>Date of Birth</label>
<input type="date">
</p>
</fieldset>

<fieldset><legend>About the parent</legend>

<p>
<label>Parent's name</label>
<input name="accountname1" type="text" spellcheck="false" maxlength="30" placeholder="First name">
<input name="accountname2" type="text" spellcheck="false" maxlength="30" placeholder="Second name">
</p>

<p>
<label>Email address</label>
<input name="email" type="email" autocomplete="email" spellcheck="false">
</p>

<p>
<label>Phone number</label>
<input name="accountphone" type="text">
</p>

<p>
<label>What days can you do/are you interested in?</label>
<textarea rows="1"></textarea>
</p>
</fieldset>

<div class="toolbar">
    <input class="btn btn-primary" type="submit" value="Submit">
</div>

</form>
<?php $this->endSection();