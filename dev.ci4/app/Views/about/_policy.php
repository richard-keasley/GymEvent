<?php 
if(!\App\Libraries\Auth::check_role('controller')) return;
?>

<section class="card text-dark bg-light mb-2">
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