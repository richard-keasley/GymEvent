<?php $this->extend('default');
 
$this->section('content'); ?>
<p>Please tell Kim (Brighton) if you see a problem on these pages.</p>
<p>Please remember the videos are examples for coaching purposes. They are not to be used as Judging videos.</p>

<?php
$sections = [
'Floor' => [
	['A value skills', '3EGdGS15xBU'],
	['B value skills', 'KeSkZinvZ_w'],
	['C value skills', 'PDiHWijJ81U'],
	['D value skills', 'VWjEM-rV3qo'],
	['E value skills', 'llU-nTF-PkQ'],
	['F value skills', 'mNpFvri43J4'],
	['Routine example 1', 'wg3hmcaQJb0'],
	['Routine example 2', '3PA_MjtwTh0'],
	['Routine example 3', 'pxVnzczM8l8'],
	['Routine example 4', 'eLYzQk01WXc'],
],
'Vault' => [
	['Examples', 'FEyie_FRRT4'],
]
];

foreach($sections as $heading=>$section) { ?>
	<section><?php
	echo "<h4>{$heading}</h4>"; ?>
	<ul class="nav"><?php
	foreach($section as $item) { ?>
		<li class="nav-item m-1 border">
		<button style="min-width:12em" class="btn btn-light text-start" type="button" onclick="getvid('<?php echo $item[1];?>')"><span class="bi-youtube"></span>&nbsp;<?php echo $item[0];?></button>
		</li><?php
	}
	?></ul>
	</section><?php
}

?>
<script>
function getvid(id) {
	$('#playvid')[0].src = 'https://www.youtube.com/embed/' + id + '?autoplay=1';
	$('#playvid').show();
	window.scrollTo(0, document.body.scrollHeight);
}
</script>

<div class="ratio ratio-16x9">
<iframe id="playvid" src="" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
</div>
<div class="py-1"></div>
<?php
$this->endSection(); 