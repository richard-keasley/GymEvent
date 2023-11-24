<script>
const BUTTONS = <?php echo json_encode(\App\Libraries\Track::BUTTONS); ?>;
let active_btn = 0;

function playbutton(button) {
	var track_url = button.dataset.url;
	if(!track_url) return false;
	
	// is a button active?
	if(active_btn) {
		active_btn.className = BUTTONS.repeat;
		playtrack.pause();
		if(active_btn.title==button.title) {
			// stopping current track
			active_btn = 0;
			return true;
		}
	}
	
	// play new track
	active_btn = button;
	active_btn.className = BUTTONS.pause;
	playtrack.load(track_url);
	return true;
}
</script>