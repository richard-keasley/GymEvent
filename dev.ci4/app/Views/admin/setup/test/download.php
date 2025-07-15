<?php $this->extend('default');

$this->section('content'); ?>
<getform>
<label>file name</label> 
<input name="filename" value="<?php echo $filename;?>">
<select name="layout">
<option name="table">table</option>
<option value="cattable">cat table</option>
</select>
<button type="submit" name="echo" value="0">download</button>
<button type="submit" name="echo" value="1">view</button>
</getform>
<?php $this->endSection();

$this->section('bottom');
$table = \App\Views\Htm\Table::load();
$table->setHeading(array_keys($data[0]));
echo $table->generate($data);
$this->endSection();
