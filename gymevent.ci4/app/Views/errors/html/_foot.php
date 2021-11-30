<?php if(!empty($_SESSION['user_id'])) { ?>
<footer>
<?php 
$attr = [
	'class' => "border rounded text-muted my-3 p-2"
];
echo form_open(base_url(uri_string()), $attr); ?>
	<button class="btn btn-secondary btn-sm" type="submit" name="logout" value="1">Logout</button> 
	<label>Logged in as <?php printf('<a href="%s">%s</a>', base_url('user'), $_SESSION['user_name']);?></label>
</form>
</footer>
<?php } 

#d($exception); 
#var_dump(get_defined_vars ( )); 
#var_dump($_SESSION);
include(VIEWPATH . 'includes/_foot.php');
