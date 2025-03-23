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
	$paths[$const] = local::path($const);
}

$this->section('sidebar'); ?>
<ul class="list-unstyled">
<li class="bi bi-folder"> <?php echo $paths['ROOTPATH'];?>
<ul class="list-unstyled ps-3">
	<li class="bi bi-folder my-1"> <?php echo $paths['APPPATH'];?>
		<ul class="list-unstyled ps-3">
			<?php local::files(APPPATH, 'application folders'); ?>
		</ul>
	</li>
	<li class="bi bi-folder my-1"> <?php echo $paths['SYSTEMPATH'];?></li>
	<li class="bi bi-folder my-1"> <?php echo $paths['WRITEPATH'];?></li>
	<li class="bi bi-folder my-1"> <?php echo $paths['FCPATH'];?>
		<ul class="list-unstyled ps-3"> 
			<?php 
			local::folders(FCPATH);
			local::files(FCPATH); 
			?>
		</ul>
	</li>
	<?php local::files(ROOTPATH); ?>
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

<p><?php echo $paths['WRITEPATH'];?> used to write cache files and logs / etc. Needs to be writeable.</p>

<p><?php echo $paths['FCPATH'];?> is the document root (publicly accessible) for this domain. You may have to use a different folder name for this (e.g. using primary domain on cPanel).</p>

<p><?php echo local::path('FCPATH', 'app');?>: All front-end <abbr title="e.g. bootstrap, jQuery, CSS">site resources</abbr>.</p>
<p><?php echo local::path('FCPATH', 'public');?>: installation specific files (not updated with app updates).</p>
<p><?php echo local::path('FCPATH', 'public/events');?> Event specific files .</p>

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
<p>Ensure the php.ini files (<?php echo local::path('FCPATH', 'php.ini');?> &amp; <?php echo local::path('FCPATH', '.user.ini');?>) are appropriate for the server.</p>

<p>PHP requirements<br>
<code>sudo apt-get install php-intl<br>
sudo apt-get install php-xml<br>
sudo apt-get install php-curl<br>
sudo apt-get install php-mbstring<br>
sudo systemctl restart apache2</code></p>

<h4>Installation edits</h4>
<p>Ensure <?php echo $paths['WRITEPATH'];?> is writeable. You may also need to create specific folders (e.g. cache).<br>
<code>chmod 777 -R writable</code></p>

<p>Edit <?php echo local::path('ROOTPATH', '.env');?> according to the specific set-up of each server. Include database connection information and base URL. Leave <code>app.baseURL</code> blank for laptops / etc (an over-ride in <code>\App\Config\App</code> will calculate this). If you are not using mod_rewrite (below) then include the line <code>app.indexPage = index.php</code>.</p>


<p>Edit the front controller (<?php echo local::path('FCPATH', 'index.php');?>) to make <code>$pathsPath</code> point to <?php echo local::path('APPPATH', 'Config/Paths.php');?>.<br>Example: <code>$pathsPath = FCPATH . '../<em>{ci4}</em>/app/Config/Paths.php';</code></p>
</section>

<section>
<h4>Enable Rewrite</h4>
<p><?php echo local::path('FCPATH', '.htaccess');?> contains instructions for the "rewrite engine" to route requests for non-existent resources to the front controller (<?php echo local::path('FCPATH', 'index.php');?>).</p>

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
App is installed in <code>/var/www/html</code> (<?php echo local::path('APPPATH');?>). 
DocumentRoot is <code>/var/www/html/public</code> (<?php echo local::path('FCPATH');?>).</p> 

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
<?php echo local::path('ROOTPATH', '.env');?>.</p>

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

class local {
	
static function path($const_name, $file='') {
	$title = rtrim(constant($const_name), DIRECTORY_SEPARATOR) . "/{$file}";
	$text = $const_name;
	if($file) $text .= "/{$file}";
	return sprintf('<mark data-bs-toggle="tooltip" title="%s">%s</mark>', $title, $text);
}

static function folders($path, $filelist=true) {
	$subdirs = [
		FCPATH . 'public',		
	];
	$dirlists = [
		FCPATH . 'app' => 'app resource folders',
	];
	
	$pathlist = [];
	$pattern = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*';
	foreach(glob($pattern, GLOB_ONLYDIR) as $entry) {
		ob_start();
		if(in_array($entry, $subdirs)) {
			local::folders($entry, false);
		}
		if($filelist) {
			$dirlist = $dirlists[$entry] ?? null ;
			local::files($entry, $dirlist);
		}
		$sub = ob_get_clean();
		if($sub) $sub = sprintf('<ul class="list-unstyled ps-3">%s</ul>', $sub);
		
		$dir = new \CodeIgniter\Files\File($entry);
		$pathlist[] = ['folder', $dir->getBasename() . $sub];			
	}

	foreach($pathlist as $row) {
		printf('<li class="bi bi-%s"> %s</li>', $row[0], $row[1]);
	}
}

static function files($path, $dirlist=null) {
	$pathlist = [];
	if($dirlist) {
		$pathlist[] = ['list', "<em>{$dirlist}</em>"];
	}
	
	$files = new \CodeIgniter\Files\FileCollection();
	$files->addDirectory($path);
	foreach($files as $file) {
		$label = $file->getBasename();
		if($label=='index.htm') continue;
		$icon = match($file->getExtension()) {
			'ico' => 'file-image',
			'txt' => 'file',
			'md' => 'file',
			default => 'file-code'
		};
		$pathlist[] = [$icon, $label];	
	}
	
	# d($pathlist);
	foreach($pathlist as $row) {
		printf('<li class="bi bi-%s"> %s</li>', $row[0], $row[1]);
	}
}

}