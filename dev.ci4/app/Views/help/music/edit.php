<ol>
<li>All music must be submitted before the music dead-line. Check with the event organiser if you don't know when that is.</li>
<li>Uploaded tracks must have one of the following file-types: <?php echo implode(', ', \App\Libraries\Track::exts_allowed);?>.
<button class="btn btn-outline-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filetype" aria-expanded="false">
<span class="bi bi-eye"></span></button>
<div class="collapse" id="filetype">
<div class="card card-body">
	<p>Uploaded tracks must be playable on <em>our machine</em>. There are many types of music file. MP3 is the easiest format to work with and we prefer all music to be in this format.</p>
	<p>If your music is of an unsupported type, you should convert it to an <code>MP3</code> file on a computer it will play on. There are many free programs that do this, we recommend VLC media player. Use the largest file size setting (320Kbps) when converting the audio file.</p>
	<p>If you really can not do this, you can ask Richard for help.</p>
</div>
</div>
</li>
<li>Only alpha-numeric file-names are allowed. Do not try to upload files punctuation or spaces within the file-name.
<button class="btn btn-outline-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapsefilename" aria-expanded="false">
<span class="bi bi-eye"></span></button>
<div class="collapse" id="collapsefilename">
<div class="card card-body">
	<p>Ensure the files you are uploading have a simple file-name. You should avoid using punctuation marks or spaces within the file-names you use.</p>
	<h5>Example:</h5>
	<p><span class="bi bi-hand-thumbs-up-fill text-success"></span> <code>jane_music.mp3</code><br>
	<span class="bi bi-hand-thumbs-down-fill text-danger"></span> <code>jane's music.mp3</code></p>
</div>
</div>
</li>
<li>Music tracks must be less than <?php echo formatBytes(\App\Libraries\Track::max_filesize);?>.</li>
<li>Ensure all uploaded tracks are not protected, and can be played on any device.
<button class="btn btn-outline-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseprotect" aria-expanded="false">
<span class="bi bi-eye"></span></button>
<div class="collapse" id="collapseprotect">
<div class="card card-body">
	<p>Protected music can only be played on one device (e.g. the computer it was originally downloaded on to). This protection has to be removed to allow us to play your music on our audio system. The protection must be removed using the computer that can play the track.</p>
	<p>If the file is a 'Windows Media Audio' file (.wma) the best way to remove the protection is to convert it to an 'MP3' file on the PC it will play on. There are many free programs that do this, we recommend VLC media player. Use the largest file size setting (320Kbps) when converting the audio file.</p>
	<p>You can tell if a track is protected by looking in its properties. Right click on the file and click on "properties".</p>
</div>
</div>
</li>
<li>Event organisers are responsible for ensuring they have the right to play all music in a public place
<button class="btn btn-outline-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseppl" aria-expanded="false">
<span class="bi bi-eye"></span></button>
<div class="collapse" id="collapseppl">
<div class="card card-body">
	<p>Event organisers are responsible for ensuring they have the right to play music in the venue. In almost all cases, a <abbr title="Phonographic Performance Limited">PPL</abbr> licence and a <abbr title="Performing Right Society">PRS</abbr> licence is required to play music at a gymnastics event.</p>
	<p>If in doubt, event organisers should consult the <a href="http://www.ppluk.com/">PPL Website</a>.</p>
	<p>British Gymnastics holds a "Dubbing Licence", which only covers the <em>replication</em> (e.g. transferring music from CD to website or USB stick). The event organiser will need a PPL/PRS licence to cover the <em>playing</em> of music at the event. This licence will  usually be part of the venue agreement. For full information regarding music licencing, please read <a href="https://www.british-gymnastics.org/clubs/club-membership/music-licensing-ppl-prs">Music Licensing – PPL/PRS "Playing Music In public</a> on the British Gymnastics web-site.</p>
</div>
</li>
</ol>
<p>Once you have uploaded the track, try to play it to ensure it works.</p>
<p>Please ask Richard Keasley at Hawth if you have queries about this service; we will be happy to help you with technical issues.</p>