<header><?php 

if($showhelp ?? false) {
	// look for help entry
	$html = model('Htmls')->find_path();
	if($html) {
		$this->setVar('html', $html);
		echo $this->include('html/popup');
	}
}

$text = $heading ?? $title ?? null;
if($text) echo "<h1>{$text}</h1>";
else echo '<div class="py-1"></div>';

include(__DIR__ . '/breadcrumbs.php');
include(__DIR__ . '/messages.php');

?></header>