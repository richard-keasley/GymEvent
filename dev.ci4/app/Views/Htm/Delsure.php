<?php namespace App\Views\Htm;

class Delsure {
	
static $count=0;

private $attrs = [
	'title' => 'Delete item',
	'message' => '<p>Delete this item?</p>',
	'icon' => 'trash',
	'button' => 'danger'
];
	
function __construct($attrs=[]) {
	foreach(array_keys($this->attrs) as $key) {
		if(isset($attrs[$key])) $this->attrs[$key] = $attrs[$key];
	}
	
	$this->attrs['id'] = 'delsure' . self::$count++;
	
	$requested = filter_input(INPUT_POST, 'cmd')==$this->attrs['id'];
	$this->attrs['request'] = $requested ?
		filter_input(INPUT_POST, 'del_id') : 
		null ;
		
	$this->attrs['button'] = "btn btn-{$this->attrs['button']}";
	$this->attrs['icon'] = sprintf('<span class="bi bi-%s"></span>', $this->attrs['icon']);
}

function __get($key) {
	return $this->attrs[$key] ?? null ;
}

function button($del_id, $title=null) {
	$attrs = [
		'type' => "button",
		'class' => $this->button,
		'title' => $title ? $title : $this->title,
		'data-id' => $del_id,
		'data-bs-toggle' => "modal",
		'data-bs-target' => "#{$this->id}",
	];
	return sprintf('<button %s>%s</button>',
		\stringify_attributes($attrs),
		$this->icon
	);
}

function form() {
ob_start();

$attrs = [
	'class' => "modal",
	'tabindex' => "-1",
	'id' => $this->id,
];
$hidden = [
	'cmd' => $this->id,
	'del_id' => '',
];
echo form_open('', $attrs, $hidden);
?>
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title"><?php echo $this->title;?></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<?php echo $this->message;?>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary bi-x-circle-fill" data-bs-dismiss="modal" title="cancel"></button>
	<button type="submit" class="<?php echo $this->button;?>" title="<?php echo $this->title;?>"><?php echo $this->icon;?></button>
</div>
</div>
</div>
<script>
document.getElementById('<?php echo $this->id;?>').addEventListener('show.bs.modal', event => {
	var value = event.relatedTarget.getAttribute('data-id');
	$('<?php echo "#{$this->id} input[name=del_id]";?>').val(value);
	var value = event.relatedTarget.getAttribute('title');
	if(!value) value = '<?php echo addslashes($this->title);?>';
	$('<?php echo "#{$this->id} .modal-header .modal-title";?>').html(value);

})
</script>

<?php 
echo form_close();
return ob_get_clean();
}

}
