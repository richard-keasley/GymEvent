<?php $this->extend('default');

$this->section('content');

$can_edit = \App\Libraries\Auth::check_path('admin/entries/edit');
$formats = ['plain', 'dob'];
$format = $format ?? 'plain' ;
if(!$can_edit) $format = 'plain';
if(!in_array($format, $formats)) $format = 'plain';
# d($format);

if($can_edit) {
	$attr = [
		'class' => "toolbar nav sticky-top"
	];
	echo form_open(current_url(), $attr);
	echo \App\Libraries\View::back_link("admin/events/view/{$event->id}");
	echo getlink("admin/entries/edit/{$event->id}", 'edit');
	
	$attr = ['class'=>"nav-link"];
    foreach($formats as $val) {
	    if($format==$val) continue;
		$label = match($val) {
			'dob' => 'DoB',
			default => ucfirst($val)
		};
	    echo anchor("admin/entries/view/{$event->id}/{$val}", $label, $attr);
	}
	
	echo getlink("admin/entries/categories/{$event->id}", 'categories');
	echo getlink("admin/entries/clubs/{$event->id}", 'clubs');
	echo getlink("admin/entries/import/{$event->id}", 'import');
	echo getlink("admin/entries/export/{$event->id}", 'export');
	?>
 	<input type="hidden" name="renumber" value="0">
	<button class="btn btn-primary" name="chk_renumber" value="1" type="button">Renumber</button>
	<script>
	$('[name=chk_renumber]').click(function(){
		if(!confirm("Re-number all entries for this event.")) return;
		$(this).closest('form').find('[name=renumber]').val(1);
		$(this).closest('form').submit();
	});
	</script>
	<?php 
	echo form_close();
}
?>
<div class="d-flex flex-wrap gap-4">
<?php 
$table = \App\Views\Htm\Table::load('responsive');

$edit_base = site_url("/admin/entries/edit/{$event->id}");
$thead = [];
if($can_edit) $thead[] = 'num';
$thead[] = 'club';
$thead[] = 'name';
if($format=='dob') $thead[] = 'DoB';
# if($can_edit) $thead[] = 'run';

foreach($entries as $dis) { ?>
	<section class="mw-100">
	<h4><?php echo $dis->name;?></h4>
	<?php foreach($dis->cats as $cat) {
		$tbody = []; 
		foreach($cat->entries as $entry) {
			$row = [];
			foreach($thead as $key) {
				switch($key) {
					case 'club':
					$value = $users[$entry->user_id]->abbr ?? '?';
					break;
					
					case 'name':
					$value = $entry->name;
					if($entry->guest) $value .= ' <abbr title="guest">(G)</abbr>';
					break;
					
					case 'DoB':
					$value = strtotime($entry->dob);
					$value = date('d-M-Y', $value);
					break;
					
					case 'run':
					$value = $entry->get_rundata('group');
					break; 
					
					default:
					$value = $entry->$key;
				}
				$row[] = $value;
			}
			$tbody[] = $row;
		}
		
		if($tbody || $can_edit) {
			$heading = $cat->name;
			if($can_edit) {
				$params = [
					'disid' => $dis->id,
					'catid' => $cat->id
				];
				$href = $edit_base . '?' . http_build_query($params);
				$heading = anchor($href, $heading, ['title' => 'Edit category']);
			}
			echo "<h5>{$heading}</h5>";
		}
		
		if($tbody) {
			$table->autoHeading = false;
			echo $table->generate($tbody); 
		}
		elseif($can_edit) {
			echo '<p class="alert alert-warning p-1">Empty category.</p>';
		}
	} ?>
	</section>
<?php } ?>
</div>
<?php
# d($entries);
# d($event);
# d($users);
$this->endSection(); 
