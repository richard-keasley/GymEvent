<p>The media player should play most formats. Click a track to start playing.</p>
<p>If the browser won't play the track, you can <em>sometimes</em> play the track on your local machine if you download it. For example: the browser will not play Windows Media files (<code>.wma</code>), but any Windows machine will be able to play these tracks if they are downloaded.</p>

<h3>Finding tracks</h3>
<?php
$track = new \App\Libraries\Track;
$track->event_id = 999;
$track->exe = 'FX';
$track->entry_num = 21;

$filebase = $track->filebase('*');
$urlpath = $track->urlpath();
?>
<p>Example for event id <code>999</code>, exercise <code>FX</code>, entry number <code>21</code>.</p>

<p>Music files are stored in <code><?php echo $urlpath;?></code>.</p>
<p>This track is named <code><?php echo $filebase;?></code>.</p>
