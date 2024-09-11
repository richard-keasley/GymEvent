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
$view_all = '<abbr title="view event entries">view all</abbr>';
$edit_all = '<abbr title="edit event entries">edit all</abbr>';
$tbody = [
	['public', $nothing,  $nothing, $view_all, $nothing],
	['club',   $nothing,  'edit own', $view_all, $nothing],
	['admin',  $edit_all, $edit_all, $edit_all, $nothing]
];
$table->setHeading($thead);
echo $table->generate($tbody);
?>

<h5>Music states</h5>
<?php 

$tbody = [
	['public', $nothing, $nothing, 'view all', $nothing],
	['club', $nothing, 'upload', 'view own', $nothing],
	['admin', 'edit all', 'edit all', 'edit all', $nothing],
	['player', $no, $yes, $yes, $no]
];
$table->setHeading($thead);
echo $table->generate($tbody);
?>

<h5>Private events</h5>
<ul>
<li>Are not accessible by the public</li>
<li>Have no returns (entries by import)</li>
<li>Accept no music uploads by public (use admin interface)</li>
</ul>
