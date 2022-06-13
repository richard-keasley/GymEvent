<?php $this->extend('default');

$this->section('content'); ?>
<p><img src="/app/scoreboard/screen.png" class="float-end" style="max-width:35%" alt="screen">Our scoring system is multi-discipline. Each discipline has its own scoring requirements, but in general the scoring elements follow similar rules and judging forms can be tailored to the needs of each discipline or event.</p>
<p>The scoring system is designed for simplicity making it especially suitable for regional or club events. It doesn't conform to <em>all</em> requirements detailed in the relevant discipline's code of points documents (e.g. it doesn’t take all E scores and automatically exclude outer scores - you just put in the scores you want to save). Limiting the app to just simple sums and averages provides the most flexibility, especially useful in events that don't have  full judging panels.</p>
<p>The application can display scores to audiences in "real time" and results' tables are also "real time". This means medal ceremonies can usually start as soon as all the scores have been entered by judges.</p>
<p>We have a touch screen panel which shows the results while the competition is in progress, which has proved popular with coaches.</p>
<p>The registration system enables event organisers to tick off gymnasts as they arrive at the venue and log withdrawn entries. This updates the judges and announcer as to who is present.</p>
<p>The announcer's screen has the same lists of entries per round / rotation as the judges. These lists also update with judges' actions (selecting an entry to judge, entry can start routine, judge started entering scores, score value). This means the announcer is kept up to date with every aspect of the event as it happens.</p>
<p>We can switch on the scoreboard before the day so judges can familiarise themselves with the system.</p>

<p class="toolbar"><?php 
echo \App\Libraries\View::back_link("/");
echo getlink('/setup/scoreboard', 'admin');
?></div>

<?php $this->endSection();
 
if($links) {
	$this->section('sidebar');
	echo $links->htm();
	$this->endSection(); 
}
