<?php if(!empty($_SESSION['user_id'])) { 
$attr = [
	'class' => "alert alert-light p-1 d-print-none"
];
echo form_open(base_url(), $attr); ?>
<button class="btn btn-secondary" type="submit" name="logout" value="1">Logout</button>
<?php echo getlink('/admin', 'admin'); ?> 
<label>Logged in as <?php printf('<a href="%s">%s</a>', base_url('user'), $_SESSION['user_name']);?></label>
<?php echo form_close();

if(ENVIRONMENT == 'development' && empty($exception)) { ?>
<footer class="border-top bg-light">
<button class="btn btn-warning btn-sm small" type="button" data-bs-toggle="collapse" data-bs-target="#debuginfo" aria-expanded="false" aria-controls="debuginfo"><i class="bi bi-tools"></i></button>
<div class="collapse row text-secondary" id="debuginfo">

<div class="col"><?php echo anchor(base_url('setup/dev'), ENVIRONMENT);?></div>
<div class="col">Page rendered in {elapsed_time} seconds</div>
<div class="col">
<div class="card card-body">
<ul class="list-unstyled"><?php 
foreach(\App\Libraries\Auth::check_paths() as $path=>$row) {
	$colour = $row[1] ? 'success' : 'danger' ;
	$title = $row[1] ? 'allowed' : 'Forbidden' ;
	printf('<li class="text-%s" title="%s"><strong>%s:</strong> %s </li>', $colour, $title, $path, $row[0]);
};
?></ul>
</div>
</div>
</div>
</footer>
<?php }

}
