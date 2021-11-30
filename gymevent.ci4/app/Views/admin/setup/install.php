<?php $this->extend('default');

$this->section('sidebar'); ?>
<ul class="list-unstyled">
<li class="bi bi-folder"> [root]<ul class="list-unstyled ps-3">
	<li class="bi bi-folder"> gymevent.ci4<ul class="list-unstyled ps-3">
		<li class="bi bi-folder"> app<ul class="list-unstyled ps-3">
			<li class="bi bi-list"> [application folders]</li>
			<li class="bi bi-file-code"> .htaccess</li>
			<li class="bi bi-file-code"> Common.php</li>
		</ul></li>
		<li class="bi bi-folder"> system</li>
		<li class="bi bi-folder"> writable</li>
		</ul></li>
		<li class="bi bi-file-code"> .env</li>
	</ul></li>
	<li class="bi bi-folder"> public_html<ul class="list-unstyled ps-3">
		<li class="bi bi-folder"> public<ul class="list-unstyled ps-3">
			<li class="bi bi-folder"> ui</li>
			<li class="bi bi-list"> [site downloads / etc]</li>
		</ul></li>
		<li class="bi bi-file-code"> .htaccess</li>
		<li class="bi bi-file-code"> .user.ini</li>
		<li class="bi bi-file-code"> php.ini</li>
		<li class="bi bi-file-code"> index.php</li>
		<li class="bi bi-file"> robots.txt</li>
	</ul></li>
</ul>
<?php $this->endSection();

$this->section('content'); ?>
<h4>Directory structure</h4>

<p>CodeIgniter sits in <mark>~/gymevent.ci4</mark>.</p>

<p>You will have to edit <mark>~/gymevent.ci4/.env</mark> according to the specific set-up of each server. You will need database connection information and default URL.</p>

<p><mark>~/gymevent.ci4/system</mark> stores the files that make up the framework itself. An upgrade of CodeIgniter system will overwrite this folder. Edit nothing in this folder.</p>

<p><mark>~/gymevent.ci4/app</mark> holds the GymEvent application. Edit files in here to alter the application's behaviour.</p>

<p><mark>~/gymevent.ci4/writable</mark> will be used to write cache files and logs / etc. It can be empty for a new installation, but make sure it is writeable.</p>

<p><mark>~/public_html</mark> is the document root for this domain. You may have to use a different folder name for this.</p>

<p>Ensure <mark>~/public_html/php.ini</mark> and <mark>~/public_html/.user.ini</mark> contain PHP ini files. Ensure they are appropriate for the server.</p>

<p>Edit <mark>~/public_html/index.php</mark> to make <code>$pathsPath</code> point to <mark>~/gymevent.ci4/app/Config/Paths.php</mark>. Example:<br><code>$pathsPath = realpath(FCPATH . '../gymevent.ci4/app/Config/Paths.php');</code></p>

<p><mark>~/public_html/public</mark> is used for downloads and files accessible to the public. All front-end <abbr title="e.g. bootstrap, jQuery, CSS">site resources</abbr> are in <mark>~/public_html/public/ui</mark>.</p>

<p>Read about CodeIgniter's <a href="https://codeigniter.com/user_guide/concepts/structure.html">directory structure</a>.</p>
<?php $this->endSection();

$this->section('bottom'); ?>
<h4>Core constants</h4>
<ul class="list-unstyled">
<?php foreach(['APPPATH', 'ROOTPATH', 'SYSTEMPATH', 'FCPATH', 'VIEWPATH', 'PUBLICPATH'] as $const) {
	printf('<li>%s: <code>%s</code></li>', $const, constant($const));
} ?>
</ul>

<h4>Enable Rewrite</h4>
<p><mark>~/public_html/.htaccess</mark> contains instructions for the  "rewrite engine".</p>

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

<?php $this->endSection();