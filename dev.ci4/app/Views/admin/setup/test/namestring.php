<?php $this->extend('default');

$this->section('content');

$clubret = new \App\Entities\Clubret;

$test_string = 
'name1, name2, 12346, 7-8-2010
name1, name2, 12346, 12 aug 2021    
name1, name2, 12346, 12 aug 2011, another, name, 76575, 8-aug-90  
name1, name2, 12346, 12 aug 2011    
123456, name1, name2, 12 aug 2010    
123456, 12 aug 2010, name1, name2    
Anaya Akisanya, , 2845451, 23-Jan-2009
Anaya middle Akisanya, , 2845451, 23-Jan-2009
Anaya, middle, Akisanya, 2845451, 23-Jan-2009
name1 name2, 4522575, 09-Sep-2013
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
	echo $namestring;
}

$this->endSection();

$this->section('top'); ?>
<pre class="alert alert-light">
the ongoing battle against stupidity
assume users
	- input data in wrong order
	- add middle name as a separate data item
	- include more than one name 
	- don't separate name with comma
</pre>
<?php $this->endSection();