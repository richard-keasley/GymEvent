<?php $this->extend('default');
$related = getlink('about/policy', 'Data Policy');

$this->section('content'); ?>
<nav class="nav"><?php echo $related;?></nav>

<h2>Time-line for event preparation</h2>
<img src="/public/profile/logo.png" style="width:6em; float:right">
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
</ol>
<h3>Remember</h3>
<ol>
<li>We can open the score-board before the event to allow judges to practice</li>
<li>We do not have score screens or amplification. Many Leisure centres / etc will already have these items available to borrow as part of the venue hire. If not, these will need to be hired in as extra. Try the following suppliers: 
	<ul>
	<li><a href="https://www.avensyshireevents.co.uk/">Avensys</a></li>
	<li><a href="https://www.gymdata.co.uk/">GymData</a> (book them early if you do)</li>
	<li><a href="http://www.brightonsoundsystem.co.uk/">Brighton Sound systems</a></li>
	</ul>
</li>
<li>If you are playing music:
	<ul>
	<li>We can provide a media player with the music organised according to the running order</li>
	<li>Check you have the necessary <abbr title="Phonographic Performance Limited">PPL</abbr> and <abbr title="Performing Right Society">PRS</abbr> license</li>
	</ul>
</li>
</ol>
<p><img src="/public/profile/image.png"></p>

<nav class="nav"><?php echo $related;?></nav>

<?php $this->endSection();

