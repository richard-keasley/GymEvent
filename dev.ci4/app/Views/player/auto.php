<?php $this->extend('default'); 

$this->section('content'); ?>
<section id="playtrack" class="alert alert-warning">
<audio style="width:100%" controls></audio> 
<p class="pb-1 px-1 m-0">track&hellip;</p>
<p class="p-1 m-0">looking up channel&hellip;</p>
</section>
<p class="my-3"><strong>Important:</strong> This service will only work if the browser is set to allow "autoplay" from this website. Otherwise <span class="bg-opacity-25 bg-danger">NotAllowedError</span> will be shown.</p>
<?php $this->endSection(); 

$this->section('sidebar'); ?>
<section>
<h6>Channel selector</h6>
<?php 
$nav = [];
foreach($channels as $key=>$val) {
	$nav[] = [ base_url("control/player/auto/{$key}"), $val ];
}
$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();
?>
</section>
<?php $this->endSection();

$this->section('bottom');?>
<script>
const ch_id = <?php echo $ch_id;?>;

$(function() {
var $player = $('#playtrack audio')[0];
var $player_url = $('#playtrack p')[0];

let current_store = {};
let playpromise = null;

$player.addEventListener("error", function(err) {
	let msg = '';
	switch (err.target.error.code) {
		case err.target.error.MEDIA_ERR_ABORTED:
			msg = 'Playback aborted';
			break;
		case err.target.error.MEDIA_ERR_NETWORK:
			msg = 'Network error';
			break;
		case err.target.error.MEDIA_ERR_DECODE:
			msg = 'Decoding error';
			break;
		case err.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED:
			msg = 'Track not loaded';
			break;
		default:
			msg = 'unknown error';
	}
	show_message('Error: ' + msg);
});

if(ch_id) {
	var tt = setInterval(function() {
		var url = '<?php echo base_url("api/music/auto");?>/' + ch_id;
		$.get(url, function(response) {
			var store = JSON.stringify(response);
			if(current_store!=store) {
				try {
					current_store = store;
					$player.pause();
					if(!response.url) throw 'Track not found';
					show_message(response.state, 1);
					$player.src = response.url;
					$('#playtrack p')[0].innerHTML = response.url;
					if(response.state=='play') {
						playpromise = $player.play();
						playpromise.catch((error) => {
							show_message(error);
						});
					}
				}
				catch(err) {
					show_message(err);
				}
			}
		})
		.fail(function(jqXHR) {
			show_message(get_error(jqXHR));
		});
	}, 1000);
}
else {
	show_message('select a channel to start the service');
}
});

function show_message(message, success=0) {
	$('#playtrack p')[1].innerHTML = message;
	$('#playtrack')[0].className = success ? 'alert alert-success' : 'alert alert-danger';
}

</script>
<?php $this->endSection();
