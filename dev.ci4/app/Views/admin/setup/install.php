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
			<?php htm_pathlist(APPPATH); ?>
		</ul>
	</li>
	<li class="bi bi-folder my-1"> <?php echo $paths['SYSTEMPATH'];?></li>
	<li class="bi bi-folder my-1"> <?php echo $paths['WRITEPATH'];?></li>
	<li class="bi bi-folder my-1"> <?php echo $paths['FCPATH'];?>
		<ul class="list-unstyled ps-3">
			<li class="bi bi-folder"> public
				<ul class="list-unstyled ps-3">
					<li class="bi bi-folder"> events
					<ul class="list-unstyled ps-3">
						<li class="bi bi-list"> event uploads</li>
					</ul></li>
					<li class="bi bi-folder"> teamtime</li>
				</ul>
			</li>
			<li class="bi bi-folder"> app
				<ul class="list-unstyled ps-3">
					<li class="bi bi-list"> App resources</li>
					<li class="bi bi-file-code"> *.css</li>
					<li class="bi bi-file-code"> *.js</li>
				</ul>
			</li>
			<?php htm_pathlist(FCPATH); ?>
		</ul>
	</li>
	<?php htm_pathlist(ROOTPATH); ?>
</ul>
</li>
</ul>
<?php $this->endSection();

$this->section('content'); ?>
<section>
<h4>Directory structure</h4>

<p>CodeIgniter sits in <?php echo $paths['ROOTPATH'];?>. This path sits outside the document root.</p>

<p><?php echo $paths['SYSTEMPATH'];?> stores the files that make up the CodeIgniter framework. This folder contains a copy of CodeIgniter system. An upgrade of CodeIgniter system will overwrite this folder. Edit nothing in this folder. <strong>NB:</strong> The CodeIgniter system is not included in this distribution.</p>

<p><?php echo $paths['APPPATH'];?> holds the GymEvent application. Edit files in here to alter the application's behaviour.</p>

<p><?php echo $paths['WRITEPATH'];?> will be used to write cache files and logs / etc. It can be empty for a new installation, but make sure it is writeable.</p>

<p><?php echo $paths['FCPATH'];?> is the document root (publicly accessible) for this domain. You may have to use a different folder name for this (e.g. using primary domain on cPanel).</p>

<p><?php echo path_label('FCPATH', 'public');?>
is used for downloads and files accessible to the public. All front-end <abbr title="e.g. bootstrap, jQuery, CSS">site resources</abbr> are in 
<?php echo path_label('FCPATH', 'public');?>. 
Event specific files (and installation specific files) are in <?php echo path_label('FCPATH', 'public/events');?>.</p>

<p>Read about CodeIgniter's <a href="https://codeigniter.com/user_guide/concepts/structure.html">directory structure</a>.</p>
</section>

<section>
<h4>Core Constants</h4>
<ul class="list-unstyled">
<?php foreach($consts as $const) {
	printf('<li>%s: <code>%s</code></li>', $const, constant($const));
} ?>
</ul>
<p><a href="https://codeigniter.com/user_guide/general/common_functions.html#core-constants" title="CodeIgniter help">Read about CodeIgniter constants here</a>.</p>
</section>
<?php $this->endSection();

$this->section('bottom'); ?>
<section>
<h4>Prepare PHP for CodeIgniter</h4>
<p>Ensure the php.ini files (<?php echo path_label('FCPATH', 'php.ini');?> &amp; <?php echo path_label('FCPATH', '.user.ini');?>) are appropriate for the server.</p>

<p>PHP requirements<br>
<code>sudo apt-get install php-intl<br>
sudo apt-get install php-xml<br>
sudo apt-get install php-curl<br>
sudo apt-get install php-mbstring<br>
sudo systemctl restart apache2</code></p>

<h4>Installation edits</h4>
<p>Ensure <?php echo $paths['WRITEPATH'];?> is writeable. You may also need to create specific folders (e.g. cache).<br>
<code>chmod 777 -R writable</code></p>

<p>Edit <?php echo path_label('ROOTPATH', '.env');?> according to the specific set-up of each server. Include database connection information and base URL. Leave <code>app.baseURL</code> blank for laptops / etc (an over-ride in <code>\App\Config\App</code> will calculate this). If you are not using mod_rewrite (below) then include the line <code>app.indexPage = index.php</code>.</p>


<p>Edit the front controller (<?php echo path_label('FCPATH', 'index.php');?>) to make <code>$pathsPath</code> point to <?php echo path_label('APPPATH', 'Config/Paths.php');?>.<br>Example: <code>$pathsPath = FCPATH . '../<em>{ci4}</em>/app/Config/Paths.php';</code></p>
</section>

<section>
<h4>Enable Rewrite</h4>
<p><?php echo path_label('FCPATH', '.htaccess');?> contains instructions for the "rewrite engine" to route requests for non-existent resources to the front controller (<?php echo path_label('FCPATH', 'index.php');?>).</p>

<p>Make sure the server domain <em>allows</em> rewrite (it probably doesn't by default). Edit the httpd configuration<br>
<code>sudo nano /etc/apache2/sites-available/000-default.conf</code></p>
<pre class="border p-1">
DocumentRoot /var/www/html/public
 
&lt;Directory /var/www/html/public&gt;
	Options Indexes FollowSymLinks MultiViews
	AllowOverride all
	Order allow,deny
	allow from all
&lt;/Directory&gt;
</pre>
<p><strong>NB:</strong> 
App is installed in <code>/var/www/html</code> (<mark>APPPATH</mark>). 
DocumentRoot is <code>/var/www/html/public</code> (<mark>FCPATH</mark>).</p> 

<p>Make sure the relevant Apache modules are enabled.<br>
<code>sudo a2enmod rewrite<br>
sudo service apache2 restart</code></p>
</section>

<section>
<h4>Set-up database</h4>
<p>Use MySQL to insert users and data to database.<br>
<code>sudo mysql -u root</code></p>
<pre class="border p-1">
CREATE USER 'db_user'@'localhost' IDENTIFIED BY 'db_password';
CREATE DATABASE 'gymevent_main';
GRANT ALL PRIVILEGES ON 'gymevent_main'.* To 'db_user'@'localhost';
FLUSH PRIVILEGES;
</pre>
<p><strong>NB:</strong> <q>db_user</q> and <q>db_password</q> need to be entered into 
<?php echo path_label('ROOTPATH', '.env');?>.</p>

<p>Export database from live website (phpMyAdmin). Copy it into Richard’s documents.</p>
<p><code>cd  [wherever the MySQL script was saved]<br>
sudo mysql -u root gymevent_main &lt; gymevent_main.sql</code></p>
</section>

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

function htm_pathlist($path) {
	$files = [];
	foreach(scandir($path) as $file) {
		$file = new \CodeIgniter\Files\File($path . $file);
		switch($file->getExtension()) {
			case 'ico': 
				$icon = 'file-image'; break;
			case 'txt': 
			case 'md':
				$icon = 'file'; break;
			default:
			$icon = 'file-code';
		}
		if($file->isFile()) {
			printf('<li class="bi bi-%s"> %s</li>', $icon, $file->getBasename());
		}
	}	
}
