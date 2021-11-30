<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table compact">'];
$table->setTemplate($template);

$this->section('content');
#d($event);
#d($entries);
#d($evt_users);

$attr = [
	'id' => "control"
];
echo form_open(base_url(uri_string()), $attr); ?>
<input type="hidden" name="method" value="set_check">
<div class="toolbar nav row sticky-top">
	<div class="col-auto"><div class="input-group">	
		<button class="btn btn-warning" type="submit" name="val" value="0">unchecked</button>
		<button class="btn btn-success" type="submit" name="val" value="1">checked</button>
		<button class="btn btn-info" type="submit" name="val" value="2">withdrawn</button>
	</div></div>		

	<div class="col-auto">
		<button class="btn btn-danger" type="button" name="delete">delete</button>
	</div>
</div>
<script>
$(function() {

$('#control [name=delete]').click(function(){
	if(!confirm('Are you sure you want to delete the current entries?')) return;
	$('#control [name=method]').val('delete');
	$('#control').submit();
});

});
</script>

<?php
$count_entries = 0;
$tracks_table = [];
foreach(['missing', 'unchecked', 'ok', 'archived', 'withdrawn'] as $key) $tracks_table[$key] = 0;

$track = new \App\Libraries\Track;
$track->event_id = $event->id;

foreach($entries as $dis) {
	if($filter['dis'] && $filter['dis']!=$dis->id) continue;
	$dis_title = 0;
	foreach($dis->cats as $cat) {
		if($cat->music) {
			if($filter['cat'] && $filter['cat']!=$cat->id) continue;
			$tbody = [];
			$thead = ['num', 'name', 'club'];

			foreach($cat->entries as $key=>$entry) {
				$track->entry_num = $entry->num;
				if($filter['user'] && $filter['user']!=$entry->user_id) continue;
				$tr = [
					$entry->num,
					$entry->name,
					$entry->club()
				];
				$count_entries++;
				foreach($entry->music as $exe=>$check_state) {
					$track->exe = $exe;
					$track->check_state = $check_state;
					$tracks_table[$track->status()] ++;
					$tr[] = $track->view(['checkbox']);
					if(!$key) $thead[] = $track->exe;	
				}
				$tr[] = getlink($entry->url('music'), '<span class="bi bi-pencil"></span>');
				$tbody[] = $tr;
			}
			$thead[] = '' ;		
				
			if($tbody) {
				if(!$dis_title) printf('<h4>%s</h4>', $dis->name);
				$dis_title = 1;
				printf('<h6>%s</h6>', $cat->name);
				$table->setHeading($thead);
				echo $table->generate($tbody);
			}
		}
	}
}
?>
</form>
<?php
$this->endSection();

$this->section('sidebar'); ?>
<section class="pt-5 mt-2 pe-3 sticky-top border-end">
<h5>Summary</h5>
<?php
$tbody = [];
foreach($tracks_table as $status=>$count) {
	if($count) $tbody[] = [$count, $status, 'int'];
}
$tbody[] = [array_sum($tracks_table), 'Total', '*int'];
echo \App\Libraries\View::vartable($tbody);
printf('<p>%s entries.</p>', $count_entries); 
?></section>
<?php $this->endSection();

$this->section('top'); 
$attr = [
	'id' => "frmstate",
	'class' => 'toolbar'
];
echo form_open(base_url(uri_string()), $attr);
echo form_hidden('set_state', 1);
echo \App\Libraries\View::back_link("admin/events/view/{$event->id}");
?>
<div class="btn-group">
	<label class="input-group-text">Set music state for this event</label>
	<?php 
	$colours = \App\Entities\Event::state_colours;
	$input = ['class' => 'btn-check'];
	$input['name'] = 'music';
	foreach(\App\Entities\Event::state_labels as $state=>$state_label) {
		$input['id'] = "music_{$state_label}";
		$input['checked'] = $event->music==$state;
		$input['value'] = $state;
		echo form_radio($input);
		printf('<label class="btn btn-outline-%s" for="%s">%s</label>', $colours[$state], $input['id'], $state_label);
	} 
	?>
</div>
<?php 
echo getlink("admin/entries/categories/{$event->id}", 'categories'); 
echo getlink("player/view/{$event->id}", 'player'); 
?>
<script>
$(function() {
	$('#frmstate [name=music]').click(function(){
		$('#frmstate').submit();
	});
});
</script>
</form>

<form name="selector" method="GET" class="row">
<div class="col-auto"><?php 
	$selector = []; $dis_opts = ['-'];
	$user_opts = [];
	foreach($entries as $dis) {
		foreach($dis->cats as $cat) {
			if($cat->music) {
				$selector[$dis->id][$cat->id] = $cat->name;
				foreach($cat->entries as $entry) {
					$entry->club = $entry->club();
					$user_opts[$entry->user_id] = $entry->club;
				}
			}
		}
		if(!empty($selector[$dis->id])) $dis_opts[$dis->id] = $dis->name;
	}
	echo form_dropdown('dis', $dis_opts, $filter['dis'], 'class="form-control"');
?>
</div>
<div class="col-auto">
	<select class="form-control" name="cat"></select>
</div>
<div class="col-auto"><?php
	echo form_dropdown('user', ['-'] + $evt_users, $filter['user'], 'class="form-control"');
?></div>
<div class="col-auto">
	<button type="submit" class="btn btn-primary">get</button>
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
$dis_sel.change(function() { update_selector(); });
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
</form> 

<?php $this->endSection();
