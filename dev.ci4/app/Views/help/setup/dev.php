<h2>Codeigniter</h2>
<nav class="nav flex-column float-start">
	<a class="nav-link" href="https://codeigniter.com" target="_blank"></a>
	<a class="nav-link" href="/">Home</a>
	<a class="nav-link" href="https://codeigniter4.github.io/userguide/" target="_blank">Docs</a>
	<a class="nav-link" href="https://forum.codeigniter.com/" target="_blank">Community</a>
	<a class="nav-link" href="https://github.com/codeigniter4/CodeIgniter4/blob/master/CONTRIBUTING.md" target="_blank">Contribute</a>
</nav>

<h3>Learn</h3>
<p>The User Guide contains an introduction, tutorial, a number of "how to" guides, and then reference documentation for the components that make up the framework. Check the <a href="https://codeigniter4.github.io/userguide"	target="_blank">User Guide</a>!</p>

<h3>Discuss</h3>
<p>CodeIgniter is a community-developed open source project, with several venues for the community members to gather and exchange ideas. View all the threads on <a href="https://forum.codeigniter.com/" target="_blank">CodeIgniter's forum</a>, or <a href="https://codeigniterchat.slack.com/"target="_blank">chat on Slack</a>!</p>

<h3>Contribute</h3>
<p>CodeIgniter is a community driven project and accepts contributions of code and documentation from the community. Why not <a href="https://codeigniter.com/en/contribute" target="_blank">join us</a>?</p>

<h2>Bootstrap</h2>
<nav class="nav float-start flex-column">
	<a class="nav-link" href="https://getbootstrap.com/docs/5.0/getting-started/introduction/">Documentation</a>
	<a class="nav-link" href="/bootstrap/icons/">Icons</a>
</nav>
<p>Bootstrap is used for the front-end</p>

<h3>Style</h3>
<ul>
<li>Icon <code>.bi-<em>icon</em> .text-<em>colour</em></code> indicates current state</li><li>Filled button <code>.btn .btn-<em>colour</em></code> indicates a change will occur on click</li>
<li>Outlined button <code>.btn .btn-outline-<em>colour</em></code> indicates a link to an 'edit page'</li><li>Normal text <code>.nav-link</code> indicates a link to an 'information page'</li>
</ul>

<h3>Colours</h3>
<div class="container"><?php 
$bgs = ['transparent', 'light', 'dark'];
$texts = ['primary','secondary','success','danger','warning','info','light','dark','body', 'muted','white','black-50','white-50']; 
foreach($texts as $text) { ?>
<div class="row">	
<?php foreach($bgs as $bg) { ?>
	<div class="col-sm bg-<?php echo $bg;?> text-<?php echo $text;?> border">
	.bg-<?php echo $bg;?>
	.text-<?php echo $text;?>
	</div>
	<?php } ?>
</div>
<?php } ?>
</div>
