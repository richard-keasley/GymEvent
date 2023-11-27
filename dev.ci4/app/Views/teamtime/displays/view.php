<?php $this->extend('teamtime/displays/layout');
use \App\Libraries\Teamtime as tt_lib;

$this->section('body'); 
$images = tt_lib::get_images();
?>
<div id="display">
<div id="images">
<?php foreach($images as $key=>$image) {
	printf('<img id="image%u" class="frame" src="%s">', $key, $image);
} ?>
</div>
<div id="info" class="frame"></div>
<div id="msg"></div>
<div id="frametick"></div>
</div>

<script>
const display = <?php echo json_encode($display);?>;
const frames = <?php 
    if($images) {
        $frames = [];
        foreach(array_keys($images) as $key) {
        	$frames[] = "image{$key}";
        	$frames[] = "info"; 
        }
    }
    else {
        $frames = ['info'];
    }
    echo json_encode($frames);
?>;

let view = { info:0, images:0, updated:0 };
let ticknum = 0;
let frame_num = 0; // current frame
let frame_type = '';
let frame_ticks = 0 ; // ticks per frame
let frame_display = null ; // CSS display style of frame
let chk = 0;

// get view from server
get_view();
function get_view() {
	var url = '<?php echo site_url("/api/teamtime/display_view/{$ds_id}/{$ds_updated}");?>/'+view.updated;
	// console.log(url);
	$.get(url, function(response) {
		try {
			var reload = response.reload;
			if(reload) {
				<?php if(ENVIRONMENT == 'development') { ?>
				// console.log(response);
				// console.log(view);
				<?php } ?>
				switch(reload) {
					case 'view':
					view.updated = response.updated;
					view.info = response.view.info;
					view.images = response.view.images;
					$('#info').html(response.view.html);
					break;

					case 'display':
					location.reload()
					break;
				}
			}
			show_msg('');
		}
		catch(errorThrown) {
			show_msg('400: ' + errorThrown);
		}
	})
	.fail(function(jqXHR) {
		show_msg(get_error(jqXHR));
	})
	.always(function() {
		setTimeout(function(){ get_view(); }, display.tick);
	});
}

// get next frame
frame_ticker();
function frame_ticker() {
	chk = 0;
	do { // find next valid frame
		frame_num++;
		if(frame_num>=frames.length) frame_num = 0;
		frame_type = (frame_num % 2) ? 'info' : 'images' ;
		frame_period = parseInt(view[frame_type]);
		chk++; // in case no frames allowed
	}
	while(frame_period==0 && chk<=frames.length)
	// hide inactive frames
	$('#display .frame').each(function() {
		frame_display = this.id==frames[frame_num] ? 'block' : 'none' ;
		this.style.display = frame_display;
	});
	// stop browser timing out due to inactivity
	$('#frametick').html(frame_num+'/'+frame_period);
	// no frame_period if view not yet loaded 
	if(!frame_period) frame_period = 1; 
	setTimeout(function(){ frame_ticker(); }, frame_period * 1000);
};

function show_msg(msg) { 
	if(msg) msg = '<p>'+msg+'</p>'; 
	$('#msg').html(msg); 
}

</script>

<?php $this->endSection();
