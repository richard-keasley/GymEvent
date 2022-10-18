<h3>Participants</h3>
<h5>disciplines / categories</h5>
<p>Only use alpha-numeric characters, dashes and under-scores in disciplines and categories. No spaces, &amp;, commas, etc.</p> 
<h5>inf settings</h5>
<dl>
<dt>fe</dt><dd>Fee per entry</dd>
<dt>fg</dt><dd>Fee per gymnast</dd>
<dt>team</dt><dd>Request team name</dd>
<dt>n</dt><dd>Number of lines per entry (size of text area)</dd>
<dt>cat</dt><dd>Append Year / month of birth to category. Hint: it adds the DoB in the given format. e.g. "y-m".</dd>
</dl>

<h3>Permissions and states</h3>
<?php 
// compare to help\setup\index

$table = \App\Views\Htm\Table::load('default');
$thead = ['', '0. waiting', '1. edit', '2. closed', '3. finished'];


$yes = '<i class="bi bi-check text-success"></i>';
$no = '<i class="bi bi-x text-danger"></i>';
$nothing = '<i class="bi bi-dash"></i>';

?>
<h5>Clubret states</h5>
<?php 
$tbody = [
	['guest', $nothing, $nothing, $nothing, $nothing],
	['club', $nothing, 'edit own', '<abbr title="view event entries">view all</abbr>', $nothing],
	['admin', 'edit all', 'edit all', 'edit all', $nothing]
];
$table->setHeading($thead);
echo $table->generate($tbody);
?>

<?php /*
<h5>Video states</h5>
<?php 
$tbody = [
	['guest', $nothing, $nothing, 'view all', $nothing],
	['club', $nothing, 'edit own', 'view all', $nothing],
	['admin', 'edit all', 'edit all', 'edit all', $nothing]
];
$table->setHeading($thead);
echo $table->generate($tbody);
// */ ?>

<h5>Music states</h5>
<?php 

$tbody = [
	['guest', $nothing, $nothing, 'view all', $nothing],
	['club', $nothing, 'upload', 'view own', $nothing],
	['admin', 'edit all', 'edit all', 'edit all', $nothing],
	['player', $no, $yes, $yes, $no]
];
$table->setHeading($thead);
echo $table->generate($tbody);
?>

