<?php
$this->extend('default');

$this->section('content');?>
<p>It's necessary to hold personal data on event entrants and clubs so we can:</p>
<ul>
	<li>provide accurate results</li>
	<li>play the correct music</li>
	<li>check the eligibility of entrants to a specific category or event</li>
	<li>ensure clubs are informed of event details</li> 
	<li>provide an estimate of resources needed (e.g. judges, labour, seating)</li>
	<li>allow event specific announcements (before, during and after the event)</li>
</ul>
<p>The data we hold:</p>
<ul>
<li>For the entrant:<ul>
	<li>full name</li>
	<li>club</li>
	<li>Date of Birth</li>
	<li><abbr title="British Gymnastics">BG</abbr> membership number</li>
	</ul></li>
<li>For the club:<ul>
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

if(\App\Libraries\Auth::check_role('admin')) { 
$this->section('bottom'); ?>
<section class="card text-dark bg-light">
<h3 class="card-header">Risks</h3>
<div class="card-body">
<dl>
<dt>Some users have a weak password to access the scoreboard. What if a  password is guessed to gain unauthorised access to scoreboard?</dt>
<dd>Judges / etc should not have access to dates of birth.</dd>
<dd>General users who can manage the event can access more.</dd>

<dt>How much information can an unauthorised person get if they gain entry to the server?</dt>
<dd>We should take reasonable precautions to stop hackers gaining entry to the server.</dd>

<dt>Vulnerabilities in unknown laptops can reveal passwords to unauthorised people</dt>
<dd>Unknown laptops may contain keyboard loggers or other malware.</dd>

<dt>As SSL is not used, how likely is it that someone will intercept communication between the users and server?</dt> 
<dd>There is no way of quantifying that risk. Packet sniffers could reveal the login credentials. Remember, the server is not busy; it is highly unlikely that anyone even knows about it.</dd>

<dt>Who gets the blame (and is liable for the fine) in the event of unauthorised access?</dt>
<dd>There is no excuse for leaving a password lying around but, as mentioned above, the victim (data controller) is not responsible for the actions of the criminal (hacker).</dd>
</dl>

<hr>
<h4>Actions</h4>
<p>There are 2 categorises of user: "event specific" and "general". General users will have access to data all the time, while event specific users are only granted access during an event (or in the lead up to that event).</p>
<p>The following precautions should be used with the user accounts of these "event specific" users:</p>
<ul>
<li>credentials (user name / password) should be updated for each event.</li>
<li>accounts should be deactivated immediately after the end of an event</li>
<li>the number of active accounts should be limited to those who <em>need</em> access</li>
</ul>

</div>

<div class="card-footer">
<p>We must keep things in perspective here.</p>
<p>Most of the data held will be made public at the end of the event in the form of <a href="/events/">results sheets</a>. The <abbr title="Date of Birth">DoB</abbr> for entrants is probably the only data held that is not made public</p>
<p><abbr title="General Data Protection Regulations">(GDPR)</abbr> is supposed to cover things like data leakage (e.g. us transmitting the data somewhere it can be read with no user credentials) or using data for purposes not specified or mentioned to the data owner (i.e. the gymnast or club). <abbr>GDPR</abbr> doesn't suggest prosecuting data controllers in the event of unauthorised access. In that situation, the hacker is the criminal and the data controller the victim.</p> 
</div>




</section>
<?php $this->endSection(); 
}
