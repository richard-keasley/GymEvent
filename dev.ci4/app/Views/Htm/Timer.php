<?php namespace App\Views\Htm;

class Timer implements \stringable {
private $id = '';

public function __construct($id='timer') {
	$this->id = $id;
}

public function __toString() {
ob_start(); ?>
<div class="bg-dark text-light text-center fw-bold" style="width:4.7em; line-height:1.8em" id="<?php echo $this->id;?>">0</div>
<script>
function timer(selector) {
	this.el = $('#'+selector);
	this.timer = null;
	
	this.reset = function() {
		if(this.timer) clearInterval(this.timer);
		this.show(0);
	};
	
	this.start = function() {
		var secs = 0;
		var obj = this;
		this.timer = setInterval(function() {
			secs++;
			obj.show(secs);
		}, 1000);
	};
	
	this.show = function(secs) {
		var mins = Math.floor(secs/60); 
		secs = secs % 60;
		mins = mins.toString().length < 2 ? '0' + mins : mins;
		secs = secs.toString().length < 2 ? '0' + secs : secs;
		this.el.text(mins + ':' + secs);
	};
	
	this.reset();
}
</script>






<?php return ob_get_clean();
}

}
