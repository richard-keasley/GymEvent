<?php $this->extend('default');

$this->section('content'); ?>

<p>Here is a summary of the hardware you may need to run an event.</p>
<p>We have some reconditioned laptops and old TVs if you need them.</p>
<p>Yuu will access to the internet (WifI / etc) at the venue.</p>
<h3>Client laptops</h3>
<p>Client laptops can be almost anything that can run a browser (e.g. Chrome, Edge, Firefox, Safari). Any device will do, and we encourage users to provide their own device (e.g. laptop, tablet, mobile phone).</p>
<ul>
<li>1 for each judging panel</li>
<li>1 for registration desk</li>
<li>1 for announcer</li>
</ul>
<h3>Score display</h3>
<p>Each display needs a laptop and a (large) TV.</p>
<h3>Additionally</h3>
<ul>
<li>Ensure you have lots of long extension leads to reach to each judge station.</li>
<li>You will need a machine (laptop) to play music. Ensure you have a lead to connect the laptop to whatever PA system you are using.</li>
<li>You may need a router if using a <abbr title="Local network">LAN</abbr> (rather than direct connection via the internet).</li>
</ul>

<?php $this->endSection();

$this->section('top');
include(__DIR__ .'/_head.php');
$this->endSection();

$this->section('bottom');
include(__DIR__ .'/_foot.php');
$this->endSection();

