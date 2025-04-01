<?php $this->extend('default');

$this->section('content'); ?>

<?php echo form_open(); ?>
<div class="p-1 alert alert-light">
<p>Hint: enter data as <?php echo \App\Libraries\Namestring::hint;?>.</p>

<div><?php
$value = $postvars['namestring'] ?? '' ;
if($value) {
	$namestring = new \App\Libraries\Namestring($value);
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
	if($namestring->error) echo "<strong>input {$namestring->error}</strong>";
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
foreach($dates as $string) {
	$dt = \App\Libraries\Namestring::get_dt($string);
	$result = $dt ? $dt->format('j F Y') : 'fail' ;
	if(!$string) $string = '[empty]';
	echo "<div>{$string} =&gt; {$result}</div>";
}
// date checker */

/* name checker */

$test_string = 
'Elizabeth ,  Rimmer-Leeming, 18-Mar-2007
name1 name2, 123, 
name1 name2, 7/8/80, 
name1, name2, 12346, 7/8/10
name1, name2, 7/2/10, 12346
name1,    name2, 12346, 7 aug 2010    
name1  name2, 123 
name1 middle name2, 123, 3 dec 01    
name1, o\'connor, 12346, 7 aug 1920    
name1, name2, 12346, 7 aug 2010, another, name, 76575, 8-aug-90  
name1, name2, 12346, 7 aug 2010    
123456, name1, name2, 7 aug 10    
123456, 7 aug 2010, name1, name2    
Anaya Akisanya, , 2845451, 23-Jan-2009
Anaya middle Akisanya, , 2845451, 23-Jan-2009
Anaya middle, Akisanya, 2845451, 23-Jan-2009
Anaya, middle, Akisanya, 2845451, 23-Jan-2009
name1 name2, 4522575, 09-Sep-2013
name1, name2, , 7-8-2010
name1, name2, 1.2, 7-8-2010
one_name
name1, name2, 12346
name1, name2, 12346, random
name1, name2, 0, yesterday

name1,name2
3 aug 1990
465, 5/3/90';

$test_data = explode("\n", $test_string);

foreach($test_data as $rowkey=>$row) {
	$arr = [$row];
	
	$namestring = new \App\Libraries\Namestring($row);
		
	$arr[] = sprintf('<em class="text-muted"> values: [ %s ]</em>', implode(', ', $namestring->__toArray()));
	
	$arr[] = $namestring;
	if($namestring->error) {
		$rownum = $rowkey + 1;
		$arr[] = "<strong>Row {$rownum} {$namestring->error}</strong>";
	}
	printf('<p class="border p-1">%s</p>', implode('<br>', $arr));
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