<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
	font-family: sans-serif;
	margin: 1em;
	padding: 0;
}
h1 {
	margin: 0;
	background: #eee;
}
header {
	margin: 0 0 0.5em 0;
	border-bottom: 1px solid #ccc;
	padding: 0.5em 0;
}
header ul {
	white-space: nowrap;
}
header ul strong {
	display: inline-block;
	min-width: 6em;
	text-align: right;
	margin-right: .3em;
}
form {
	margin: 0.2em 0;
	font-size: 1rem;
	color: #212529;
	background-color: #ffc;
	padding: 0.3em;
	border: 1px solid #F0E6BF;
}
button {
	float: left;
	font-weight: 400;
	line-height: 1.5;
	color: #FFF;
	text-align: center;
	cursor: pointer;
	background-color: #572b2b;
	border: 0;
	padding: .375rem .75rem;
	font-size: 1rem;
	border-radius: .25rem;
	margin: 0 .5em .5em 0;
}
tfoot td {
	font-weight: bold;
	border-top: 1px solid #ddd;
}
thead th {
	text-align: left;
}
.list-unstyled {
	list-style: none;
	padding: 0;
	margin: 0;
}
.routine table {
	border-collapse: collapse;
	width: 100%;
	max-width: 26cm;
}
.routine thead th {
	text-align: left;
}
.routine tbody td {
	border: 1px solid #666;
	padding: .1em .4em;
	max-width: 8cm;
	overflow: hidden;
	white-space: nowrap;
}
.startval {
	margin-top: 1em;
}
.startval h5 {
	font-size: 1rem;
	margin: 0;
}
.startval table td {
	padding: 0 0.5em 0 0;
}
.text-end {
	text-align: right;
}
.row {
	display: flex;
	gap: 1em;
}


@media print {
	@page {
		size: A4 landscape;
	}
	form {
		display:none;
	}
	body {
		margin: 0;
	}
}
</style>
<title><?php echo $title;?></title>
</head>

<body class="container-fluid">
<h1>Intention sheet</h1>

<header class="row">

<ul class="list-unstyled"><?php 
$li_format = '<li><strong>%s:</strong> %s</li>';
foreach(['name', 'club'] as $key) {
	printf($li_format, $key, $intention->$key);
} ?></ul>

<ul class="list-unstyled"><?php 
foreach(['gender', 'level'] as $key) {
	printf($li_format, $key, $intention->$key);
} 
?></ul>

<ul class="list-unstyled"><?php 
foreach(['exercise'] as $key) {
	printf($li_format, $key, $intention->$key);
} ?></ul>

<?php echo view('general/intention/version'); ?>
</header>

<main>

<section class="routine"><?php 
$table = new \CodeIgniter\View\Table();

$tbody = []; 
foreach($intention->skills as $sk_num=>$sk_id) {
	$skill = $intention->rules->skills->get($sk_id);
	$special = $intention->rules->specials->get($intention->specials[$sk_num]);
	$bonus = $intention->rules->bonuses->get($intention->bonuses[$sk_num]);
	// lookup skill description, group, value	
	$tbody[] = [
		$skill['description'],
		$skill['group'],
		$skill['difficulty'],
		$special['description'],
		$bonus['description']
	];
}
$table->setTemplate(['table_open' => '<table>']);
$table->setHeading(['Skill','Grp','Diff','Special requirement','Bonus']);
echo $table->generate($tbody);
 ?></section>

<section class="startval">
<?php echo view('general/intention/sv_table', ['intention'=>$intention]); ?>
</section>

</main>

<footer>

<?php 
echo form_open(base_url('general/intention'));
echo form_hidden('routine', $intention->encode()); 
?>
<p><button type="submit">edit</button> This is a printer friendly page containing the routine and judging notes. You can either print it or 
<abbr title="right click, then select save as...">save it</abbr>
to your PC for use later. Please use A4 landscape for printing intention sheets. Click the 'edit' button to upload your routine for editing.</p>
</form>
</footer>

</body>
</html>