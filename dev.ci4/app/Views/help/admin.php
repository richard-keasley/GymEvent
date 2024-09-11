<h3>Process</h3>
<ol>
<li>Create event (event states "waiting")</li>
<li>Open club returns (state "edit")</li>
<li>Stop club returns (state "view")</li>
<li>Populate entries from club returns</li>
<li>Set-up entries specifying apparatus and music.</li>
<li>Open music upload (state "edit")</li>
<li>Stop music upload (state "view")</li>
<li>Export data for scoreboard</li>
<li>Set event states to "finished"</li>
</ol>

<p>Club returns and music use the following states:</p>
<ul>
<li>0: Waiting: Not yet open</li>
<li>1: Edit: Open for uploads / edits</li>
<li>2: View: Read-only</li>
<li>3: Finished: Can't be viewed</li>
</ul>
