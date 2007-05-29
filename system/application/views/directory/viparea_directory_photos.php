<div id="RightColumn">
	<h2>What's this?</h2>
	<p><?php echo $main_text; ?></p>
	<h2>Disclaimer</h2>
	<p><?php echo $disclaimer_text; ?></p>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<?php foreach( $images->result() as $image ) { ?>
		<?=$this->image->getThumb($image->photo_id, 'slideshow')?>
		<br />
		<?=anchor(vip_url('directory/photos/move/'.$image->photo_id.'/up'), 'move up')?> |
		<?=anchor(vip_url('directory/photos/move/'.$image->photo_id.'/down'), 'move down')?> |
		<a href="<?php echo vip_url('directory/photos/delete/'.$image->photo_id) ?>" onclick="return confirm('Are you sure you want to delete this photo?');">delete</a>
		<br />
		<?php } ?>
	</div>

	<?php
		$CI = &get_instance();
		$CI->load->view('uploader/upload_single_photo', array('action_url' => vip_url('directory/photos/upload')) );
	?>
</div>

<a href='/viparea/'>Back to the vip area.</a>