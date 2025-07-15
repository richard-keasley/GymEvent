<?php $this->extend('default');

$this->section('content'); ?>

<h2>Database structure</h2>

<div class="float-end" style="width:12em;">
<img src="/app/images/db1.png" class="m-1"><br>
<img src="/app/images/db2.png" class="m-1">
</div>

<p><?php
echo anchor('setup/db/orphans', 'View orphans');
?></p>
<p>Deleting <code>events</code> or <code>users</code> will delete associated <code>club returns</code> and <code>entries</code>.</p>
<p><code>Entries</code> are created from <code>club returns</code>, but are not related to them (after creation).</p>
<p><strong>ToDo</strong> DB structure for entries:
<pre>
- events
&nbsp;&nbsp; - evt_disciplines
&nbsp;&nbsp;&nbsp;&nbsp; - evt_categories
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - evt_entries</pre></p>

<?php $this->endSection();
