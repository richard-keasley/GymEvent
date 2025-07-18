<?php 
// logout form
$logged_in = $_SESSION['user_id'] ?? false;
if($logged_in) { ?>
<footer class="savepage-hide alert alert-light p-1 d-print-none">
<?php echo form_open(site_url()); ?>
<button class="btn btn-secondary" type="submit" name="logout" value="1">Logout</button>
<?php echo getlink('admin', 'admin'); ?> 
<label><?php 
$user_name = $_SESSION['user_name'] ?? 'unknown';
$label = "Logged in as {$user_name}";
$link = getlink('user', $label);
echo $link ? $link : $label;
?></label>
<?php echo form_close(); ?>
</footer>
<?php } // logout form

if(ENVIRONMENT != 'development') return;
if(!empty($exception)) return;
?>
<footer class="savepage-hide row border-top bg-light text-secondary d-print-none">

<div class="col-sm"><?php 
$links = [
	['setup/dev', 'Development notes'],
	['setup/update', 'Update the App'],
];
$navbar = new \App\Views\Htm\Navbar($links);
$navbar->template['items_start'] = '<ul class="nav">';
echo $navbar->htm();
?></div>

<div class="col-sm">
<p class="p-2 m-0 text-end">Page rendered in {elapsed_time} sec</p>
</div>

</footer>
