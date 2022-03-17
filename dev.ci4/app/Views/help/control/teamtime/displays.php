<h3>Displays</h3>
<p>Each display is a physical screen. This can be in the the arena or in the warm-up room. The period each display waits before requesting an update is shown in "ticks". You can add custom css to the styles</p>

<h3>Views</h3>
<p>Each display can be given a separate view. Each view comprises many "frames". These frames alternate between the images stored for this event and the "info" frame. The info frame is entered in the "HTML" box.</p>
<p>The following views are built-in:</p>
<ul><?php 
foreach(glob(VIEWPATH . 'teamtime/displays/info/*.php') as $view) {
	printf('<li>%s</li>', basename($view, '.php'));
}
?></ul>
<p>To use a built-in view, enter the view name above into the HTML box. If the HTML box does not contain the name of a built-in view, the contents of the HTML box will be displayed instead.</p>
