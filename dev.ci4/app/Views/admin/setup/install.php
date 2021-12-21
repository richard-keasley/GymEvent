<?php $this->extend('default');

$consts = [
	'ROOTPATH',
	'APPPATH',
	'SYSTEMPATH',
	'WRITEPATH',
	'FCPATH'
];
// create HTML for path constants
foreach($consts as $const) {
	$paths[$const] = path_label($const);
} 

$this->section('sidebar'); ?>
<ul class="list-unstyled">
<li class="bi bi-folder"> <?php echo $paths['ROOTPATH'];?>
<ul class="list-unstyled ps-3">
	<li class="bi bi-folder my-1"> <?php echo $paths['APPPATH'];?>
		<ul class="list-unstyled ps-3">
			<li class="bi bi-list"> [application folders]</li>
			<li class="bi bi-file-code"> .htaccess</li>
			<li class="bi bi-file-code"> Common.php</li>
		</ul>
	</li>
	<li class="bi bi-folder my-1"> <?php echo $paths['SYSTEMPATH'];?></li>
	<li class="bi bi-folder my-1"> <?php echo $paths['WRITEPATH'];?></li>
	<li class="bi bi-folder my-1"> <?php echo $paths['FCPATH'];?>
		<ul class="list-unstyled ps-3">
			<li class="bi bi-folder"> public
				<ul class="list-unstyled ps-3">
					<li class="bi bi-folder"> ui</li>
					<li class="bi bi-list"> [site downloads / etc]</li>
				</ul>
			</li>
			<li class="bi bi-file-code"> .htaccess</li>
			<li class="bi bi-file-code"> .user.ini</li>
			<li class="bi bi-file-code"> php.ini</li>
			<li class="bi bi-file-code"> index.php</li>
			<li class="bi bi-file"> robots.txt</li>
		</ul>
	</li>
	<li class="bi bi-file-code"> .env</li>
</ul>
</li>
</ul>
<?php $this->endSection();

$this->section('content'); ?>
<h4>Directory structure</h4>

<p>CodeIgniter sits in <?php echo $paths['ROOTPATH'];?>. This path sits outside the document root.</p>

<p><?php echo $paths['SYSTEMPATH'];?> stores the files that make up the CodeIgniter framework. This folder contains a copy of CodeIgniter system. An upgrade of CodeIgniter system will overwrite this folder. Edit nothing in this folder. <strong>NB:</strong> The CodeIgniter system is not included in this distribution.</p>

<p><?php echo $paths['APPPATH'];?> holds the GymEvent application. Edit files in here to alter the application's behaviour.</p>

<p><?php echo $paths['WRITEPATH'];?> will be used to write cache files and logs / etc. It can be empty for a new installation, but make sure it is writeable.</p>

<p><?php echo $paths['FCPATH'];?> is the document root (publicly accessible) for this domain. You may have to use a different folder name for this (e.g. using primary domain on cPanel).</p>

<p><?php echo path_label('FCPATH', 'public');?>
is used for downloads and files accessible to the public. All front-end <abbr title="e.g. bootstrap, jQuery, CSS">site resources</abbr> are in 
<?php echo path_label('FCPATH', 'public/ui');?>. 
Event specific files (and installation specific files) are in <?php echo path_label('FCPATH', 'public/events');?>.</p>

<p>Read about CodeIgniter's <a href="https://codeigniter.com/user_guide/concepts/structure.html">directory structure</a>.</p>

<h4>Installation edits</h4>

<p>Edit <?php echo path_label('ROOTPATH', '.env');?> according to the specific set-up of each server. Include database connection information and default URL.</p>

<p>Ensure the php.ini files (<?php echo path_label('FCPATH', 'php.ini');?> &amp; <?php echo path_label('FCPATH', '.user.ini');?> are appropriate for the server.</p>

<p>Edit the front controller (<?php echo path_label('FCPATH', 'index.php');?>) to make <code>$pathsPath</code> point to <?php echo path_label('APPPATH', 'Config/Paths.php');?>.<br>Example: <code>$pathsPath = FCPATH . '../<em>{ci4}</em>/app/Config/Paths.php';</code></p>

<?php $this->endSection();

$this->section('bottom'); ?>
<h4>Core Constants</h4>
<ul class="list-unstyled">
<?php foreach($consts as $const) {
	printf('<li>%s: <code>%s</code></li>', $const, constant($const));
} ?>
</ul>
<p><a href="https://codeigniter.com/user_guide/general/common_functions.html#core-constants" title="CodeIgniter help">Read about CodeIgniter constants here</a>.</p>

<h4>Enable Rewrite</h4>
<p><?php echo path_label('FCPATH', '.htaccess');?> contains instructions for the "rewrite engine" to route requests for non-existent resources to the front controller (<code>/index.php</code>).</p>

<p>Make sure the server domain <em>allows</em> rewrite (it probably doesn't by default). Edit the httpd configuration<br>
<code>sudo nano /etc/apache2/sites-available/default</code></p>
<pre class="border p-1">&lt;Directory "/path/to/document/root/"&gt;
	Options Indexes FollowSymLinks MultiViews
	AllowOverride all
	Order allow,deny
	allow from all
&lt;/Directory&gt;</pre>

<p>Make sure the relevant Apache modules are enabled.<br>
<code>sudo a2enmod rewrite<br>
sudo service apache2 restart</code></p>

<script>
$(function() {
var tooltipTriggerList = [].slice.call(document.querySelectorAll('mark'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	return new bootstrap.Tooltip(tooltipTriggerEl);
})
});
</script>

<?php $this->endSection();

function path_label($const_name, $file='') {
	$title = rtrim(constant($const_name), DIRECTORY_SEPARATOR) . "/{$file}";
	$text = $const_name;
	if($file) $text .= "/{$file}";
	return sprintf('<mark data-bs-toggle="tooltip" title="%s">%s</mark>', $title, $text);
}
