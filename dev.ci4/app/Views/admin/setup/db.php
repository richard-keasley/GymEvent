<?php $this->extend('default');

$this->section('content'); ?>

<h2>Database structure</h2>

<div class="float-end" style="width:12em;">
<img src="/app/setup/db1.png" class="m-1"><br>
<img src="/app/setup/db2.png" class="m-1">
</div>

<p>Deleting <code>events</code> or <code>users</code> will delete associated <code>club returns</code> and <code>entries</code>.</p>
<p><code>Entries</code> are created from <code>club returns</code>, but are not related to them (after creation).</p>

<?php $this->endSection();
