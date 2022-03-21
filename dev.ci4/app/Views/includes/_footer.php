<?php if(!empty($_SESSION['user_id'])) { ?>
<footer>
<?php 
$attr = [
	'class' => "border rounded text-muted my-3 p-2"
];
echo form_open(base_url(), $attr); ?>
	<button class="btn btn-secondary btn-sm" type="submit" name="logout" value="1">Logout</button> 
	<label>Logged in as <?php printf('<a href="%s">%s</a>', base_url('user'), $_SESSION['user_name']);?></label>
</form>
</footer>
<?php } ?>

<?php if(ENVIRONMENT !== 'production' && \App\Libraries\Auth::check_role('superuser')) { ?>
<footer class="border-top bg-light">
<div class="row text-secondary">
<div class="col"><?php echo anchor(base_url('setup/dev'), ENVIRONMENT);?></div>
<div class="col">Page rendered in {elapsed_time} seconds</div>
</div>
</footer>
<?php }
