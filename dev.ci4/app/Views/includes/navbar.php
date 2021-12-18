<?php 
$anchors = [];
$attr = ['class'=>'nav-link'];
foreach($nav as $item) {
	if(is_array($item)) {
		$href = trim($item[0], '/');
		$label = $item[1];
	}
	else {
		$href = trim($item, '/');
		$label = ucfirst(basename($href));
	}
	if($href=='home') $href = '/';
	if($href && \App\Libraries\Auth::check_path($href)) {
		$anchors[] = anchor(base_url($href), $label, $attr);
	}
}

if($anchors) { ?>
	<ul class="nav flex-column"><?php 
	foreach($anchors as $anchor) { 
		printf('<li class="nav-item">%s</li>', $anchor);
	} 
	?></ul>
<?php } 
