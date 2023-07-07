<?php 
if(empty($_SESSION['user_id'])) return; 

$attr = [
	'class' => "alert alert-light p-1 d-print-none"
];
echo form_open(site_url(), $attr); ?>
<button class="btn btn-secondary" type="submit" name="logout" value="1">Logout</button>
<?php echo getlink('admin', 'admin'); ?> 
<label><?php 
$label = "Logged in as {$_SESSION['user_name']}";
$link = getlink('user', $label);
echo $link ? $link : $label;
?></label>
<?php echo form_close();

if(ENVIRONMENT != 'development') return;
if(!empty($exception)) return;
?>
<footer class="row border-top bg-light text-secondary d-print-none">

<div class="col"><?php 
$links = [
	['setup/dev', 'Development notes'],
	['setup/update', 'Update the App']
];
$navbar = new \App\Views\Htm\Navbar($links);
$navbar->template['items_start'] = '<ul class="nav">';
echo $navbar->htm();
?></div>

<div class="col">
<p class="p-2 m-0">Page rendered in {elapsed_time} sec</p>
</div>

<div class="col dropup">
<button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-lock"></i> Permissions</button>
<ul id="debuginfo" class="dropdown-menu dropdown-menu-end"><?php 
foreach(\App\Libraries\Auth::check_paths() as $path=>$row) {
	$colour = $row[1] ? 'success' : 'danger' ;
	$title = $row[1] ? 'allowed' : 'Forbidden' ;
	printf('<li class="dropdown-item text-%s" title="%s"><strong>%s :</strong> %s </li>', $colour, $title, $path, $row[0]);
};
?></ul>
</div>

</footer>
