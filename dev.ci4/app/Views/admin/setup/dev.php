<?php $this->extend('default');

$this->section('content'); ?>
<div class="float-sm-end"><?php echo $this->include('includes/version');?></div>

<h2>CodeIgniter <img src="<?php echo base_url('/app/images/codeigniter.svg');?>" style="height: 1.3em;"></h2>

<p>Base URL for this installation is <?php printf('<a href="%1$s">%1$s</a>', base_url());?>.</p>

<p><a target="ci" href="https://codeigniter.com">CodeIgniter</a> is used for the back-end (PHP) framework.</p>

<p>Check the <a href="https://codeigniter4.github.io/userguide" target="_blank">User Guide</a>. CodeIgniter is a community-developed open source project, with several venues for the community members to gather and exchange ideas. View all the threads on <a href="https://forum.codeigniter.com/" target="_blank">CodeIgniter's forum</a>, or <a href="https://codeigniterchat.slack.com/"target="_blank">chat on Slack</a>!</p>

<p><a href="<?php echo base_url('setup/db');?>">Read about the database structure.</a></p>

<h4>Updating CodeIgniter</h4>
<?php 
$paths = new \Config\Paths;
$systemDirectory = realpath($paths->systemDirectory);
$ci_folder = dirname(dirname($systemDirectory));
?>
<ol>
<li>Create a directory <code>x.x.x</code> in <code><?php echo $ci_folder;?></code>.</li>
<li>Upload contents of the CodeIgniter installation to folder <code><?php echo $ci_folder;?>/x.x.x</code>.</li>
<li>Update 'systemDirectory' in <code>/App/Config/Paths</code> to point to the <em>system</em> folder within this installation (<code><?php echo $ci_folder;?>/x.x.x/system</code>).</li>
<li>Check <a target="ci" href="https://codeigniter4.github.io/userguide/installation/upgrading.html">CodeIgniter release notes</a> and merge changes as necessary into <code>\App\Config\*</code>.</li>
</ol>
<p>CodeIgniter system directory is currently <code><?php echo $systemDirectory;?></code>.</p>

<h2>Front end (<abbr title="style sheets">CSS</abbr> and <abbr title="JavaScript animations">JS</abbr>) <span class="bi-bootstrap-fill" style="color:#7952b3"></span></h2>
<p>Bootstrap is used for the front-end. Read <a href="https://getbootstrap.com/docs/5.0/getting-started/introduction/">documentation</a> on how to use Bootstrap.</p>

<p>All resources for front-end are stored in <code><?php echo FCPATH;?>app</code> (<?php echo base_url('app');?>).</p>

<p>Read <a href ="https://icons.getbootstrap.com/">documentation on Bootstrap icons</a>.</p>

<h5>Style sheets</h5>
<p>Style sheet is a compiled bootstrap style sheet <mark>/app/bootstrap.ccs</mark>.<p> 
<p>Custom styles can be added to <mark>/app/custom.ccs</mark>.</p>

<h5>JavaScript helpers</h5>
<p><mark>/app/bootstrap.bundle.min.js</mark> is included as a JS helper at the end of each page. This requires <mark>/app/bootstrap.bundle.min.js.map</mark>.</p>

<p><a href="https://jquery.com/"><img src="<?php echo base_url('/app/images/jquery.svg');?>" class="pe-2" style="height:1.3em;">jQuery</a> is used as well (but not jQuery-UI).</p>

<h3>Style</h3>
<ul>
<li>Icon <code>.bi-<em>icon</em> .text-<em>colour</em></code> indicates current state <span class="bi-award text-success" title="award success"><span></li>
<li>Filled button <code>.btn .btn-<em>colour</em></code> indicates a change will occur on click <span class="btn bi-award btn-sm btn-success" title="click this to award success"><span></li>
<li>Outlined button <code>.btn .btn-outline-<em>colour</em></code> indicates a link to an 'edit page' <span class="btn bi-award btn-sm btn-outline-primary" title="this links to a page where you can change things"><span></li>
<li>Normal text <code>.nav-link</code> indicates a link to an 'information page'</li>
</ul>

<h3>Colour scheme</h3>
<div class="my-2 d-flex flex-wrap">
<?php 
$prefixes = ['text-bg-', 'text-', 'bg-opacity-25 bg-'];
$colours = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light'];
foreach($prefixes as $prefix) { ?>
	<div class="px-2" style="width:100%; max-width:16em"><?php 
	foreach($colours as $colour) {
		$class = $prefix . $colour;
		printf('<div class="p-1 %1$s">%1$s</div>', $class);
	}
	?></div>
<?php } ?>
</div>

<?php $this->endSection();
