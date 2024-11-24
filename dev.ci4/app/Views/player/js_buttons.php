<script>
const BUTTONS = <?php echo json_encode(\App\Libraries\Track::BUTTONS); ?>;
let active_btn = 0;

function playbutton(button) {
	var track_url = button.dataset.url;
	if(!track_url) return false;
	
	// is a button active?
	if(active_btn) {
		active_btn.className = BUTTONS.repeat;
		if(active_btn.title==button.title) {
			// fade current track
			playtrack.pause(1000);
			active_btn = 0;
			return true;
		}
		else {
			// jumping to new track
			playtrack.pause();
		}
	}
	// play requested track
	playtrack.load(track_url);
		
	// set active button
	active_btn = button;
	active_btn.className = BUTTONS.pause;
	return true;
}
</script>