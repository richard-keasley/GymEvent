<?php $this->extend('default');

$this->section('content'); ?>
<div class="float-end"><?php echo view('includes/version');?></div>

<h2>CodeIgniter <img src="<?php echo base_url('/public/ui/res/codeigniter.svg');?>" style="height: 1.3em;"></h2>

<p>Base URL for this installation is <?php printf('<a href="%1$s">%1$s</a>', base_url());?>.</p>

<p><a href="https://codeigniter.com">CodeIgniter</a> is used for the back-end (PHP) framework.</p>

<p>Check the <a href="https://codeigniter4.github.io/userguide" target="_blank">User Guide</a>. CodeIgniter is a community-developed open source project, with several venues for the community members to gather and exchange ideas. View all the threads on <a href="https://forum.codeigniter.com/" target="_blank">CodeIgniter's forum</a>, or <a href="https://codeigniterchat.slack.com/"target="_blank">chat on Slack</a>!</p>

<h2>Front end (<abbr title="style sheets">CSS</abbr> and <abbr title="JavaScript animations">JS</abbr>) <span class="bi-bootstrap-fill" style="color:#7952b3"></span></h2>
<p>Bootstrap is used for the front-end. Read <a href="https://getbootstrap.com/docs/5.0/getting-started/introduction/">documentation</a> on how to use Bootstrap.</p>

<p>All resources for front-end are stored in <mark><?php echo FCPATH;?>public/ui/</mark> (URL: <?php echo base_url('public/ui');?>/).</p>

<p>The <a href ="<?php echo base_url('/public/ui/bootstrap-icons-1.5.0/');?>">Bootstrap icons are listed</a> here <a href ="https://icons.getbootstrap.com/">(documentation on icon usage)</a>.</p>

<h5>Style sheets</h5>
<p>Style sheet is compiled from <mark>{ui}/scss/bootstrap.sccs</mark> into <mark>{ui}/bootstrap.ccs</mark>. Compilation requires the folder <mark>{ui}/{bootstrap}/scss/*</mark>.<p> 
<p>Icon style sheet is <mark>{ui}/{BS-icons}/bootstrap-icons.css</mark>.</p>
<p>Extra styles can be added to <mark>{ui}/custom.ccs</mark>.</p>

<h5>JavaScript helpers</h5>
<p><mark>{ui}/{BS}/dist/js/bootstrap.bundle.min.js</mark> is included as a JS helper at the end of each page.</p>
<p><a href="https://jquery.com/"><img src="<?php echo base_url('/public/ui/res/jquery.svg');?>" class="pe-2" style="height:1.3em;">jQuery</a> is used as well (but not jQuery-UI).</p>

<h3>Style</h3>
<ul>
<li>Icon <code>.bi-<em>icon</em> .text-<em>colour</em></code> indicates current state <span class="bi-award text-success" title="award success"><span></li>
<li>Filled button <code>.btn .btn-<em>colour</em></code> indicates a change will occur on click <span class="btn bi-award btn-sm btn-success" title="click this to award success"><span></li>
<li>Outlined button <code>.btn .btn-outline-<em>colour</em></code> indicates a link to an 'edit page' <span class="btn bi-award btn-sm btn-outline-primary" title="this links to a page where you can change things"><span></li>
<li>Normal text <code>.nav-link</code> indicates a link to an 'information page'</li>
</ul>

<h3>Colours</h3>
<div class="container"><?php 
$bgs = ['transparent', 'light', 'dark'];
$texts = ['primary','secondary','success','danger','warning','info','light','dark','body', 'muted','white','black-50','white-50'];

foreach($texts as $text) { ?>
<div class="row">	
<?php foreach($bgs as $bg) { ?>
	<div class="col bg-<?php echo $bg;?> text-<?php echo $text;?> border">
	.bg-<?php echo $bg;?>
	.text-<?php echo $text;?>
	</div>
<?php } ?>
	<div class="col alert-<?php echo $text;?> border">
	.alert-<?php echo $text;?>
	</div>
</div>
<?php } ?>
</div>

<?php $this->endSection();