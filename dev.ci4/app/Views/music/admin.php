<?php $this->extend('default');

$this->section('content'); 
# d($event);
# d($entries);
# d($users);
# d($filter);

$user_options = [0 => '-'];
foreach($users as $id=>$user) $user_options[$id] = $user->name;

$attrs = ['id' => "player"];
$hidden = ['cmd' => "update"];
echo form_open('', $attrs, $hidden); 
?>
<div class="toolbar nav row sticky-top">
	<div class="input-group">
		<div class="input-group-text">Set track</div>
		<button class="btn btn-warning" type="submit" name="val" value="0" title="unchecked"><i class="px-1 bi-question-square"></i></button>
		<button class="btn btn-success" type="submit" name="val" value="1" title="checked"><i class="px-1 bi-check-square"></i></button>
		<button class="btn btn-info" type="submit" name="val" value="2" title="withdrawn"><i class="px-1 bi-x-square"></i></button>
	</div>
	<?php 
	$options = ['audio' => true];
	echo $this->include('Htm/Playtrack', $options);
	?>	
</div>

<?php

$count_entries = 0;
$tracks_table = [];
foreach(\App\Libraries\Track::state_labels as $key) $tracks_table[$key] = 0;

$track = new \App\Libraries\Track;
$track->event_id = $event->id;

$table = \App\Views\Htm\Table::load('responsive');

foreach($entries as $dis) {
	if($filter['dis'] && $filter['dis']!=$dis->id) continue;
	$dis_title = 0;
	foreach($dis->cats as $cat) {
		if($cat->music) {
			if($filter['cat'] && $filter['cat']!=$cat->id) continue;
			$tbody = [];
			$thead = ['#', 'Club', 'Name'];
			$head_done = false;

			foreach($cat->entries as $entry) {
				$track->entry_num = $entry->num;
				if($filter['user'] && $filter['user']!=$entry->user_id) continue;
				$ent_row = [
					$entry->num,
					$users[$entry->user_id]->abbr ?? '?',
					$entry->name
				];
				
				$show_entry = 0; $ent_states = [];
				foreach($entry->music as $exe=>$check_state) {
					if(!$head_done) $thead[] = $exe;	
						
					$track->exe = $exe;
					$track->check_state = $check_state;
					$ent_row[] = $track->playbtn(['checkbox']);
					$status = $track->status();
					$ent_states[] = $status;
					if(!$filter['status'] || $filter['status']==$status) $show_entry = 1;
				}
				$head_done = true;
				
				if($show_entry) {
					$count_entries++;
					foreach($ent_states as $status) $tracks_table[$status] ++;
					$ent_row[] = getlink($entry->url('music'), '<span class="bi bi-pencil"></span>');
					$tbody[] = $ent_row;
				}
			}
				
			if($tbody) {
				if(!$dis_title) printf('<h4>%s</h4>', $dis->name);
				$dis_title = 1;
				printf('<h6>%s</h6>', $cat->name);
				$thead[] = '' ;	// edit column
				$table->setHeading($thead);
				echo $table->generate($tbody);
			}
		}
	}
}
echo form_close();

$this->endSection();

$this->section('sidebar'); ?>
<section class="pt-5 mt-2 pe-3 sticky-top border-end">
<h5>Summary</h5>
<?php
$vartable = new \App\Views\Htm\Vartable;
foreach($tracks_table as $status=>$count) {
	if($count) $vartable->items[$status] = \App\Views\Htm\Table::number($count);
}
$vartable->footer = [\App\Views\Htm\Table::number(array_sum($tracks_table)), 'Total'];
echo $vartable->htm();

?></section>
<?php $this->endSection();

$this->section('top'); ?>
<div class="toolbar flex-wrap">
<?php echo \App\Libraries\View::back_link("admin/events/view/{$event->id}"); ?>
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#state_modeal">Music state</button>
<?php 
echo $event->link('player');
# echo getlink("control/player/view/{$event->id}", 'player'); 
echo getlink("admin/music/clubs/{$event->id}", 'clubs'); 
?>
</div>

