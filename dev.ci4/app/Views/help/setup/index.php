<h5>Role required to view pages</h5>
<?php 
// compare to help/admin/events/edit
$file = new \CodeIgniter\Files\File(__DIR__ . '/controllers.csv');
$csv = $file->openFile('r');
$tbody = [];
while($row = $csv->fgetcsv()) {
	if(count($row)>1) $tbody[] = $row;	
}
$csv = null;

$table = \App\Views\Htm\Table::load('bordered');
echo $table->generate($tbody);
?>
<div class="row">
<div class="col-6"><div class="border p-1">
<p>Roles are columns 0-3. Column headings denote the event states. Event states are:</p>
<ol start="0">
<li>waiting</li>
<li>edit</li>
<li>view</li>
<li>finished</li>
</ol>
</div></div>
<div class="col-6"><div class="border p-1">
<p>Defined roles (in order of authority):</p>
<ul class="list-unstyled ms-3"><?php 
foreach(\App\Libraries\Auth::roles as $key=>$role) {
	echo "<li><strong>{$key}:</strong> {$role}</li>";
} ?></ul>
</div></div>
</div>

<p>In general, restricted pages in the table are prohibited from view unless the user_id supplied in the URL corresponds to the current logged in user (for users with role "club").</p>
<p>Example: <code>/clubrets/view/10/1</code> relates to event 10 and user 1.<p>
<ul>
<li>Non logged in users (role ="-") can not see this page.</li>
<li>"club" user with ID 1 can see this page.</li>
<li>"admin" users can see this page</li>
</ul>
