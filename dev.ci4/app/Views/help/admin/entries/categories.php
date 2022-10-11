<h4>Disciplines and categories</h4>
<p>The data structure has changed since the MS Access database. This makes it hard to predict which discipline and discipline category is required on scoreboard.<p>
<p>Current structure used by data entry is as follows:</p>
<ul>
<li>event<ul>
	<li>discipline 1<ul>
		<li>category 1.1<ul>
			<li>entry</li>
			<li>&hellip;</li>
		</ul></li>
		<li>category 1.2<ul>
			<li>entry</li>
			<li>&hellip;</li>
		</ul></li>
	</ul></li>
	<li>discipline 2<ul>
		<li>&hellip;</li>
	</ul></li>
</ul></li>
</ul>

<p>Disciplines and categories contain minimal information; they are little more than containers to keep entries sorted. New disciplines are created for every event.</p>

<ol>
<li>Each event contains many disciplines.</li>
<li>Each event's discipline contains many categories.</li>
<li>Each discipline's category contains many entries.</li>
</ol>

<p>"Category" is used to describe a collection of entries that use the same rules (e.g. same age / level / whatever).</p>
<p>"Group" is used to describe a group of gymnasts that compete in the same place and time during the vent. One category may be split amongst several groups.</p>

<hr>
<p>Each category is assigned an "exercise set", looked up from  <code>exerciseset</code> in scoreboard database.</p>
<p>Discipline and discipline categories <em>could be</em> looked up as well, but they need to be tidied up and descriptions added for that to happen.</p>