<ul class="list-unstyled"><?php
$now = new \datetime;
$dates = $event->dates;
$dates['event'] = $event->date;
asort($dates);
foreach($dates as $key=>$val) {
	if(strpos($key, 'clubrets_')===0) continue;
	if(!$val) continue; 
	
	$date = new \datetime($val);			
	$format = $date < $now ?
		'<li><em>%s: %s</em></li>' : 
		'<li>%s: %s</li>';
	printf($format, humanize($key), $date->format('j F Y'));
}
?></ul>

<getform name="selector" class="row">
<div class="col-auto"><?php 
	$selector = []; $dis_opts = [];
	foreach($entries as $dis) {
		foreach($dis->cats as $cat) {
			if($cat->music) {
				$selector[$dis->id][$cat->id] = $cat->name;
			}
		}
		if(!empty($selector[$dis->id])) $dis_opts[$dis->id] = $dis->name;
	}
	
	if(count($dis_opts)>1) { 
		$dis_opts = ['-'] + $dis_opts;
	}
	echo form_dropdown('dis', $dis_opts, $filter['dis'], 'class="form-control"');
?>
</div>
<div class="col-auto">
	<select class="form-control" name="cat"></select>
</div>
<div class="col-auto"><?php
	echo form_dropdown('user', $user_options, $filter['user'], 'class="form-control"');
?></div>
<div class="col-auto"><?php
	$status_options = ['-'];
	foreach($tracks_table as $status=>$count) {
		$status_options[$status] = $status;
	}
	echo form_dropdown('status', $status_options, $filter['status'], 'class="form-control"');
?></div>
<div class="col-auto">
	<button type="submit" class="btn btn-primary">get</button>
</div>
</getform>

<?php $this->endSection();

$this->section('bottom'); ?>
<div class="modal fade" id="state_modeal" tabindex="-1">
<div class="modal-dialog">
<?php
// same action as above
$attrs = [
	'id' => "frmstate",
	'class' => 'modal-content'
];
$hidden = ['set_state' => "1"];
echo form_open('', $attrs, $hidden);
?>
<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">Event music state</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<p>Set music state for this event.</p>
<div class="input-group">
<span class="input-group-text">state</span>
<?php 
$colours = \App\Entities\Event::state_colours;
$input = ['class' => 'btn-check'];
$input['name'] = 'music';
foreach(\App\Entities\Event::states as $state_label=>$state) {
	$input['id'] = "music_{$state_label}";
	$input['checked'] = $event->music==$state;
	$input['value'] = $state;
	echo form_radio($input);
	
	$label = [
		'class' => "btn btn-outline-{$colours[$state]}",
		'for' => $input['id']
	];
	printf('<label %s>%s</label>', stringify_attributes($label), $state_label);
} 
?>
</div>
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
<?php echo form_close();?>
</div>
</div>

<script>
const selector = <?php echo json_encode($selector);?>;
const filter = <?php echo json_encode($filter);?>;
let $dis_sel = null;
let $cat_sel = null;
let dis_id = 0;
let selected = '';

$(function() {
	$dis_sel = $('[name=dis]');
	$cat_sel = $('[name=cat]');
	update_selector();

	$('#frmstate [name=music]').click(function(){
		$('#frmstate').submit();
	});
	$dis_sel.change(function() {
		update_selector(); 
	});
});

function update_selector(dis_id) {
	dis_id = $dis_sel.val()
	$cat_sel.find('option').remove();
	$cat_sel.append('<option value="0">-</option>');
	$.each(selector[dis_id], function(value, text) {
		selected = value==filter.cat ? 'selected="selected"' : '' ;
        $cat_sel.append('<option value="'+value+'" '+selected+'>'+text+'</option>');
	});
	if(dis_id=='0') $cat_sel.hide();
	else $cat_sel.show();
}
</script>

<?php 
$this->endSection();
