<p>The media player should play most formats. Click a track to start playing.</p>
<p>If the browser won't play the track, you can <em>sometimes</em> play the track on your local machine if you download it. For example: the browser will not play Windows Media files (<code>.wma</code>), but any Windows machine will be able to play these tracks once they are downloaded.</p>

<p>If a track has been converted (because it wouldn't play), the original track should be stored in <code>~/orig</code>.</p>

<hr>
<h5>Finding tracks</h5>
<?php
$event_id = intval(basename(previous_url()));
$track_type = 'mp3';

$track = new \App\Libraries\Track;
$track->event_id = $event_id ?? 999;
$track->exe = 'FX';
$track->entry_num = 21;

$filebase = $track->filebase($track_type);

?>
<p>Example for 
	event id <code><?php echo $track->event_id;?></code>, 
	exercise <code><?php echo $track->exe;?></code>, 
	entry number <code><?php echo $track->entry_num;?></code>,
	<abbr title="the file extension for the track">music type</abbr> <code><?php echo $track_type;?></code>.
</p>
<ul>
<li>This track is named <code><?php echo $filebase;?></code></li>
<li>Event's music folder is <code><?php echo $track->filepath();?></code></li>
<li>This track's URL is <code><?php echo base_url() . $track->urlpath() . $filebase;?></code></li>
</ul>

<p>Use this information to manually add tracks to the play list.</p>

<p>If the track has been converted, the original track should be here: <code><?php echo base_url() . $track->urlpath() . 'orig/' . $track->filebase('*');;?></code></p>