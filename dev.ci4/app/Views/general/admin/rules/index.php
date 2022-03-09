<?php $this->extend('default');
 
$this->section('content');?>
<h5>Version</h5>
<?php echo $this->include('general/intention/version'); ?>
<p>Remember to update rules' version when you upload new rules.</p>
<hr>

<section>
<h4>Start value process</h4>
<p>This is the process used to calculate the start score on an intention sheet.</p>
<ol>
<li>eliminate invalid skills, special requirements and bonuses chronologically<ul>
	<li>repeated</li>
	<li>invalid for this category</li>
	<li>too many skills from one group</li>
	</ul></li>
<li>ensure there's 2 skills from each group<ul>
	<li>no value for completion</li>
	<li>error if incomplete</li>
</ul></li>
<li>calculate difficulty value<ul>
	<li>skills without bonus</li>
	<li>higher level skills without bonus</li>
	<li>skills ignoring bonus</li>
	<li>higher level skills ignoring bonus</li>
	<li>if incomplete: recalculate difficulty without higher level skills ignoring bonus, show error on recalculated difficulty</li>
</ul></li>
<li>calculate special requirement value<ul>
	<li>check valid for this skill</li>
	<li>error if incomplete</li>
</ul></li>
<li>if no errors, calculate bonus value<ul>
	<li>check not used by difficulty</li>
	<li>check valid for this skill</li>
	<li>error if bonus present with no value</li>
</ul></li>
</ol>
</section>

<?php $this->endSection(); 

$this->section('sidebar');
$rule_parts = [
	'rules' => 'Rules',
	'skills' => 'Skills',
	'composition' => 'Composition',
	'specials' => 'Special Requirements',
	'bonuses' => 'Bonuses'
];
$nav = [];
foreach($rule_parts as $key=>$label) {
	$nav[] = ["admin/general/edit/{$exe}/{$key}", $label];
}
$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();

$this->endSection(); ?>

