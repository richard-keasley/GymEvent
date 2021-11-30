<?php $this->extend('memdb/default');

$this->section('content'); ?>
<section class="bg-light my-2">
<p><?php printf('<a href="mailto:%1$s">%1$s</a>', $postvar['email']);?></p>
</section>

<section class="bg-light my-2">
<p>This is invitation for <?php printf('%s %s', $postvar['name1'], $postvar['name2']);?> to join Hawth Gymnastics.</p>
<p>Please complete the form detailed here<br>
<a style="background:#009; padding:.2em 1em; text-decoration:none; border-radius:.5em;color:#fff" href="<?php echo base_url("memdb/enrol/{$key}");?>">join us</a>.</p>
<p>This link is personal to you. Do not share it with others; you risk giving your place to someone else.</p>
<p><?php echo $postvar['name1'];?>'s session is <?php echo $groups[$postvar['group']];?></p> 
<p>We look forward to welcoming you into the club.</p>
<p>The Hawth team</p>
</section>

<div class="toolbar">
	<a href="<?php echo base_url('memdb/invite');?>" class="btn btn-outline-secondary" title="close"><span class="bi bi-box-arrow-left"></span></a>
</div>

<?php $this->endSection();

