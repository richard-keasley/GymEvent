<?php $this->extend('default');

$this->section('content'); 
echo $nav;
?>
<div class="item-image"><img src="<?php echo base_url('/app/profile/logo.png');?>"></div>

<p>GymEvent is a multi-discipline system designed to be simple and use to set-up for a wide variety of events. GymEvent is run by Richard and Kevin from <a href="https://www.hawthgymnastics.co.uk/">Hawth Gymnastics</a>.</p>
<p>We use reconditioned laptops as judge consoles. Any device will do, and we encourage users to provide their own device (be it laptop, tablet, mobile phone).</p>
<p>The service includes:</p>
<ul>
<li>Event entry</li>
<li>Music uploads and playback</li>
<li>Judge console</li>
<li>score display</li>
<li>Announcer information (including team-time)</li>
</ul>

<h4>Things to remember</h4>
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
<p><img src="/app/profile/image.png"></p>

<?php 
echo $nav;
$this->endSection();
