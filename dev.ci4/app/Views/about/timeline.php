<?php $this->extend('default');

$this->section('content'); ?>

<h3>Time-line for event preparation</h3>
<ol>
<li>Publish the rule pack and open on-line entries.</li>
<li>Closing date<ul>
		<li>finalise categorisation of entries</li>
		<li>ensure all entries are in correct category</li>
	</ul></li>
<li>Publish the running order<ul>
		<li>no further alterations to gymnasts' numbers</li>
		<li>open music service (allow at least a week for clubs to upload music)</li>
	</ul></li>
<li>Finalise running order<ul>
		<li>close music service (allow a week to ensure all the music can be played)</li>
		<li>transfer data to score board (allow a week to check it's all done)</li>
		<li>create play-list on music player</li>
		<li>create tables for team-time</li>
	</ul></li>
<li>Run the event</li>
<li>Publish the results</li>
</ol>

<?php $this->endSection();

$this->section('bottom');
include(__DIR__ .'/_foot.php');
$this->endSection();

$this->section('top');
include(__DIR__ .'/_head.php');
$this->endSection();
