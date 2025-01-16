<?php $this->extend('default');

$this->section('content'); ?>
<p class="bg-light p-1 border d-inline-block">Updated: 13 January 2025</p>
<p>It's necessary to hold personal data on event entrants and clubs so we can:</p>
<ul>
	<li>provide accurate results</li>
	<li>play the correct music</li>
	<li>check the eligibility of entrants to a specific category or event</li>
	<li>ensure clubs are informed of event details</li> 
	<li>provide an estimate of resources needed (e.g. judges, labour, seating)</li>
	<li>allow event specific announcements (before, during and after the event)</li>
</ul>
<p>We will not use the data for any other purpose than listed above.</p>
<p>This data may be shared with competition organisers. It will not be shared with anyone else.</p>

<p>The data we hold:</p>
<ul class="list-group">
<li class="list-group-item">participants:<ul>
	<li>full name</li>
	<li>club</li>
	<li>Date of Birth</li>
</ul></li>

<li class="list-group-item">coaches / staff:<ul>
	<li>full name</li>
	<li>club</li>
	<li>Date of Birth</li>
</ul></li>


<li class="list-group-item">clubs:<ul>
	<li>contact name</li>
	<li>phone number</li>
	<li>email address</li>
</ul></li>

</ul>
<p>This data may be kept on several servers according to the requirements of each specific event.</p>
<p>We realise the responsibilities of holding this data and strive to minimise the risk of data theft to unauthorised parties.</p>
<p>How we protect this data:</p>
<ol>
	<li>access to sensitive data is restricted to authorised users</li>
	<li>users can only access data that applies to them</li>
	<li>event specific user accounts are disabled as soon as they are no longer needed</li>
	<li>passwords for event specific user accounts (e.g. judges) will be changed for each event</li>
	<li>data for past events will be deleted from the public server once it is no longer required</li>
</ol>
<p>Please talk to Richard or Kevin if you have any concerns about this.</p>
<p>Please remember the results sheets for an event will normally be made public.</p>

<?php $this->endSection();

$this->section('bottom');
include(__DIR__ .'/_foot.php');
include(__DIR__ .'/_policy.php');
$this->endSection(); 

$this->section('top');
include(__DIR__ .'/_head.php');
$this->endSection();
