<p>Select the event you are interested in.</p>
<p>Club returns, music and videos use the following states:</p>
<ul class="list list-unstyled">
<li><?php echo \App\Entities\Event::icons['future'];?> 0: Waiting: Not yet open</li>
<li><?php echo \App\Entities\Event::icons['current'];?> 1: Edit: Open for uploads / edits</li>
<li><?php echo \App\Entities\Event::icons['current'];?> 2: View: Read-only</li>
<li><?php echo \App\Entities\Event::icons['past'];?> 3: Finished: Can't be viewed</li>
</ul>
