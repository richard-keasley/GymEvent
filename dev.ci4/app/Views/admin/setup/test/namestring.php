<?php $this->extend('default');
$clubret = new \App\Entities\Clubret;

$this->section('content'); ?>

<?php echo form_open(); ?>
<div class="p-1 alert alert-light">
<p>Hint: enter data as <?php echo \App\Entities\namestring::hint;?>.</p>

<div><?php
$value = $postvars['namestring'] ?? '' ;
if($value) {
	$namestring = new \App\Entities\namestring($value);
	$value = (string) $namestring;
}

$attrs = [
	'name' => "namestring",
	'value' => $value,
	'style' => "max-width:35em",
	'class' => "form-control float-start me-1"
];

echo form_input($attrs);
$attrs = [
	'name' => "cmd", 
	'value' => "TEST",
	'type' => "submit",
	'style' => "min-width:5em",
	'class' => "btn btn-primary"
];
echo form_input($attrs);
?></div>
<p><?php
if($value) {
	echo $namestring;
	if($namestring->error) echo "<br><strong>input {$namestring->error}</strong>";
}
?></p>
</div>
<?php echo form_close();

/* date checker 
$dates = [
	'  7-8-2010',
	'7/8/20',
	'7/8/30',
	'7-Aug-2010   ',
	'Aug 7 2010 ',
	'Aug 7 30',
	'Aug 7 20',
	'2/14/2010',
	'invalid date',
	'',
	0
];
$dt = new \datetime; 
foreach($dates as $date) {
	$dob = \App\Entities\namestring::sanitize_dob($date);
	$result = $dob ? $dob->format('j F Y') : 'fail' ;
	if(!$date) $date = '[empty]';
	echo "<div>{$date} =&gt; {$result}</div>";
}
// date checker */

/* name checker */

$test_string = 
'Elizabeth ,  Rimmer-Leeming, 18-Mar-2007
name1 name2, 7-8-2010
name1, name2, 12346, 7/8/10
name1, name2, 12346, 7/14/10
name1,    name2, 12346, 7 aug 2010    
name1  name2, dunno    
name1, name2, 12346, 7 aug 1920    
name1, name2, 12346, 7 aug 2010, another, name, 76575, 8-aug-90  
name1, name2, 12346, 7 aug 2010    
123456, name1, name2, 7 aug 10    
123456, 7 aug 2010, name1, name2    
Anaya Akisanya, , 2845451, 23-Jan-2009
Anaya middle Akisanya, , 2845451, 23-Jan-2009
Anaya, middle, Akisanya, 2845451, 23-Jan-2009
name1 name2, 4522575, 09-Sep-2013
name1, name2, , 7-8-2010
name1, name2, 1.2, 7-8-2010
Raon12
name1, name2, 12346
name1, name2, 12346, random
name1, name2, 0, yesterday

name1,name2
3 aug 1990
465, 5/3/90';

$test_data = explode("\n", $test_string);
foreach($test_data as $key=>$row) {
	$namestring = new \App\Entities\namestring($row);
	$arr = [sprintf('<span style="white-space:pre">%s</span>', trim($row))];
	foreach($namestring->__debugInfo() as $key=>$val) {
		if($key=='error') {
			$val = sprintf('<strong>entry %s</strong>', $val);
		}
		$arr[] = "{$key}: {$val}";
	}
	printf('<div class="border p-1 m-1">%s</div>', implode('<br>', $arr));
}


// name checker */


$this->endSection();

$this->section('top'); ?>
<p class="border">
fraction:

<?php 
$str = '1/1 2/1 1/2 &frac12;';
echo new \App\Views\Htm\Pretty($str);

?>
</p>

<pre class="alert alert-light">
the ongoing battle against stupidity
Assume users
- blank lines
- add middle name as a separate data item
- include more than one name 
- don't separate name with comma
- use space variants and multiple spaces
</pre>
<?php $this->endSection();